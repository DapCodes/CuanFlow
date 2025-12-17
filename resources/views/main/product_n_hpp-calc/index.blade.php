@extends('layouts.app')

@section('title', 'Produk & Resep - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Produk & Resep</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Alert / Notifikasi (gaya seragam seperti discounts.index) --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-exclamation-circle mt-0.5 text-red-500"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        {{-- HEADER HALAMAN (pola sama dengan discounts.index, warna tetap hijau/biru) --}}
        <section
            class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-green-50 text-cuan-green border border-green-100">
                        <i class="fas fa-utensils text-sm"></i>
                    </span>
                    <span>Daftar Produk & Resep</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Kelola produk dan resep dengan perhitungan HPP otomatis dan tampilan yang konsisten.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 justify-start md:justify-end">
                <a href="{{ route('products-hpp.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cuan-olive focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-1 shadow-sm">
                    <i class="fas fa-plus-circle text-sm"></i>
                    <span>Tambah Produk</span>
                </a>
            </div>
        </section>

        {{-- RINGKASAN STATISTIK (disusun seperti card di discounts.index) --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total Produk</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $products->total() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-cube text-blue-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Produk Aktif</p>
                        <p class="mt-1 text-2xl font-semibold text-green-600">
                            {{ $products->where('is_active', true)->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center border border-green-100">
                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Rata-rata HPP</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">
                            Rp {{ number_format($products->avg('hpp'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center border border-yellow-100">
                        <i class="fas fa-calculator text-yellow-500 text-lg"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Avg Margin</p>
                        <p class="mt-1 text-2xl font-semibold text-cuan-green">
                            {{ number_format($products->avg('margin_percent'), 1) }}%
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center border border-green-100">
                        <i class="fas fa-chart-line text-cuan-green text-lg"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL + PAGINATION (pola sama dengan discounts.index) --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar: Search & Filter --}}
            <div class="border-b border-gray-200 px-4 md:px-6 py-4 space-y-3 md:space-y-0 md:flex md:items-center md:justify-between gap-4 bg-gray-50">
                <div class="w-full md:max-w-md">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Cari produk</label>
                    <div class="relative">
                        <input type="text" placeholder="Cari berdasarkan nama atau kode..."
                               class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 w-full md:w-auto">
                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Kategori</label>
                        <select
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                            <option value="">Semua Kategori</option>
                            <option value="makanan">Makanan</option>
                            <option value="minuman">Minuman</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-40 md:w-44">
                        <label class="text-xs font-medium text-gray-500 mb-1 block">Status</label>
                        <select
                            class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kode
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Produk
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Kategori
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                HPP
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Harga Jual
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Margin
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-mono font-semibold text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                        {{ $product->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                                 class="h-12 w-12 rounded-lg object-cover mr-3 border-2 border-gray-200 shadow-sm">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center mr-3 shadow-sm">
                                                <i class="fas fa-utensils text-white text-lg"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500 flex items-center mt-1">
                                                {{ $product->unit->name ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->category)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">
                                        Rp {{ number_format($product->hpp, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        per {{ $product->unit->name ?? 'unit' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-green-600">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </div>
                                    @if($product->reseller_price)
                                        <div class="text-xs text-gray-500">
                                            Reseller: Rp {{ number_format($product->reseller_price, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $margin = $product->margin_percent;
                                        if ($margin >= 30) {
                                            $bgClass = 'bg-green-100';
                                            $textClass = 'text-green-700';
                                            $icon = 'fa-arrow-up';
                                        } elseif ($margin >= 15) {
                                            $bgClass = 'bg-yellow-100';
                                            $textClass = 'text-yellow-700';
                                            $icon = 'fa-minus';
                                        } else {
                                            $bgClass = 'bg-red-100';
                                            $textClass = 'text-red-700';
                                            $icon = 'fa-arrow-down';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $bgClass }} {{ $textClass }}">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                                {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}"
                                          data-status-badge
                                          data-product-id="{{ $product->id }}"
                                          data-active="{{ $product->is_active ? '1' : '0' }}">
                                        <span class="dot w-2 h-2 rounded-full mr-2 {{ $product->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                        <span class="label">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('products-hpp.show', $product->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors"
                                           title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('products-hpp.edit', $product->id) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <form action="{{ route('products-hpp.toggle-status', $product->id) }}"
                                              method="POST"
                                              class="inline ajax-toggle-status"
                                              data-product-id="{{ $product->id }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md transition-colors
                                                        {{ $product->is_active ? 'bg-green-100 text-green-600 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                                                    title="{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                <i class="fas fa-{{ $product->is_active ? 'toggle-on' : 'toggle-off' }} text-xs"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('products-hpp.destroy', $product->id) }}"
                                              method="POST"
                                              class="inline-block"
                                              onsubmit="return confirm('Yakin ingin menghapus produk {{ $product->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-red-100 text-red-600 hover:bg-red-200 transition-colors"
                                                    title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center text-center">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fas fa-box-open text-4xl text-gray-300"></i>
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 mb-1">Belum Ada Produk</h3>
                                        <p class="text-sm text-gray-500 mb-4 max-w-sm">
                                            Mulai dengan menambahkan produk pertama Anda.
                                        </p>
                                        <a href="{{ route('products-hpp.create') }}"
                                           class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-cuan-olive">
                                            <i class="fas fa-plus-circle text-xs"></i>
                                            Tambah Produk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700">
                            Menampilkan 
                            <span class="font-semibold">{{ $products->firstItem() }}</span>
                            sampai
                            <span class="font-semibold">{{ $products->lastItem() }}</span>
                            dari
                            <span class="font-semibold">{{ $products->total() }}</span>
                            produk
                        </div>
                        <div>
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  function updateStatusBadge(productId, isActive) {
    const badge = document.querySelector(`[data-status-badge][data-product-id="${productId}"]`);
    if (!badge) return;

    badge.classList.remove('bg-green-100','text-green-800','bg-gray-100','text-gray-600');

    if (isActive) {
      badge.classList.add('bg-green-100','text-green-800');
      badge.querySelector('.label').textContent = 'Aktif';
    } else {
      badge.classList.add('bg-gray-100','text-gray-600');
      badge.querySelector('.label').textContent = 'Nonaktif';
    }

    const dot = badge.querySelector('.dot');
    if (dot) {
      dot.classList.remove('bg-green-500','bg-gray-400');
      dot.classList.add(isActive ? 'bg-green-500' : 'bg-gray-400');
    }

    badge.dataset.active = isActive ? '1' : '0';
  }

  document.querySelectorAll('form.ajax-toggle-status').forEach(function (form) {
    form.addEventListener('submit', async function (e) {
      if (!window.fetch) return;
      e.preventDefault();

      const btn  = form.querySelector('button[type="submit"]');
      const icon = btn?.querySelector('i');
      const productId = form.getAttribute('data-product-id');

      try {
        btn?.setAttribute('disabled', 'disabled');

        const res = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          }
        });

        if (!res.ok) throw new Error(await res.text() || 'Request gagal');

        const data = await res.json();
        const isActive = !!data.is_active;

        btn.classList.remove(
          'bg-gray-100','text-gray-600','hover:bg-gray-200',
          'bg-green-100','text-green-600','hover:bg-green-200'
        );

        if (isActive) {
          btn.classList.add('bg-green-100','text-green-600','hover:bg-green-200');
          btn.title = 'Nonaktifkan';
        } else {
          btn.classList.add('bg-gray-100','text-gray-600','hover:bg-gray-200');
          btn.title = 'Aktifkan';
        }

        if (icon) {
          icon.classList.remove('fa-toggle-on','fa-toggle-off');
          icon.classList.add(isActive ? 'fa-toggle-on' : 'fa-toggle-off');
        }

        updateStatusBadge(productId, isActive);

      } catch (err) {
        console.error(err);
        alert('Gagal mengubah status. Silakan coba lagi.');
      } finally {
        btn?.removeAttribute('disabled');
      }
    });
  });
});
</script>
@endpush
