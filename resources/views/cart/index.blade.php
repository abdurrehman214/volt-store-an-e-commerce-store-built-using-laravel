@extends('layouts.app')
@section('title','Cart — VOLT Store')
@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <h1 class="font-display text-3xl font-700 text-white mb-6">Your Cart
    <span class="text-brand-500 text-xl font-400 ml-2">({{ count($cart) }} items)</span>
  </h1>

  @if(empty($cart))
  <div class="text-center py-24 bg-dark-800 border border-dark-600 rounded-3xl">
    <p class="text-5xl mb-4">🛒</p>
    <h2 class="font-display text-xl font-700 text-white mb-2">Your cart is empty</h2>
    <p class="text-gray-500 text-sm mb-6">Add some products to get started.</p>
    <a href="{{ route('products.index') }}" class="btn-green inline-flex items-center gap-2 text-white font-medium px-6 py-2.5 rounded-xl text-sm">Browse Products →</a>
  </div>
  @else
  <div class="grid lg:grid-cols-3 gap-6">
    {{-- Cart items --}}
    <div class="lg:col-span-2 space-y-3" id="cart-items">
      @foreach($cart as $id => $item)
      <div id="row-{{ $id }}" class="flex gap-4 bg-dark-800 border border-dark-600 rounded-2xl p-4 items-center group hover:border-dark-500 transition-colors">
        <div class="w-20 h-20 rounded-xl overflow-hidden bg-dark-700 flex-shrink-0">
          <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs text-gray-600 font-mono mb-0.5">{{ $item['product_code'] }}</p>
          <p class="font-display font-600 text-white text-sm leading-snug line-clamp-2">{{ $item['name'] }}</p>
          <p class="text-xs text-gray-600 mt-0.5">
            @if($item['size']!='N/A') Size: {{ $item['size'] }} · @endif
            @if($item['color']!='N/A') Color: {{ $item['color'] }} @endif
          </p>
          <p class="text-brand-400 font-700 text-sm mt-1">${{ number_format($item['price'],2) }}</p>
        </div>
        <div class="flex flex-col items-end gap-2">
          {{-- Qty controls --}}
          <div class="flex items-center bg-dark-700 border border-dark-500 rounded-xl overflow-hidden">
            <button onclick="updateCart({{ $id }},{{ max(1,$item['quantity']-1) }})" class="px-2.5 py-1.5 text-gray-400 hover:text-white hover:bg-dark-600 transition-colors text-sm">−</button>
            <span class="px-3 text-sm text-white w-8 text-center" id="qty-{{ $id }}">{{ $item['quantity'] }}</span>
            <button onclick="updateCart({{ $id }},{{ $item['quantity']+1 }})" class="px-2.5 py-1.5 text-gray-400 hover:text-white hover:bg-dark-600 transition-colors text-sm">+</button>
          </div>
          <p class="text-sm font-700 text-white" id="line-{{ $id }}">${{ number_format($item['price']*$item['quantity'],2) }}</p>
          <button onclick="removeItem({{ $id }})" class="text-xs text-gray-700 hover:text-red-400 transition-colors">Remove</button>
        </div>
      </div>
      @endforeach
    </div>

    {{-- Summary --}}
    <div>
      <div class="bg-dark-800 border border-dark-600 rounded-2xl p-5 sticky top-20">
        <h2 class="font-display font-700 text-white text-lg mb-4">Order Summary</h2>
        <div class="space-y-2.5 text-sm mb-4">
          <div class="flex justify-between text-gray-400">
            <span>Subtotal</span>
            <span id="sum-sub">${{ number_format($subtotal,2) }}</span>
          </div>
          <div class="flex justify-between text-gray-400">
            <span>Shipping</span>
            <span id="sum-ship">{{ $shipping==0 ? '<span class="text-brand-400">Free</span>' : '$'.number_format($shipping,2) }}</span>
          </div>
          <div class="flex justify-between text-gray-400">
            <span>Tax (8%)</span>
            <span id="sum-tax">${{ number_format($tax,2) }}</span>
          </div>
          <div class="flex justify-between text-white font-700 pt-2.5 border-t border-dark-600 text-base">
            <span>Total</span>
            <span id="sum-total">${{ number_format($total,2) }}</span>
          </div>
        </div>
        @if($subtotal < 75)
        <div class="bg-brand-950 border border-brand-900 rounded-xl p-3 mb-4">
          <p class="text-xs text-brand-400">Add <span class="font-700">${{ number_format(75-$subtotal,2) }}</span> more for free shipping!</p>
          <div class="mt-2 bg-dark-700 rounded-full h-1.5 overflow-hidden">
            <div class="bg-brand-500 h-full rounded-full transition-all duration-500" style="width:{{ min(100,($subtotal/75)*100) }}%"></div>
          </div>
        </div>
        @endif
        @auth
        <a href="{{ route('checkout.index') }}" class="btn-green block text-center text-white font-medium py-3 rounded-xl text-sm mb-3">Proceed to Checkout →</a>
        @else
        <a href="{{ route('login') }}" class="btn-green block text-center text-white font-medium py-3 rounded-xl text-sm mb-3">Login to Checkout →</a>
        @endauth
        <a href="{{ route('products.index') }}" class="block text-center text-xs text-gray-600 hover:text-gray-400 transition-colors">← Continue Shopping</a>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
@push('scripts')
<script>
function updateCart(id,qty){
    fetch(`/cart/update/${id}`,{method:'PUT',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'},body:JSON.stringify({quantity:qty})})
    .then(r=>r.json()).then(d=>{
        if(d.success){
            document.getElementById(`qty-${id}`).textContent=qty;
            if(d.line_total!==undefined)document.getElementById(`line-${id}`).textContent='$'+d.line_total.toFixed(2);
            if(d.subtotal!==undefined){
                document.getElementById('sum-sub').textContent='$'+d.subtotal.toFixed(2);
                document.getElementById('sum-ship').textContent=d.shipping==0?'<span class="text-brand-400">Free</span>':'$'+d.shipping.toFixed(2);
                document.getElementById('sum-tax').textContent='$'+d.tax.toFixed(2);
                document.getElementById('sum-total').textContent='$'+d.total.toFixed(2);
            }
            updateCartBadge(d.cart_count);
            showToast('Cart updated','success');
        }
    }).catch(()=>showToast('Error updating cart','error'));
}
function removeItem(id){
    if(!confirm('Remove this item?'))return;
    fetch(`/cart/remove/${id}`,{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>r.json()).then(d=>{
        if(d.success){
            const row=document.getElementById(`row-${id}`);
            row.style.opacity='0';row.style.transform='translateX(-20px)';row.style.transition='all 0.3s ease';
            setTimeout(()=>{row.remove();if(d.cart_empty)location.reload();},300);
            updateCartBadge(d.cart_count);
            if(d.subtotal!==undefined){
                document.getElementById('sum-sub').textContent='$'+d.subtotal.toFixed(2);
                document.getElementById('sum-total').textContent='$'+d.total.toFixed(2);
            }
            showToast(d.message,'success');
        }
    }).catch(()=>showToast('Error','error'));
}
</script>
@endpush