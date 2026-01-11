@extends('admin.layouts.app')

@section('title', 'Detail Outlet')
@section('page-title', 'Detail Outlet: ' . $outlet->name)

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.outlets.index') }}" class="text-gray-500 hover:text-gray-700">Daftar Outlet</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Detail</span>
</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-6 py-8 flex flex-col md:flex-row gap-8 items-start">
            <div class="w-24 h-24 rounded-2xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                @if($outlet->logo)
                    <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="w-20 h-20 object-contain">
                @else
                    <i class="fas fa-store text-teal-600 text-4xl"></i>
                @endif
            </div>
            
            <div class="flex-1 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $outlet->name }}</h2>
                        <p class="text-gray-500 flex items-center gap-2 mt-1">
                            <i class="fas fa-barcode"></i>
                            <span class="font-mono">{{ $outlet->code }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.outlets.toggle-status', $outlet) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold transition-all {{ $outlet->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                <i class="fas {{ $outlet->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                <span>{{ $outlet->is_active ? 'Status: Aktif' : 'Status: Nonaktif' }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Alamat</p>
                        <p class="text-sm text-gray-700">{{ $outlet->address ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kontak</p>
                        <p class="text-sm text-gray-700">{{ $outlet->phone ?? '-' }} / {{ $outlet->email ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Owner</p>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-cuan-dark text-white text-[10px] flex items-center justify-center">
                                {{ substr($outlet->owner->name ?? '?', 0, 1) }}
                            </div>
                            <p class="text-sm text-gray-700">{{ $outlet->owner->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Total Penjualan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->sales()->count()) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Total Produk</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->products->count()) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Bahan Baku</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->rawMaterials->count()) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Total Staf</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outlet->users->count()) }}</p>
        </div>
    </div>

    <!-- Details Tabs -->
    <div x-data="{ activeTab: 'staff' }" class="space-y-4">
        <div class="flex border-b border-gray-200 gap-6">
            <button @click="activeTab = 'staff'" :class="activeTab === 'staff' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-4 px-2 border-b-2 font-semibold transition-all">
                Daftar Staf
            </button>
            <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-4 px-2 border-b-2 font-semibold transition-all">
                Produk
            </button>
            <button @click="activeTab = 'raw_materials'" :class="activeTab === 'raw_materials' ? 'border-cuan-green text-cuan-green' : 'border-transparent text-gray-500 hover:text-gray-700'" class="pb-4 px-2 border-b-2 font-semibold transition-all">
                Bahan Baku
            </button>
        </div>

        <!-- Staff Tab -->
        <div x-show="activeTab === 'staff'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Role</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($outlet->users as $user)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            @foreach($user->roles as $role)
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase">{{ $role->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada staf</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Products Tab -->
        <div x-show="activeTab === 'products'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga Jual</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($outlet->products as $product)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada produk</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Raw Materials Tab -->
        <div x-show="activeTab === 'raw_materials'" class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Bahan Baku</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Harga Beli</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($outlet->rawMaterials as $rm)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $rm->name }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $rm->unit->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($rm->purchase_price, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada bahan baku</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
