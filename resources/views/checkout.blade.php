<form action="{{ route('checkout.store') }}" method="POST">
    @csrf
    <select name="payment_method">
        <option value="credit_card">Credit Card</option>
        <option value="cheque">Cheque</option>
        <option value="vpp">VPP (Cash on Delivery)</option>
        <option value="dd">DD</option>
    </select>
    
    <select name="delivery_type">
        <option value="1">Standard (Code 1)</option>
        <option value="2">Express (Code 2)</option>
    </select>
    
    <button type="submit">Place Order</button>
</form>