@extends('layouts.app')

@section('title', 'Manage Orders - Admin')

@section('content')
<div class="container">
    <div class="admin-header">
        <h1>Manage Orders</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
    
    <!-- Filters -->
    <div class="filters-bar">
        <form action="{{ route('admin.orders') }}" method="GET">
            <input type="text" name="search" placeholder="Search by Order Number..." value="{{ request('search') }}">
            
            <select name="status">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="cleared" {{ request('status') == 'cleared' ? 'selected' : '' }}>Cleared</option>
                <option value="dispatched" {{ request('status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <select name="payment_status">
                <option value="">All Payment Status</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="cleared" {{ request('payment_status') == 'cleared' ? 'selected' : '' }}>Cleared</option>
            </select>
            
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.orders') }}" class="btn btn-secondary">Clear</a>
        </form>
    </div>
    
    <!-- Orders Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order Number (16-digit)</th>
                <th>Customer</th>
                <th>Products</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td><code>{{ $order->order_number }}</code></td>
                    <td>
                        {{ $order->user->name }}<br>
                        <small>{{ $order->user->email }}</small>
                    </td>
                    <td>
                        @foreach($order->orderItems->take(2) as $item)
                            <p>{{ $item->product->name }} ({{ $item->product->product_code }})</p>
                        @endforeach
                        @if($order->orderItems->count() > 2)
                            <small>+{{ $order->orderItems->count() - 2 }} more</small>
                        @endif
                    </td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->payment->method)) }}</td>
                    <td>
                        @if($order->payment->clearance_status === 'cleared')
                            <span class="badge badge-success">✓ Cleared</span>
                        @else
                            <span class="badge badge-warning">⏳ Pending</span>
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                            
                            @if($order->payment->clearance_status !== 'cleared' && $order->payment->method !== 'vpp')
                                <form action="{{ route('admin.payment.clear', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" 
                                            onclick="return confirm('Clear payment for this order?')">
                                        Clear Payment
                                    </button>
                                </form>
                            @endif
                            
                            @if($order->status === 'cleared')
                                <form action="{{ route('admin.order.dispatch', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info"
                                            onclick="return confirm('Dispatch this order?')">
                                        Dispatch
                                    </button>
                                </form>
                            @endif
                            
                            @if($order->status === 'pending' || $order->status === 'cleared')
                                <form action="{{ route('admin.order.cancel', $order->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Cancel this order?')">
                                        Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <!-- Pagination -->
    <div class="pagination">
        {{ $orders->appends(request()->except('page'))->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 40px 0 30px;
    }
    .filters-bar {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .filters-bar input,
    .filters-bar select {
        padding: 10px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
    }
    .btn-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
</style>
@endpush