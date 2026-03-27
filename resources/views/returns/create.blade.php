@extends('layouts.app')

@section('title', 'Request Return - Order #' . $order->order_number)

@section('content')
<div class="container">
    <h1>Request Return/Replacement</h1>
    
    <div class="return-info-card">
        <h3>Order Information</h3>
        <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
        <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
        <p><strong>Delivery Date:</strong> {{ $order->delivery->delivered_at?->format('M d, Y') ?? 'N/A' }}</p>
        
        @php
            $daysSinceDelivery = $order->delivery->delivered_at ? now()->diffInDays($order->delivery->delivered_at) : 999;
        @endphp
        
        @if($daysSinceDelivery <= 7)
            <p class="text-success">✓ Within 7-day return policy ({{ 7 - $daysSinceDelivery }} days remaining)</p>
        @else
            <p class="text-danger">✗ Return period expired ({{ $daysSinceDelivery - 7 }} days over limit)</p>
        @endif
    </div>
    
    <form action="{{ route('orders.return.store') }}" method="POST">
        @csrf
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        
        <div class="form-group">
            <label>Return Type *</label>
            <div class="radio-group">
                <label class="radio-option">
                    <input type="radio" name="type" value="return" checked>
                    <div>
                        <strong>Return</strong>
                        <p>Get refund to original payment method</p>
                    </div>
                </label>
                <label class="radio-option">
                    <input type="radio" name="type" value="replacement">
                    <div>
                        <strong>Replacement</strong>
                        <p>Get same product replaced</p>
                    </div>
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <label for="reason">Reason for Return *</label>
            <textarea name="reason" id="reason" rows="4" required 
                      placeholder="Please describe why you want to return this item...">{{ old('reason') }}</textarea>
            @error('reason')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label>Items to Return *</label>
            @foreach($order->orderItems as $item)
                <label class="checkbox-option">
                    <input type="checkbox" name="items[]" value="{{ $item->id }}">
                    <div>
                        <img src="{{ $item->product->image_url ?? 'https://images.unsplash.com/photo-1608256246200-53e635b5b65f?w=100&q=70' }}" 
                             alt="{{ $item->product->name }}">
                        <span>{{ $item->product->name }} (ID: {{ $item->product->product_code }})</span>
                        <span>Qty: {{ $item->quantity }}</span>
                    </div>
                </label>
            @endforeach
            @error('items')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="form-group">
            <label>Upload Images (Optional)</label>
            <input type="file" name="images[]" multiple accept="image/*">
            <small>Upload photos of the product if there's damage or defect</small>
        </div>
        
        <div class="return-policy-notice">
            <h4>7-Day Return Policy</h4>
            <ul>
                <li>✓ Items must be unworn and in original packaging</li>
                <li>✓ Warranty cards must be included for applicable products</li>
                <li>✓ Return shipping is free for defective items</li>
                <li>✓ Refund processed within 5-7 business days after receipt</li>
                <li>✓ Replacement shipped within 2-3 business days after approval</li>
            </ul>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg">Submit Return Request</button>
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .return-info-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin: 30px 0;
    }
    .radio-group, .checkbox-option {
        display: grid;
        gap: 15px;
        margin: 15px 0;
    }
    .radio-option, .checkbox-option {
        display: flex;
        gap: 15px;
        padding: 15px;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        cursor: pointer;
    }
    .radio-option:hover, .checkbox-option:hover {
        border-color: #007bff;
    }
    .return-policy-notice {
        background: #e7f3ff;
        padding: 20px;
        border-radius: 8px;
        margin: 30px 0;
    }
    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
</style>
@endpush