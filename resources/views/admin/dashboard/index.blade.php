@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-cuan-dark to-cuan-green rounded-2xl p-6 lg:p-8 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-2xl lg:text-3xl font-bold">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="mt-2 text-white/80">Kelola data master dan pengaturan sistem CuanFlow dari sini.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm text-white/60">{{ now()->format('l') }}</p>
                    <p class="text-lg font-semibold">{{ now()->format('d F Y') }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
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
