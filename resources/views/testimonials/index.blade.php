@extends('layouts.app')

@section('title', 'Kelola Testimoni - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Testimoni</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-500 border border-blue-100">
                        <i class="fas fa-comment-alt text-sm"></i>
                    </span>
                    <span>Testimoni Pelanggan</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola ulasan dan testimoni yang masuk dari halaman landing page Anda.
                </p>
            </div>
        </section>

        <section class="bg-gray-100/50 border border-gray-200 rounded-2xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                {{-- Filter Produk --}}
                <div>
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1 block">Pilih Produk</label>
                    <select id="filterProduct" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all font-semibold text-gray-700 shadow-sm border-gray-200">
                        <option value="">Semua Produk (Layanan Umum)</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $selectedProduct == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center gap-2 pb-1">
                    <div id="filterLoading" class="hidden">
                        <i class="fas fa-circle-notch fa-spin text-blue-500"></i>
                        <span class="text-[11px] font-bold text-gray-400 uppercase ml-2">Memasifikasi...</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" id="testimonialTableContainer">
            @include('testimonials._table')
        </section>
    </div>
</main>

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
@endsection
