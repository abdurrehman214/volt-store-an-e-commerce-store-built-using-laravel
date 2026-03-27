<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\ReturnItem;
use App\Models\Feedback;
use App\Models\User;

class AdminController extends Controller
{
    // ✅ KEPT your __construct middleware approach — clean and works well
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->check() || ! auth()->user()->isStaff()) {
                abort(403, 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Dashboard
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT all your stats queries — they were correct
    public function dashboard()
    {
        $stats = [
            'total_orders'      => Order::count(),
            'orders_today'      => Order::whereDate('created_at', today())->count(),
            'pending_payments'  => Payment::where('clearance_status', 'pending')
                                    ->where('method', '!=', 'vpp')
                                    ->count(),
            'ready_to_dispatch' => Order::where('status', 'cleared')->count(),
            'total_revenue'     => Order::where('status', 'completed')->sum('total_amount'),
        ];

        $recentOrders = Order::with(['user', 'payment', 'orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $lowStockProducts = Product::where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->take(5)
            ->get();

        $returnRequests = ReturnItem::with(['order.user'])
            ->where('status', 'requested')
            ->orderBy('requested_date', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'lowStockProducts',
            'returnRequests'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Orders
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT your search + filter queries — well written
    public function orders(Request $request)
    {
        $query = Order::with(['user', 'payment', 'orderItems.product']);

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('clearance_status', $request->payment_status);
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    // ✅ KEPT — was correct
    public function showOrder($id)
    {
        $order = Order::with([
            'user',
            'payment',
            'delivery',
            'orderItems.product',
            'returns',
        ])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    // ✅ KEPT your VPP check logic — correct business rule
    public function clearPayment($id)
    {
        $order = Order::with('payment')->findOrFail($id);

        if (! $order->payment) {
            return back()->with('error', 'No payment record found for this order.');
        }

        if (! $order->payment->requiresClearance()) {
            return back()->with('info', 'VPP (Cash on Delivery) orders do not require pre-clearance.');
        }

        if ($order->payment->isCleared()) {
            return back()->with('info', 'Payment is already cleared.');
        }

        $order->payment->update([
            'clearance_status' => 'cleared',
            'cleared_at'       => now(),
        ]);

        if ($order->status === 'pending') {
            $order->update(['status' => 'cleared']);
        }

        return back()->with('success', 'Payment cleared for Order #' . $order->order_number);
    }

    // ✅ KEPT your dispatch logic — upgraded to use model helper isDispatchable()
    public function dispatchOrder($id)
    {
        $order = Order::with(['payment', 'delivery', 'orderItems.product'])->findOrFail($id);

        if (! $order->payment) {
            return back()->with('error', 'Cannot dispatch — no payment record found.');
        }

        if (! $order->isDispatchable()) {
            return back()->with('error', 'Payment must be cleared before dispatch.');
        }

        if (in_array($order->status, ['dispatched', 'completed'])) {
            return back()->with('error', 'Order is already dispatched.');
        }

        $order->update([
            'status'        => 'dispatched',
            'dispatched_at' => now(),
        ]);

        // ✅ KEPT your updateOrCreate approach
        Delivery::updateOrCreate(
            ['order_id' => $order->id],
            [
                'status'           => 'shipped',
                'tracking_number'  => 'TRK' . strtoupper(uniqid()),
                'carrier'          => 'Standard Courier',
            ]
        );

        // ✅ KEPT inventory reduction
        foreach ($order->orderItems as $item) {
            $item->product->decrement('stock_quantity', $item->quantity);
        }

        return back()->with('success', 'Order #' . $order->order_number . ' dispatched successfully.');
    }

    // ✅ KEPT your cancel logic — upgraded to use model helpers
    public function cancelOrder($id)
    {
        $order = Order::with(['orderItems.product', 'payment'])->findOrFail($id);

        if (! $order->isCancellable()) {
            return back()->with('error', 'Order cannot be cancelled at this stage.');
        }

        // Restore inventory
        foreach ($order->orderItems as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);

        if ($order->payment && $order->payment->isCleared()) {
            $order->payment->update([
                'clearance_status' => 'refunded',
                'cleared_at'       => null,
            ]);
        }

        return back()->with('success', 'Order #' . $order->order_number . ' cancelled.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Products
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT
    public function products()
    {
        $products = Product::orderBy('name')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    // ✅ FIX: was missing — route for GET /admin/products/create existed
    //    but createProduct() method didn't exist
    public function createProduct()
    {
        return view('admin.products.create');
    }

    // ✅ FIX: was missing — POST handler for new product creation
    //    Includes image upload handling
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'required|string',
            'price'               => 'required|numeric|min:0',
            'stock_quantity'      => 'required|integer|min:0',
            'category'            => 'required|string|max:100',
            'warranty_applicable' => 'sometimes|boolean',
            'is_active'           => 'sometimes|boolean',
            'image'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path     = $request->file('image')->store('products', 'public');
            $imageUrl = Storage::url($path);
        }

        Product::create([
            'name'                => $request->name,
            'description'         => $request->description,
            'price'               => $request->price,
            'stock_quantity'      => $request->stock_quantity,
            'category'            => $request->category,
            'warranty_applicable' => $request->boolean('warranty_applicable'),
            'is_active'           => $request->boolean('is_active', true),
            'image_url'           => $imageUrl,
            // product_code is auto-generated by Product model boot()
        ]);

        return redirect()->route('admin.products')
            ->with('success', 'Product created successfully.');
    }

    // ✅ KEPT
    public function editProduct($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    // ✅ KEPT your validation — added image upload handling
    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'required|string',
            'price'               => 'required|numeric|min:0',
            'stock_quantity'      => 'required|integer|min:0',
            'category'            => 'required|string|max:100',
            'warranty_applicable' => 'sometimes|boolean',
            'is_active'           => 'sometimes|boolean',
            'image'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);

        $data = $request->only([
            'name', 'description', 'price',
            'stock_quantity', 'category',
        ]);

        $data['warranty_applicable'] = $request->boolean('warranty_applicable');
        $data['is_active']           = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $path              = $request->file('image')->store('products', 'public');
            $data['image_url'] = Storage::url($path);
        }

        $product->update($data);

        return back()->with('success', 'Product updated successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Stock
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT
    public function stock()
    {
        $lowStock   = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20, ['*'], 'low_page');

        $outOfStock = Product::where('stock_quantity', 0)
            ->paginate(20, ['*'], 'out_page');

        return view('admin.stock', compact('lowStock', 'outOfStock'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Returns
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT
    public function returns()
    {
        $returns = ReturnItem::with(['order.user', 'order.orderItems.product'])
            ->orderBy('requested_date', 'desc')
            ->paginate(20);

        return view('admin.returns.index', compact('returns'));
    }

    // ✅ FIX: replaced the messy data_get() inline logic with the
    //    ReturnItem::isWithinPolicy() model method we wrote in Step 2
    public function showReturn($id)
    {
        $return = ReturnItem::with([
            'order.user',
            'order.orderItems.product',
            'order.payment',
            'order.delivery',
        ])->findOrFail($id);

        $isWithinPolicy    = $return->isWithinPolicy();
        $daysSinceDelivery = $return->order?->delivery?->daysSinceDelivery() ?? 0;

        return view('admin.returns.show', compact('return', 'isWithinPolicy', 'daysSinceDelivery'));
    }

    // ✅ KEPT your approve/reject logic
    public function processReturn(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $return = ReturnItem::findOrFail($id);
        $action = $request->action;

        $return->update([
            'status' => $action === 'approve' ? 'approved' : 'rejected',
        ]);

        return back()->with('success', 'Return request has been ' . $action . 'd.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Feedback
    // ─────────────────────────────────────────────────────────────────────────
    public function feedback()
    {
        $feedbacks = Feedback::with(['user', 'order'])
            ->latest()
            ->paginate(20);

        return view('admin.feedback', compact('feedbacks'));
    }

    // Toggle feedback visibility (hide/show a review)
    public function toggleFeedback($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update(['is_visible' => ! $feedback->is_visible]);

        return back()->with('success', 'Review visibility updated.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Employees  (admin only — not employee)
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: replaced repeated if(auth()->user()->role !== 'admin') abort(403)
    //    with the isAdmin() helper from User model — cleaner
    public function employees()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $employees = User::where('role', 'employee')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.employees.index', compact('employees'));
    }

    public function createEmployee()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('admin.employees.create');
    }

    // ✅ KEPT your validation and Hash::make
    public function storeEmployee(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'employee',
        ]);

        return redirect()->route('admin.employees')
            ->with('success', 'Employee account created successfully.');
    }

    public function destroyEmployee($id)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $employee = User::where('role', 'employee')->findOrFail($id);
        $employee->delete();

        return redirect()->route('admin.employees')
            ->with('success', 'Employee account removed.');
    }
}