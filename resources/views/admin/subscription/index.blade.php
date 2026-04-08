@extends('admin.layouts.app')

@section('title', 'Daftar Pelanggan')
@section('page-title', 'Manajemen Billing')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Subscriptions</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6" x-data="subscriptionManager()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-users-viewfinder text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Daftar Pelanggan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Monitor status langganan dan akses fitur seluruh pengguna sistem</p>
            </div>
        </div>
        
        <!-- Actions & Filter Tabs -->
        <div class="flex flex-col sm:flex-row gap-3 items-center">
            <button type="button" @click="openModal = true" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-black text-xs uppercase tracking-widest hover:bg-emerald-700 transition duration-300 shadow-md shadow-emerald-200 active:scale-95 whitespace-nowrap">
                <i class="fas fa-plus"></i> Tambah Pelanggan
            </button>

            <div class="bg-white rounded-2xl p-1.5 flex gap-1.5 border border-gray-200 shadow-sm">
            @php
                $tabClasses = "px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95 whitespace-nowrap";
            @endphp
            <a href="{{ route('admin.subscription-users.index') }}" 
               class="{{ $tabClasses }} {{ !$status ? 'bg-gray-900 text-white shadow-md' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Semua
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'active']) }}" 
               class="{{ $tabClasses }} {{ $status == 'active' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Aktif
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'trial']) }}" 
               class="{{ $tabClasses }} {{ $status == 'trial' ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Trial
            </a>
            <a href="{{ route('admin.subscription-users.index', ['status' => 'expired']) }}" 
               class="{{ $tabClasses }} {{ $status == 'expired' ? 'bg-rose-100 text-rose-700 shadow-sm' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Expired
            </a>
        </div>
        </div>
    </div>

    {{-- RINGKASAN STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Users --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Total Pengguna</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 uppercase tracking-tighter">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 shadow-sm">
                    <i class="fas fa-users text-gray-400 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Active Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Pelanggan Aktif</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600 uppercase tracking-tighter">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100 shadow-sm shadow-emerald-100/50">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Trial Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Masa Trial</p>
                    <p class="mt-1 text-2xl font-black text-indigo-600 uppercase tracking-tighter">{{ number_format($stats['trial']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center border border-indigo-100 shadow-sm shadow-indigo-100/50">
                    <i class="fas fa-flask text-indigo-500 text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Expired Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-500">Habis Masa Berlaku</p>
                    <p class="mt-1 text-2xl font-black text-rose-600 uppercase tracking-tighter">{{ number_format($stats['expired']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center border border-rose-100 shadow-sm shadow-rose-100/50">
                    <i class="fas fa-calendar-xmark text-rose-500 text-lg"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
    <x-card-container class="!p-0 overflow-hidden border border-gray-200 shadow-sm bg-white rounded-xl">
        {{-- Toolbar: Search --}}
        <div class="border-b border-gray-200 px-4 md:px-6 py-5 bg-gray-50/50">
            <form action="{{ route('admin.subscription-users.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-center md:justify-between gap-4">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="w-full md:max-w-xs">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2 block italic">Cari Nama / Email</label>
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pelanggan..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition-all shadow-md shadow-gray-200 active:scale-95 group">
                        <i class="fas fa-search group-hover:rotate-12 transition-transform"></i>
                    </button>
                    @if(request()->anyFilled(['search']))
                        <a href="{{ route('admin.subscription-users.index', ['status' => $status]) }}" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-red-500 transition-all shadow-sm active:scale-95" title="Reset">
                            <i class="fas fa-redo-alt text-sm"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">Profil Pengguna</th>
                        <th class="px-6 py-4 text-left whitespace-nowrap">Paket Langganan</th>
                        <th class="px-6 py-4 text-left">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center font-black">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subscriptions as $sub)
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-full bg-gray-900 border-2 border-gray-800 flex items-center justify-center text-emerald-400 font-black text-xs uppercase overflow-hidden shadow-lg group-hover:scale-105 transition-transform">
                                    @if($sub->user->profile_photo_path)
                                        <img src="{{ Storage::url($sub->user->profile_photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($sub->user->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="max-w-[200px]">
                                    <p class="font-black text-gray-900 leading-tight uppercase tracking-tight truncate">{{ $sub->user->name }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-emerald-500 mt-1 italic truncate font-mono">{{ $sub->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col gap-1.5">
                                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest inline-block w-fit text-white {{ $sub->tier->badge_color ?? 'bg-gray-600' }} shadow-sm">
                                    {{ $sub->tier->display_name }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center gap-2 text-xs font-bold text-gray-900">
                                <i class="far fa-calendar-times text-gray-300"></i>
                                <span>Hingga: {{ ($sub->is_trial ? $sub->trial_ends_at : $sub->expires_at)?->format('d/m/Y') ?? 'Unlimited' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider 
                                @if($sub->status == 'active') bg-emerald-100 text-emerald-700 
                                @elseif($sub->status == 'trial') bg-indigo-100 text-indigo-700 
                                @elseif($sub->status == 'expired') bg-red-100 text-red-700 
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $sub->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.subscription-users.show', $sub) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-indigo-600 hover:text-white text-gray-600 text-[11px] font-bold rounded-lg transition-all">
                                <i class="fas fa-eye text-[10px]"></i>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-users-slash text-4xl text-gray-200"></i>
                                <p class="font-medium">Belum ada data pelanggan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($subscriptions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/30">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </x-card-container>

    <!-- Modal Tambah Pelanggan -->
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true" @click="openModal = false">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                <form action="{{ route('admin.subscription-users.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="mb-4 flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-bold text-gray-900 uppercase tracking-tight" id="modal-title">Tambah Pelanggan Baru</h3>
                            <button type="button" @click="openModal = false" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- User Search with Debounce -->
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Cari Pengguna</label>
                                
                                <div x-show="selectedUserId" class="mb-2 p-3 bg-emerald-50 rounded-xl border border-emerald-100 flex justify-between items-center" style="display: none;">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user-circle text-emerald-500 text-lg"></i>
                                        <span x-text="selectedUserName" class="font-bold text-gray-900 text-sm"></span>
                                    </div>
                                    <button type="button" @click="clearUser" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <input type="hidden" name="user_id" x-model="selectedUserId" required>
                                </div>

                                <div x-show="!selectedUserId" class="relative">
                                    <div class="relative group">
                                        <input type="text" x-model.debounce.500ms="searchQuery" placeholder="Ketik nama atau email pengguna..." class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-emerald-500 transition-colors text-xs"></i>
                                        <i x-show="isSearching" class="fas fa-spinner fa-spin absolute right-3.5 top-1/2 -translate-y-1/2 text-emerald-500 text-xs" style="display: none;"></i>
                                    </div>

                                    <!-- Dropdown Results -->
                                    <div x-show="searchResults.length > 0 && !isSearching" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-48 overflow-y-auto" style="display: none;">
                                        <ul class="py-1">
                                            <template x-for="user in searchResults" :key="user.id">
                                                <li @click="selectUser(user)" class="px-4 py-2 hover:bg-emerald-50 cursor-pointer transition-colors border-b border-gray-100 last:border-0">
                                                    <div class="font-bold text-sm text-gray-900" x-text="user.name"></div>
                                                    <div class="text-[10px] text-gray-500 font-mono" x-text="user.email"></div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                    <div x-show="searchResults.length === 0 && searchQuery.length >= 2 && !isSearching" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg p-4 text-center text-sm text-gray-500" style="display: none;">
                                        Tidak ada pengguna ditemukan.
                                    </div>
                                </div>
                            </div>

                            <!-- Select Plan -->
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-widest text-gray-500 mb-2">Pilih Paket Langganan</label>
                                <select name="plan_id" required class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition-all duration-300">
                                    <option value="">-- Pilih Paket --</option>
                                    @foreach($allPlans as $plan)
                                        <option value="{{ $plan->id }}">
                                            {{ $plan->tier->display_name ?? 'Unknown Tier' }} - {{ $plan->duration_name }} ({{ 'Rp ' . number_format($plan->price, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100 rounded-b-2xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-black text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm uppercase tracking-widest transition-all">
                            Simpan
                        </button>
                        <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('subscriptionManager', () => ({
            openModal: false,
            searchQuery: '',
            searchResults: [],
            isSearching: false,
            selectedUserId: '',
            selectedUserName: '',
            
            init() {
                this.$watch('searchQuery', (value) => {
                    this.searchUsers(value);
                });
            },
            
            async searchUsers(query) {
                if (query.trim().length < 2) {
                    this.searchResults = [];
                    return;
                }
                
                this.isSearching = true;
                
                try {
                    const response = await fetch(`{{ route('admin.subscription-users.search') }}?query=${encodeURIComponent(query.trim())}`);
                    if (response.ok) {
                        this.searchResults = await response.json();
                    } else {
                        this.searchResults = [];
                    }
                } catch (error) {
                    console.error("Search error", error);
                    this.searchResults = [];
                } finally {
                    this.isSearching = false;
                }
            },
            
            selectUser(user) {
                this.selectedUserId = user.id;
                this.selectedUserName = user.name;
                this.searchQuery = '';
                this.searchResults = [];
            },

            clearUser() {
                this.selectedUserId = '';
                this.selectedUserName = '';
            }
        }));
    });
</script>
@endpush
@endsection
