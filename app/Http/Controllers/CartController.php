<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /cart
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT — your cart totals calculation was correct
    public function index()
    {
        $cart     = session()->get('cart', []);
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = $subtotal > 0 ? ($subtotal >= 75 ? 0 : 7.99) : 0;
        $tax      = $subtotal * 0.08;
        $total    = $subtotal + $shipping + $tax;

        return view('cart.index', compact('cart', 'subtotal', 'shipping', 'tax', 'total'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /cart/add
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: was returning back() — causes full page reload.
    //    Now returns JSON so JavaScript can update the cart badge
    //    and show a toast without reloading the page.
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'sometimes|integer|min:1|max:99',
        ]);

        $product  = Product::findOrFail($request->product_id);

        // Don't let user add more than what's in stock
        if (! $product->inStock()) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, this product is out of stock.',
            ], 422);
        }

        $cart = session()->get('cart', []);
        $id   = $product->id;
        $qty  = $request->quantity ?? 1;

        if (isset($cart[$id])) {
            // ✅ KEPT — increment if already in cart
            $cart[$id]['quantity'] += $qty;
        } else {
            // ✅ KEPT — your cart item structure was good, kept it exactly
            $cart[$id] = [
                'product_id'   => $product->id,
                'name'         => $product->name,
                'product_code' => $product->product_code,
                'quantity'     => $qty,
                'price'        => $product->price,
                'image'        => $product->getImage(),
                'size'         => $request->size  ?? 'N/A',
                'color'        => $request->color ?? 'N/A',
            ];
        }

        session()->put('cart', $cart);
        session()->put('cart_count', count($cart));

        // ✅ FIX: return JSON — JavaScript reads this and:
        //    1. Updates the cart item count badge in the navbar
        //    2. Shows a success toast notification
        //    No page reload needed.
        return response()->json([
            'success'    => true,
            'message'    => $product->name . ' added to cart!',
            'cart_count' => count($cart),
            'cart_total' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUT /cart/update/{id}
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: was returning back() and reading from request() global helper.
    //    Now properly typed with Request $request and returns JSON.
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $cart = session()->get('cart', []);

        if (! isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.',
            ], 404);
        }

        $cart[$id]['quantity'] = $request->quantity;
        session()->put('cart', $cart);

        // Recalculate totals to send back to JavaScript
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = $subtotal >= 75 ? 0 : 7.99;
        $tax      = $subtotal * 0.08;
        $total    = $subtotal + $shipping + $tax;

        return response()->json([
            'success'       => true,
            'message'       => 'Cart updated.',
            'cart_count'    => count($cart),
            'line_total'    => $cart[$id]['price'] * $cart[$id]['quantity'],
            'subtotal'      => $subtotal,
            'shipping'      => $shipping,
            'tax'           => $tax,
            'total'         => $total,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE /cart/remove/{id}
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: was returning back(). Now returns JSON so JavaScript
    //    can remove the cart row from the DOM without a page reload.
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (! isset($cart[$id])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart.',
            ], 404);
        }

        $itemName = $cart[$id]['name'];
        unset($cart[$id]);
        session()->put('cart', $cart);
        session()->put('cart_count', count($cart));

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $shipping = $subtotal > 0 ? ($subtotal >= 75 ? 0 : 7.99) : 0;
        $tax      = $subtotal * 0.08;
        $total    = $subtotal + $shipping + $tax;

        return response()->json([
            'success'    => true,
            'message'    => $itemName . ' removed from cart.',
            'cart_count' => count($cart),
            'cart_empty' => count($cart) === 0,
            'subtotal'   => $subtotal,
            'shipping'   => $shipping,
            'tax'        => $tax,
            'total'      => $total,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /cart/count   (AJAX — navbar badge refresh)
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ NEW — useful for keeping the navbar cart badge accurate
    //    after the page loads (e.g. if user opens a new tab)
    public function count()
    {
        return response()->json([
            'cart_count' => count(session()->get('cart', [])),
        ]);
    }
}