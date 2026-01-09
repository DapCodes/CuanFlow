@extends('admin.layouts.app')

@section('title', 'Edit Unit')
@section('page-title', 'Edit Unit')

@section('breadcrumb')
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <a href="{{ route('admin.units.index') }}" class="text-gray-500 hover:text-gray-700">Units</a>
</li>
<li class="flex items-center">
    <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
    <span class="text-gray-700">Edit</span>
</li>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <form action="{{ route('admin.units.update', $unit) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900">Edit Unit: {{ $unit->name }}</h2>
            <a href="{{ route('admin.units.index') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </a>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Unit <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('name') border-red-300 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="abbreviation" class="block text-sm font-medium text-gray-700 mb-2">
                        Singkatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="abbreviation" id="abbreviation" value="{{ old('abbreviation', $unit->abbreviation) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green @error('abbreviation') border-red-300 @enderror">
                    @error('abbreviation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="base_unit_id" class="block text-sm font-medium text-gray-700 mb-2">Unit Dasar</label>
                    <select name="base_unit_id" id="base_unit_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
                        <option value="">-- Unit Dasar --</option>
                        @foreach($baseUnits as $baseUnit)
                        <option value="{{ $baseUnit->id }}" {{ old('base_unit_id', $unit->base_unit_id) == $baseUnit->id ? 'selected' : '' }}>
                            {{ $baseUnit->name }} ({{ $baseUnit->abbreviation }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="conversion_factor" class="block text-sm font-medium text-gray-700 mb-2">Faktor Konversi</label>
                    <input type="number" name="conversion_factor" id="conversion_factor" 
                           value="{{ old('conversion_factor', $unit->conversion_factor) }}"
                           step="0.000001" min="0"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cuan-green">
                </div>
            </div>
            
            <div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="w-5 h-5 text-cuan-green border-gray-300 rounded"
                           {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Aktifkan unit</span>
                </label>
            </div>
        </div>
        
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.units.index') }}" 
               class="px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cuan-dark text-white font-semibold rounded-lg hover:bg-cuan-green">
                <i class="fas fa-save mr-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
