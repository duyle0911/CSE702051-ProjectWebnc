<!-- resources/views/checkout/momo.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="text-center">
            <h3>Đang chuyển sang cổng thanh toán MoMo...</h3>
            <p>Vui lòng chờ trong giây lát.</p>
        </div>

        <form id="momoForm" action="{{ $momoUrl }}" method="POST">
            @csrf
            <input type="hidden" name="data" value="{{ $data }}">
        </form>

        <script>
            document.getElementById('momoForm').submit();
        </script>
    </div>
@endsection
