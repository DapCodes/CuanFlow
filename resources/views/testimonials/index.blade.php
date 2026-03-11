@extends('layouts.app')

@section('title', 'Testimoni Pelanggan - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Testimoni</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Testimoni Pelanggan
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola ulasan dan testimoni yang masuk dari halaman landing page Anda.
                </p>
            </div>
        </section>

        {{-- FILTER SECTION --}}
        <x-card-container>
            <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 block">Pilih Produk</label>
                    <select id="filterProduct" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold text-gray-700 shadow-sm">
                        <option value="">Semua Produk (Layanan Umum)</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $selectedProduct == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-3 pb-3">
                    <div id="filterLoading" class="hidden flex items-center gap-2">
                        <div class="w-4 h-4 border-2 border-cuan-green border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Memuat...</span>
                    </div>
                </div>
            </div>
        </x-card-container>

        {{-- TABLE SECTION --}}
        <x-card-container>
            <div id="testimonialTableContainer">
                @include('testimonials._table')
            </div>
        </x-card-container>
    </div>
</main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterProduct = document.getElementById('filterProduct');
        const tableContainer = document.getElementById('testimonialTableContainer');
        const loading = document.getElementById('filterLoading');

        function fetchTestimonials(page = 1) {
            loading.classList.remove('hidden');
            tableContainer.classList.add('opacity-50', 'pointer-events-none');

            const productId = filterProduct.value;
            const url = `{{ route('testimonials.index') }}?product_id=${productId}&page=${page}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
                loading.classList.add('hidden');
                tableContainer.classList.remove('opacity-50', 'pointer-events-none');
                
                // Re-bind pagination links
                bindPagination();
            })
            .catch(error => {
                console.error('Error fetching testimonials:', error);
                loading.classList.add('hidden');
                tableContainer.classList.remove('opacity-50', 'pointer-events-none');
            });
        }

        function bindPagination() {
            document.querySelectorAll('.ajax-pagination a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    fetchTestimonials(page);
                });
            });
        }

        filterProduct.addEventListener('change', () => fetchTestimonials(1));
        
        bindPagination();
    });
</script>
@endpush
