@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-white border border-gray-100 rounded-[2rem] p-8 mb-8 shadow-sm shadow-emerald-100/20 relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
                <p class="text-gray-500 mt-2 font-medium">Monitoring performa CuanFlow dan kendali sistem terpusat di sini.</p>
                
                <div class="flex items-center gap-4 mt-6">
                    <div class="flex items-center gap-2 bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">System Online</span>
                    </div>
                    <span class="text-gray-300">|</span>
                    <p class="text-sm text-gray-500 font-medium">
                        <i class="far fa-clock mr-1"></i> {{ now()->format('H:i') }} WIB
                    </p>
                </div>
            </div>
            
            <div class="flex items-center gap-4 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ now()->format('l') }}</p>
                    <p class="text-lg font-bold text-gray-900">{{ now()->format('d F Y') }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-emerald-50 rounded-full blur-3xl opacity-60"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-emerald-50 rounded-full blur-3xl opacity-60"></div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4" 
         x-data="{ 
            activeUsers: {{ $stats['active_users'] }},
            async updateActiveUsers() {
                try {
                    const response = await fetch('{{ route('admin.dashboard.active-users') }}');
                    const data = await response.json();
                    this.activeUsers = data.count;
                } catch (error) {
                    console.error('Error fetching active users:', error);
                }
            }
         }"
         x-init="setInterval(() => updateActiveUsers(), 30000)">
        
        <!-- Active Users (Realtime) -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-5 shadow-lg shadow-emerald-100 transition-all hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div class="text-white">
                    <p class="text-xs font-bold uppercase tracking-wider opacity-80">User Aktif</p>
                    <p class="text-3xl font-black mt-1" x-text="activeUsers">{{ $stats['active_users'] }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                    <i class="fas fa-signal text-white text-xl animate-pulse"></i>
                </div>
            </div>
            <div class="flex items-center gap-1.5 mt-3">
                <div class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></div>
                <span class="text-[10px] font-bold text-white uppercase tracking-widest">Real-time</span>
            </div>
        </div>

        <!-- Users -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Total Users</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['users']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-500 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 mt-3">
                Lihat detail <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        
        <!-- Roles -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Roles</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['roles']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-shield text-purple-500 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center gap-1 text-sm text-purple-600 hover:text-purple-700 mt-3">
                Lihat detail <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        
        <!-- Permissions -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Permissions</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['permissions']) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-key text-amber-500 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.permissions.index') }}" class="inline-flex items-center gap-1 text-sm text-amber-600 hover:text-amber-700 mt-3">
                Lihat detail <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        
        <!-- Units -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Units</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['units']) }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-ruler text-teal-500 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.units.index') }}" class="inline-flex items-center gap-1 text-sm text-teal-600 hover:text-teal-700 mt-3">
                Lihat detail <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
        
        <!-- Expense Categories -->
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">Expense Cat.</p>
                    <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['expense_categories']) }}</p>
                </div>
                <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tags text-rose-500 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('admin.expense-categories.index') }}" class="inline-flex items-center gap-1 text-sm text-rose-600 hover:text-rose-700 mt-3">
                Lihat detail <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.users.create') }}" 
               class="flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-cuan-green hover:bg-cuan-yellow/10 transition-colors">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-plus text-blue-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Tambah User</span>
            </a>
            
            <a href="{{ route('admin.roles.create') }}" 
               class="flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-cuan-green hover:bg-cuan-yellow/10 transition-colors">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plus-circle text-purple-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Tambah Role</span>
            </a>
            
            <a href="{{ route('admin.units.create') }}" 
               class="flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-cuan-green hover:bg-cuan-yellow/10 transition-colors">
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plus text-teal-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Tambah Unit</span>
            </a>
            
            <a href="{{ route('admin.expense-categories.create') }}" 
               class="flex flex-col items-center gap-3 p-4 rounded-xl border-2 border-dashed border-gray-200 hover:border-cuan-green hover:bg-cuan-yellow/10 transition-colors">
                <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder-plus text-rose-600"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Tambah Kategori</span>
            </a>
        </div>
    </div>
</div>
@endsection
