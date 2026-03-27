@extends('layouts.app')
@section('title','Checkout — VOLT Store')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <h1 class="font-display text-3xl font-700 text-white mb-7">Checkout</h1>
  <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
    @csrf
    <div class="grid lg:grid-cols-5 gap-7">
      {{-- Left: Details --}}
      <div class="lg:col-span-3 space-y-5">
        {{-- Delivery Info --}}
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
          <h2 class="font-display font-700 text-white text-lg mb-4">Delivery Details</h2>
          <div class="grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-xs text-gray-500 mb-1.5">Full Name *</label>
              <input type="text" name="full_name" value="{{ old('full_name',auth()->user()->name) }}" required
                     class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
            </div>
            <div class="sm:col-span-2">
              <label class="block text-xs text-gray-500 mb-1.5">Street Address *</label>
              <input type="text" name="address" value="{{ old('address') }}" required placeholder="123 Main Street"
                     class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">City *</label>
              <input type="text" name="city" value="{{ old('city') }}" required
                     class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
            </div>
            <div>
              <label class="block text-xs text-gray-500 mb-1.5">Phone *</label>
              <input type="text" name="phone" value="{{ old('phone') }}" required
                     class="w-full bg-dark-700 border border-dark-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-gray-600">
            </div>
          </div>
        </div>

        {{-- Delivery type --}}
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
          <h2 class="font-display font-700 text-white text-lg mb-4">Delivery Method</h2>
          <div class="space-y-2.5">
            @foreach([[1,'Standard Delivery','5-7 business days','Free on orders $75+'],
                      [2,'Express Delivery','1-2 business days','$14.99'],
                      [3,'Click & Collect','Collect from store','Free']] as [$code,$name,$eta,$price])
            <label class="flex items-center justify-between p-3.5 bg-dark-700 border border-dark-500 rounded-xl cursor-pointer hover:border-brand-800 transition-colors has-[:checked]:border-brand-600 has-[:checked]:bg-brand-950/30">
              <div class="flex items-center gap-3">
                <input type="radio" name="delivery_type" value="{{ $code }}" {{ $code==1?'checked':'' }} class="accent-green-500">
                <div>
                  <p class="text-sm text-white font-medium">{{ $name }}</p>
                  <p class="text-xs text-gray-500">{{ $eta }}</p>
                </div>
              </div>
              <span class="text-sm text-brand-400 font-700">{{ $price }}</span>
            </label>
            @endforeach
          </div>
        </div>

        {{-- Payment --}}
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5">
          <h2 class="font-display font-700 text-white text-lg mb-4">Payment Method</h2>
          <div class="space-y-2.5">
            @foreach([['credit_card','💳','Credit Card','Cleared immediately'],
                      ['cheque','🏦','Cheque','Must clear before dispatch'],
                      ['vpp','💵','Cash on Delivery','Pay when you receive'],
                      ['dd','🔁','Direct Debit','Bank transfer']] as [$val,$icon,$name,$note])
            <label class="flex items-center justify-between p-3.5 bg-dark-700 border border-dark-500 rounded-xl cursor-pointer hover:border-brand-800 transition-colors has-[:checked]:border-brand-600 has-[:checked]:bg-brand-950/30">
              <div class="flex items-center gap-3">
                <input type="radio" name="payment_method" value="{{ $val }}" {{ $val=='credit_card'?'checked':'' }} class="accent-green-500">
                <div>
                  <p class="text-sm text-white font-medium">{{ $icon }} {{ $name }}</p>
                  <p class="text-xs text-gray-500">{{ $note }}</p>
                </div>
              </div>
            </label>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Right: Summary --}}
      <div class="lg:col-span-2">
        <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5 sticky top-20">
          <h2 class="font-display font-700 text-white text-lg mb-4">Order Summary</h2>
          <div class="space-y-3 mb-4 max-h-60 overflow-y-auto pr-1">
            @foreach($cart as $item)
            <div class="flex gap-3 items-center">
              <div class="w-11 h-11 rounded-lg overflow-hidden bg-dark-700 flex-shrink-0">
                <img src="{{ $item['image'] }}" alt="" class="w-full h-full object-cover">
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs text-white truncate">{{ $item['name'] }}</p>
                <p class="text-xs text-gray-600">× {{ $item['quantity'] }}</p>
              </div>
              <span class="text-xs text-brand-400 font-700 flex-shrink-0">${{ number_format($item['price']*$item['quantity'],2) }}</span>
            </div>
            @endforeach
          </div>
          <div class="border-t border-dark-600 pt-3.5 space-y-2 text-sm text-gray-400">
            <div class="flex justify-between"><span>Subtotal</span><span>${{ number_format($subtotal,2) }}</span></div>
            <div class="flex justify-between"><span>Shipping</span><span>{{ $shipping==0?'Free':'$'.number_format($shipping,2) }}</span></div>
            <div class="flex justify-between"><span>Tax (8%)</span><span>${{ number_format($tax,2) }}</span></div>
            <div class="flex justify-between text-white font-700 text-base pt-2 border-t border-dark-600">
              <span>Total</span><span class="text-brand-400">${{ number_format($total,2) }}</span>
            </div>
          </div>
          <button type="submit" id="place-btn" class="btn-green mt-4 w-full text-white font-medium py-3 rounded-xl text-sm flex items-center justify-center gap-2">
            Place Order — ${{ number_format($total,2) }}
          </button>
          <p class="text-xs text-center text-gray-700 mt-2.5">🔒 Secured with 256-bit SSL</p>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('checkout-form').addEventListener('submit',function(){
    const btn=document.getElementById('place-btn');
    btn.disabled=true;btn.innerHTML='<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Processing...';
});
</script>
@endpush