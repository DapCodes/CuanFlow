@extends('admin.layouts.app')

@section('title', 'Paket Berlangganan')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Paket Berlangganan</h1>
            <p class="text-gray-500 mt-1">Kelola tingkatan paket berlangganan (Tiers)</p>
        </div>
        <a href="{{ route('admin.subscription-tiers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition">
            <i class="fa-solid fa-plus mr-2"></i> Buat Paket
        </a>
    </div>

    <!-- Tiers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($tiers as $tier)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="h-10 w-10 rounded-lg flex items-center justify-center bg-indigo-50 text-indigo-600 font-bold text-lg">
                            {{ substr($tier->name, 0, 1) }}
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $tier->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $tier->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $tier->display_name }}</h3>
                    <p class="text-sm text-gray-500 line-clamp-2 h-10">{{ $tier->description }}</p>
                    
                    <div class="mt-4 flex items-baseline">
                        <span class="text-2xl font-bold text-gray-900">Rp {{ number_format($tier->price, 0, ',', '.') }}</span>
                        <span class="ml-1 text-gray-500 text-sm">/ bulan</span>
                    </div>

                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fa-solid fa-store w-5 text-gray-400"></i>
                            <span>{{ $tier->max_outlets ? $tier->max_outlets . ' Outlet Max' : 'Unlimited Outlet' }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fa-solid fa-users w-5 text-gray-400"></i>
                            <span>{{ $tier->subscriptions_count ?? 0 }} Pelanggan Aktif</span>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('admin.subscription-tiers.edit', ['tier' => $tier->id]) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Edit Paket</a>
                    <!-- Custom delete form if needed or link -->
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
