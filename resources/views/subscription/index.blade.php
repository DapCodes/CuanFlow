@extends('layouts.app')

@section('title', 'Langganan & Paket')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
            Pilih Paket Langganan
        </h2>
        <p class="mt-4 text-xl text-gray-500">
            Sesuaikan dengan kebutuhan bisnis Anda.
        </p>
    </div>

    @include('subscription.partials.pricing_cards')
</div>
@endsection
