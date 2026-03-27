public function store(Request $request)
{
    $order = Order::findOrFail($request->order_id);
    
    // Check 7-day policy
    if ($order->delivered_at && now()->diffInDays($order->delivered_at) > 7) {
        return back()->with('error', 'Return policy expired (7 days limit).');
    }

    ReturnItem::create([
        'order_id' => $order->id,
        'type' => $request->type, // return or replacement
        'reason' => $request->reason,
        'requested_date' => now()
    ]);
}