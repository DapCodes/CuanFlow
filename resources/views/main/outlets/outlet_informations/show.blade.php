@extends('layouts.app')

@section('title', 'Detail Outlet - ' . $outlet->name)

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <a href="{{ route('outlets.index') }}" class="text-gray-600 hover:text-gray-900">Informasi Outlet</a>
</li>
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">{{ $outlet->name }}</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                                    {{ $outlet->name }}
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">Detail informasi outlet</p>
                            </div>
                            <a href="{{ route('outlets.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>
                        </div>
                    </div>
                            
                    <div class="p-6 space-y-6">
                        <!-- Logo & Name -->
                        <div class="flex items-start gap-4">
                            @if($outlet->logo)
                            <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="h-24 w-24 rounded-lg object-cover border-2 border-gray-200 shadow-md">
                            @else
                            <div class="h-24 w-24 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center shadow-md">
                                <i class="fas fa-store text-white text-3xl"></i>
                            </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900">{{ $outlet->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $outlet->code }}</span>
                                </p>
                                <div class="mt-3">
                                    @if($outlet->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        Aktif
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                                        Nonaktif
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <dl class="grid grid-cols-1 gap-4">
                                <div class="flex items-start">
                                    <dt class="flex items-center text-sm font-semibold text-gray-700 w-32">
                                        <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>
                                        Dibuat
                                    </dt>
                                    <dd class="text-sm text-gray-900">{{ $outlet->created_at->format('d F Y, H:i') }}</dd>
                                </div>
                                <div class="flex items-start">
                                    <dt class="flex items-center text-sm font-semibold text-gray-700 w-32">
                                        <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                                        Diperbarui
                                    </dt>
                                    <dd class="text-sm text-gray-900">{{ $outlet->updated_at->format('d F Y, H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </x-card-container>

                <!-- Contact Info Card -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-address-book text-green-500 mr-2"></i>
                            Informasi Kontak
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-4">
                            <div class="flex items-start">
                                <dt class="flex items-center text-sm font-semibold text-gray-700 w-32">
                                    <i class="fas fa-phone text-green-500 mr-2"></i>
                                    Telepon
                                </dt>
                                <dd class="text-sm text-gray-900">{{ $outlet->phone ?? '-' }}</dd>
                            </div>
                            <div class="flex items-start">
                                <dt class="flex items-center text-sm font-semibold text-gray-700 w-32">
                                    <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                    Email
                                </dt>
                                <dd class="text-sm text-gray-900">{{ $outlet->email ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </x-card-container>

                <!-- Location Card -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-map-marked-alt text-red-500 mr-2"></i>
                            Lokasi
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                Alamat Lengkap
                            </h4>
                            <p class="text-sm text-gray-900 leading-relaxed">{{ $outlet->address }}</p>
                        </div>

                        @if($outlet->latitude && $outlet->longtitude)
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-globe text-blue-500 mr-2"></i>
                                Koordinat
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Latitude</p>
                                    <p class="text-sm font-mono text-gray-900">{{ number_format($outlet->latitude, 6) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Longitude</p>
                                    <p class="text-sm font-mono text-gray-900">{{ number_format($outlet->longtitude, 6) }}</p>
                                </div>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $outlet->latitude }},{{ $outlet->longtitude }}" 
                               target="_blank"
                               class="mt-3 inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                Lihat di Google Maps
                            </a>
                        </div>
                        @endif
                    </div>
                </x-card-container>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Owner Card -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-user-tie text-purple-500 mr-2"></i>
                            Owner
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        @if($outlet->owner)
                        <div class="flex items-center gap-3">
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center shadow-md">
                                <span class="text-white text-4xl font-semibold">{{ strtoupper(substr($outlet->owner->name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $outlet->owner->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $outlet->owner->email }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 bg-purple-100 text-purple-800 text-xs font-medium rounded">
                                    {{ ucfirst($outlet->owner->role) }}
                                </span>
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-gray-500">Owner belum ditentukan</p>
                        @endif
                    </div>
                </x-card-container>

                <!-- Statistics Card -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                            Statistik
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-box text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Produk</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_products'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-cubes text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Bahan Baku</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_raw_materials'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-shopping-cart text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Penjualan</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_sales'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-yellow-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Karyawan</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['total_employees'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card-container>

                <!-- Quick Actions -->
                <x-card-container>
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-b border-gray-200">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <i class="fas fa-bolt text-orange-500 mr-2"></i>
                            Aksi Cepat
                        </h2>
                    </div>
                    
                    <div class="p-6 space-y-2">
                        <form action="{{ route('outlets.toggle-status', $outlet->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                                <i class="fas fa-toggle-{{ $outlet->is_active ? 'off' : 'on' }} mr-2"></i>
                                {{ $outlet->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Outlet
                            </button>
                        </form>
                    </div>
                </x-card-container>
            </div>
        </div>
    </div>
</main>
@endsection