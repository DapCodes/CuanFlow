@extends('layouts.app')

@section('title', 'Informasi Outlet - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Informasi Outlet</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4">
    <div class="max-w-7xl mx-auto">
        
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg" role="alert">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
        @endif

        <x-card-container>
            <!-- Header -->
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-store text-orange-500 mr-3"></i>
                            Informasi Outlet
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Kelola informasi dan pengaturan outlet Anda</p>
                    </div>
                    <a href="{{ route('outlets.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-br from-yellow-500 to-orange-500 text-white rounded-lg font-semibold hover:from-yellow-600 hover:to-orange-600 transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Tambah Outlet
                    </a>
                </div>
            </div>

            <!-- Filter & Search -->
            <div class="p-6 bg-gray-50 border-b border-gray-200">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchInput" placeholder="Cari outlet..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                    <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-barcode mr-1 text-gray-400"></i>
                                Kode
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-store mr-1 text-gray-400"></i>
                                Outlet
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                Alamat
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-phone mr-1 text-gray-400"></i>
                                Kontak
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-user-tie mr-1 text-gray-400"></i>
                                Owner
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-toggle-on mr-1 text-gray-400"></i>
                                Status
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-cog mr-1 text-gray-400"></i>
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($outlets as $outlet)
                        <tr class="hover:bg-gray-50 transition-colors outlet-row" data-status="{{ $outlet->is_active ? 'active' : 'inactive' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    {{ $outlet->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($outlet->logo)
                                    <img src="{{ Storage::url($outlet->logo) }}" alt="{{ $outlet->name }}" class="h-12 w-12 rounded-lg object-cover mr-3 border-2 border-gray-200 shadow-sm">
                                    @else
                                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center mr-3 shadow-sm">
                                        <i class="fas fa-store text-white text-lg"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 outlet-name">{{ $outlet->name }}</div>
                                        <div class="text-xs text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $outlet->created_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                    {{ Str::limit($outlet->address, 50) }}
                                </div>
                                @if($outlet->latitude && $outlet->longtitude)
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-globe mr-1"></i>
                                    {{ number_format($outlet->latitude, 6) }}, {{ number_format($outlet->longtitude, 6) }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <i class="fas fa-phone text-green-500 mr-1"></i>
                                    {{ $outlet->phone ?? '-' }}
                                </div>
                                @if($outlet->email)
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-envelope text-blue-500 mr-1"></i>
                                    {{ $outlet->email }}
                                </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($outlet->owner)
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-cuan-olive to-cuan-green flex items-center justify-center text-white font-semibold mr-2">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $outlet->owner->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $outlet->owner->email }}</div>
                                    </div>
                                </div>
                                @else
                                <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('outlets.show', $outlet->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors" 
                                       title="Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="{{ route('outlets.edit', $outlet->id) }}" 
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors" 
                                       title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <form action="{{ route('outlets.destroy', $outlet->id) }}" 
                                          method="POST" 
                                          class="inline-block" 
                                          onsubmit="return confirm('Yakin ingin menghapus outlet {{ $outlet->name }}? Semua data terkait akan terhapus!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-colors" 
                                                title="Hapus">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-store-slash text-5xl text-gray-300"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Outlet</h3>
                                    <p class="text-sm text-gray-500 mb-6">Mulai dengan menambahkan outlet pertama Anda</p>
                                    <a href="{{ route('outlets.create') }}" class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white rounded-lg font-semibold hover:bg-cuan-olive transition-colors">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        Tambah Outlet
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($outlets->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700">
                        Menampilkan 
                        <span class="font-semibold">{{ $outlets->firstItem() }}</span>
                        sampai
                        <span class="font-semibold">{{ $outlets->lastItem() }}</span>
                        dari
                        <span class="font-semibold">{{ $outlets->total() }}</span>
                        outlet
                    </div>
                    <div>
                        {{ $outlets->links() }}
                    </div>
                </div>
            </div>
            @endif

            <!-- Summary Statistics -->
            <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Total Outlet</p>
                                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $outlets->total() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-store text-blue-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Outlet Aktif</p>
                                <p class="text-2xl font-bold text-green-600 mt-1">{{ $outlets->where('is_active', true)->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Outlet Nonaktif</p>
                                <p class="text-2xl font-bold text-gray-600 mt-1">{{ $outlets->where('is_active', false)->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-times-circle text-gray-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Total Owner</p>
                                <p class="text-2xl font-bold text-purple-600 mt-1">{{ $outlets->pluck('owner_id')->unique()->count() }}</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card-container>

    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.outlet-row');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        rows.forEach(row => {
            const name = row.querySelector('.outlet-name').textContent.toLowerCase();
            const status = row.dataset.status;
            
            const matchesSearch = name.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
});
</script>
@endpush
@endsection