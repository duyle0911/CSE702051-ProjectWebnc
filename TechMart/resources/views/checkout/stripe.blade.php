{{-- resources/views/checkout/stripe.blade.php --}}
<form action="{{ route('stripe.session') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary">Thanh toán bằng Stripe</button>
</form>
