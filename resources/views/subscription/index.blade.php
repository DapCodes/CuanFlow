@extends('layouts.app')

@section('title', 'Langganan & Paket')

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Langganan & Paket</span>
</li>
@endsection

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
