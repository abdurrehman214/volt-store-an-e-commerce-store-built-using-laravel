<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Feedback;

class ProductController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /   and   GET /products
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT all your filter and sort logic — it was well written.
    //    Fixed one bug: the search query used ->orWhere() without wrapping
    //    in a group, which made category/price filters combine incorrectly
    //    with the search (SQL OR leaked out of the search group).
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Search — ✅ FIX: wrapped in a where() group so it doesn't
        // break other filters with a stray OR clause
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // Filter by category
        // ✅ KEPT your array handling
        if ($request->filled('category')) {
            $categories = is_array($request->category)
                ? $request->category
                : [$request->category];
            $query->whereIn('category', $categories);
        }

        // Price range
        // ✅ KEPT
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        // ✅ KEPT your switch — clean approach
        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        // Home page vs products listing
        // ✅ KEPT your routeIs() check — clever approach
        if ($request->routeIs('home')) {
            $products  = $query->take(8)->get();
            $pageTitle = 'MyStore — Quality Products';
            $view      = 'home';

            // Real testimonials from feedbacks table
            // Falls back to static ones if no reviews yet
            $testimonials = Feedback::with(['user', 'order.orderItems.product'])
                ->where('is_visible', true)
                ->where('rating', '>=', 4)
                ->latest()
                ->take(3)
                ->get();

            // If no real reviews yet, use static placeholders
            if ($testimonials->isEmpty()) {
                $testimonials = $this->staticTestimonials();
            }

            return view($view, compact('products', 'pageTitle', 'testimonials'));
        }

        // All categories for the filter sidebar
        $categories = Product::where('is_active', true)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $products  = $query->paginate(12)->withQueryString();
        $pageTitle = 'Shop — MyStore';
        $view      = 'products.index';

        return view($view, compact('products', 'pageTitle', 'categories'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /product/{id}
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT your related products logic.
    //    Upgraded: loads real Feedback reviews instead of mock array.
    public function show($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);

        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Real reviews from the feedbacks table
        $reviews = Feedback::with('user')
            ->where('order_id', function ($q) use ($product) {
                // Reviews for any order that contained this product
                $q->select('order_id')
                  ->from('order_items')
                  ->where('product_id', $product->id);
            })
            ->where('is_visible', true)
            ->latest()
            ->take(10)
            ->get();

        $avgRating = $reviews->avg('rating') ?? 0;

        return view('products.show', compact('product', 'relatedProducts', 'reviews', 'avgRating'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /search
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ KEPT — simple redirect helper, correct approach
    public function search(Request $request)
    {
        return redirect()->route('products.index', [
            'search' => $request->get('search'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private: static testimonials fallback
    // ─────────────────────────────────────────────────────────────────────────
    // ✅ FIX: moved static testimonials out of index() into a private method
    //    so index() is not 80 lines of inline arrays.
    //    These only show when there are no real reviews in the DB yet.
    private function staticTestimonials(): array
    {
        return [
            (object)[
                'comment' => 'Amazing quality and fast delivery. Exactly what I was looking for.',
                'rating'  => 5,
                'user'    => (object)['name' => 'Marcus D.'],
            ],
            (object)[
                'comment' => 'Great value for money. Will definitely order again.',
                'rating'  => 5,
                'user'    => (object)['name' => 'Leila R.'],
            ],
            (object)[
                'comment' => 'The product matched the description perfectly. Happy customer!',
                'rating'  => 4,
                'user'    => (object)['name' => 'Tyson M.'],
            ],
        ];
    }
}