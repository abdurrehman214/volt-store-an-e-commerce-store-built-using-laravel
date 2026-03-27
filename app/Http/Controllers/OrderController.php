<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /checkout
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: This method was completely missing from your project.
    //    The route Route::get('/checkout', ...) pointed here
    //    but the method didn't exist — instant 500 error when
    //    any logged-in user tried to visit /checkout.
    public function create()
    {
        $cart = session()->get('cart', []);

        // Redirect to cart if they try to access checkout with empty cart
        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('info', 'Your cart is empty. Add some products first!');
        }

        // Recalculate totals fresh from session (don't trust JS values)
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = $subtotal >= 75 ? 0 : 7.99;
        $tax      = $subtotal * 0.08;
        $total    = $subtotal + $shipping + $tax;

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'tax', 'total'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /checkout
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT — your DB transaction logic here was genuinely solid.
    //    Upgraded: reads from session instead of hidden form fields
    //    (much safer — user can't tamper with prices via the form).
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:credit_card,cheque,vpp,dd',
            'delivery_type'  => 'required|in:1,2,3',   // 1=standard, 2=express, 3=collection
            'full_name'      => 'required|string|max:255',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
        ]);

        // Read cart from SESSION not from form — prevents price tampering
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            // Calculate total from server-side session data
            // ✅ KEPT your approach — loop through cart items from session
            $totalAmount = 0;
            $shipping    = 0;

            foreach ($cart as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);

                // Check stock is still available at time of checkout
                if ($product->stock_quantity < $item['quantity']) {
                    DB::rollBack();
                    return back()->with('error',
                        $product->name . ' only has ' . $product->stock_quantity . ' left in stock.'
                    );
                }

                $totalAmount += $product->price * $item['quantity'];
            }

            // Add shipping (free over $75)
            $shipping     = $totalAmount >= 75 ? 0 : 7.99;
            $tax          = $totalAmount * 0.08;
            $totalAmount  = $totalAmount + $shipping + $tax;

            // Create the Order
            // ✅ KEPT your structure exactly
            $order = Order::create([
                'user_id'            => auth()->id(),
                'total_amount'       => $totalAmount,
                'status'             => 'pending',
                'delivery_type_code' => $request->delivery_type,
            ]);

            // Create Order Items
            // ✅ KEPT your loop — price_at_purchase locks in today's price
            foreach ($cart as $item) {
                $product = \App\Models\Product::find($item['product_id']);

                OrderItem::create([
                    'order_id'          => $order->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'price_at_purchase' => $product->price,
                ]);
            }

            // Generate the 16-digit order number after items are saved
            // ✅ KEPT — calls your Order model method
            $order->generateOrderNumber();

            // Create Payment record
            Payment::create([
                'order_id'         => $order->id,
                'method'           => $request->payment_method,
                'clearance_status' => 'pending',
            ]);

            // Clear the cart session after successful order
            session()->forget('cart');
            session()->forget('cart_count');

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order placed! Your order number is: ' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Order failed. Please try again.']);
            // Note: Don't expose $e->getMessage() to users in production
            // Log it instead:  \Log::error($e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /order/{id}
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT your ownership check — users can only see their own orders
    public function show($id)
    {
        $order = Order::with([
            'orderItems.product',
            'payment',
            'delivery',
        ])->findOrFail($id);

        // Security: customer can only see their own orders
        // ✅ KEPT — upgraded to use the isAdmin() helper from User model
        if (! auth()->user()->isAdmin() && $order->user_id !== auth()->id()) {
            abort(403, 'You do not have permission to view this order.');
        }

        return view('orders.show', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /my-orders
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT — was correct
    public function myOrders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with(['orderItems.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /order/{id}/cancel
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: was missing from OrderController — the route existed in web.php
    //    but the method didn't exist here, only in AdminController.
    //    Customers need their own cancel method that checks ownership first.
    public function cancel($id)
    {
        $order = Order::with(['orderItems.product', 'payment'])->findOrFail($id);

        // Only the order owner can cancel it
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Use the helper from Order model
        if (! $order->isCancellable()) {
            return back()->with('error', 'This order can no longer be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        // Refund if payment was already cleared
        if ($order->payment && $order->payment->isCleared()) {
            $order->payment->update([
                'clearance_status' => 'refunded',
                'cleared_at'       => null,
            ]);
        }

        return redirect()->route('orders.my')
            ->with('success', 'Order #' . $order->order_number . ' has been cancelled.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /order/{id}/return
    // ─────────────────────────────────────────────────────────────────────────
    public function createReturn($id)
    {
        $order = Order::with(['orderItems.product', 'delivery'])->findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Can only return a delivered order
        if (! $order->delivery || ! $order->delivery->isDelivered()) {
            return back()->with('error', 'You can only return delivered orders.');
        }

        // Check 7-day return window
        if ($order->delivery->daysSinceDelivery() > 7) {
            return back()->with('error', 'The 7-day return window has expired for this order.');
        }

        return view('orders.return', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /order/{id}/return
    // ─────────────────────────────────────────────────────────────────────────
    public function storeReturn(Request $request, $id)
    {
        $request->validate([
            'type'   => 'required|in:return,replacement',
            'reason' => 'required|string|min:10|max:500',
        ]);

        $order = Order::with('delivery')->findOrFail($id);

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Double-check policy server-side (never trust only front-end)
        if (! $order->delivery || $order->delivery->daysSinceDelivery() > 7) {
            return back()->with('error', 'Return window has expired.');
        }

        ReturnItem::create([
            'order_id'       => $order->id,
            'type'           => $request->type,
            'reason'         => $request->reason,
            'status'         => 'requested',
            'requested_date' => now()->toDateString(),
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Return request submitted. We will review it shortly.');
    }
}