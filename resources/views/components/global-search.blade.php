{{-- 
    Ultra Premium Global Search Component
    ─────────────────────────────────────────
    A spotlight-style full-screen search system.
--}}

<div x-data="globalSearch()" 
     x-show="isOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 backdrop-blur-0"
     x-transition:enter-end="opacity-100 backdrop-blur-xl"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 backdrop-blur-xl"
     x-transition:leave-end="opacity-0 backdrop-blur-0"
     class="fixed inset-0 z-[200] flex flex-col items-center justify-start pt-[10vh] px-4 bg-white/95 backdrop-blur-md"
     @keydown.window.escape="closeSearch()"
     @keydown.window.slash.prevent="openSearch()"
     @open-search.window="openSearch()"
     x-cloak>
    
    <!-- Header: Search Input & Close -->
    <div class="w-full max-w-3xl transform transition-all duration-300"
         x-show="isOpen"
         x-transition:enter="transition ease-out duration-500 delay-100"
         x-transition:enter-start="translate-y-10 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100">
        
        <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
            <i class="fa-solid fa-magnifying-glass text-xl text-gray-400"></i>
            <input type="text" 
                   x-ref="searchInput"
                   x-model="query"
                   @input.debounce.300ms="performSearch()"
                   placeholder="Cari menu atau fitur..."
                   class="flex-1 bg-transparent border-none outline-none text-xl sm:text-2xl font-bold text-gray-900 placeholder-gray-300 focus:ring-0">
            <button @click="closeSearch()" 
                    class="px-2 py-2 text-xs font-bold text-gray-400 hover:text-primary-500 transition-all uppercase tracking-widest">
                Tutup
            </button>
        </div>

        <!-- Shortcuts Info -->
        <div class="flex items-center gap-4 mt-6">
            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Paling Sering Dicari:</span>
            <div class="flex flex-wrap gap-2">
                <template x-for="rec in recommendations">
                    <button @click="query = rec.label; performSearch()" 
                            class="px-3 py-1 bg-gray-50 hover:bg-primary-50 text-gray-500 hover:text-primary-600 rounded-full text-[10px] font-bold border border-gray-100 hover:border-primary-200 transition-all">
                        <span x-text="rec.label"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Results -->
        <div class="mt-12 overflow-y-auto max-h-[60vh] custom-scrollbar pr-2">
            <!-- Loading State -->
            <div x-show="isLoading" class="flex flex-col items-center justify-center py-12">
                <div class="h-12 w-12 border-4 border-primary-100 border-t-primary-500 rounded-full animate-spin"></div>
                <p class="text-sm font-bold text-gray-400 mt-4 uppercase tracking-widest">Mencari hasil terbaik...</p>
            </div>

            <!-- Empty State -->
            <div x-show="!isLoading && query && results.length === 0" class="text-center py-12">
                <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="fa-solid fa-search-minus text-3xl text-gray-300"></i>
                </div>
                <h4 class="text-xl font-black text-gray-900">Oops! Tidak ditemukan</h4>
                <p class="text-gray-500 mt-1">Kami tidak menemukan hasil untuk "<span class="text-primary-600 font-bold" x-text="query"></span>"</p>
            </div>

            <!-- Results List -->
            <div x-show="!isLoading && results.length > 0" class="space-y-4">
                <template x-for="(item, index) in results" :key="index">
                    <a :href="item.url" 
                       class="flex items-center gap-4 p-4 rounded-xl hover:bg-gray-50 transition-all group overflow-hidden border border-transparent hover:border-gray-100">
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-base font-bold text-gray-900 group-hover:text-primary-600 transition-colors" x-text="item.label"></span>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-wider" 
                                      :class="item.type === 'Action' ? 'bg-amber-100 text-amber-600' : 'bg-primary-100 text-primary-600'"
                                      x-text="item.type"></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" x-text="item.description || 'Navigasi ke menu ini'"></p>
                        </div>

                        <!-- Arrow -->
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fa-solid fa-arrow-right text-primary-500 text-xs"></i>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>

    <!-- Simple Shortcut Hint -->
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 text-[9px] font-bold uppercase tracking-[0.2em] text-gray-400">
        Tekan <span class="text-gray-900">ESC</span> untuk membatalkan
    </div>
</div>

<script>
function globalSearch() {
    return {
        isOpen: false,
        query: '',
        isLoading: false,
        results: [],
        recommendations: [
            { label: 'Kasir', keywords: ['pos', 'kasir', 'transaksi'] },
            { label: 'Penjualan', keywords: ['sales', 'penjualan'] },
            { label: 'Laporan', keywords: ['report', 'laporan'] },
            { label: 'Produk', keywords: ['product', 'barang'] }
        ],
        searchItems: [
            @canAccessFeature('pos')
            @can('akses pos')
            { label: 'Point of Sale (Kasir)', keywords: ['pos', 'kasir', 'transaksi', 'jual'], description: 'Buka scanner dan kasir pos untuk transaksi langsung', url: "{{ route('pos.index') }}", type: 'Action' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('sales_management')
            @can('lihat penjualan')
            { label: 'Riwayat Penjualan', keywords: ['penjualan', 'sales', 'history', 'riwayat', 'laporan'], description: 'Lihat semua riwayat transaksi dan status pembayaran', url: "{{ route('sales.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('discount_management')
            @can('lihat diskon')
            { label: 'Daftar Diskon', keywords: ['diskon', 'promo', 'potongan'], description: 'Kelola promosi dan potongan harga outlet', url: "{{ route('discounts.index') }}", type: 'Menu' },
            { label: 'Buat Diskon Baru', keywords: ['buat', 'tambah', 'diskon', 'promo'], description: 'Tambahkan diskon atau promo menu baru', url: "{{ route('discounts.create') }}", type: 'Action' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('finance_management')
            @can('lihat keuangan')
            { label: 'Keuangan', keywords: ['keuangan', 'finance', 'laporan', 'uang'], description: 'Statistik kas, laba rugi, dan ringkasan keuangan', url: "{{ route('finance.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('other_income')
            @can('buat pemasukan')
            { label: 'Catat Pemasukan', keywords: ['pemasukan', 'income', 'tambah', 'uang masuk'], description: 'Catat uang masuk di luar transaksi penjualan', url: "{{ route('expenses.index', ['type' => 'income']) }}", type: 'Action' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('operational_costs')
            @can('buat pengeluaran')
            { label: 'Catat Pengeluaran', keywords: ['pengeluaran', 'expense', 'biaya', 'operasional', 'beli'], description: 'Catat biaya operasional atau belanja bahan', url: "{{ route('expenses.index', ['type' => 'expense']) }}", type: 'Action' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('balance_withdrawal')
            @can('buat penarikan')
            { label: 'Penarikan Saldo', keywords: ['tarik', 'saldo', 'withdraw', 'pencairan'], description: 'Cuan-kan saldo digital Anda ke rekening bank', url: "{{ route('withdraw.index') }}", type: 'Menu' },
            { label: 'Ajukan Penarikan', keywords: ['buat', 'ajukan', 'tarik', 'saldo'], description: 'Kirim pengajuan penarikan dana baru', url: "{{ route('withdraw.create') }}", type: 'Action' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('payment_methods')
            @can('lihat metode pembayaran')
            { label: 'Metode Pembayaran', keywords: ['payment', 'metode', 'bayar', 'qris', 'bank'], description: 'Atur QRIS, Bank, dan Link Pembayaran outlet', url: "{{ route('outlet-payment-links.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('task_management')
            @can('tasks.view')
            { label: 'Manajemen Tugas (Kanban)', keywords: ['tugas', 'task', 'kanban', 'kerja', 'project'], description: 'Atur alur kerja tim dan project outlet', url: "{{ route('tasks.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('dashboard')
            @can('lihat statistik')
            { label: 'Dashboard & Statistik', keywords: ['statistik', 'chart', 'grafik', 'analisis', 'dashboard'], description: 'Pantau pertumbuhan bisnis melalui data visual', url: "{{ route('statistics.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('reports')
            @can('lihat laporan')
            { label: 'Laporan Keseluruhan', keywords: ['laporan', 'report', 'keuangan', 'pdf', 'excel'], description: 'Download dan cetak laporan lengkap bisnis', url: "{{ route('reports.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('products_recipes')
            @can('lihat produk')
            { label: 'Daftar Produk & Resep', keywords: ['produk', 'menu', 'makanan', 'minuman', 'resep', 'barang'], description: 'Kelola menu dan hitung HPP otomatis', url: "{{ route('products-hpp.index') }}", type: 'Menu' },
            { label: 'Tambah Produk Baru', keywords: ['tambah', 'buat', 'produk', 'menu', 'resep'], description: 'Masukkan item baru ke dalam daftar menu', url: "{{ route('products-hpp.create') }}", type: 'Action' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('raw_materials')
            @can('lihat bahan baku')
            { label: 'Stok Bahan Baku', keywords: ['bahan', 'baku', 'raw', 'material', 'stok', 'inventory'], description: 'Pantau sisa persediaan bahan baku dapoer', url: "{{ route('raw-materials.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('production')
            @can('lihat produksi')
            { label: 'Daftar Produksi', keywords: ['produksi', 'production', 'olah', 'masak'], description: 'Alat pengolahan bahan baku menjadi produk jadi', url: "{{ route('production.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('stock_opname')
            @can('lihat stock opname')
            { label: 'Stock Opname', keywords: ['stock', 'opname', 'so', 'cek', 'stok'], description: 'Sesuaikan stok fisik dengan stok di sistem', url: "{{ route('stock-opname.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('multi_outlet')
            @can('lihat outlet')
            { label: 'Informasi Outlet', keywords: ['outlet', 'toko', 'cabang', 'informasi', 'profil'], description: 'Kelola profil, alamat, dan pengaturan outlet', url: "{{ route('outlets.index') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('clara_ai')
            @can('akses clara ai')
            { label: 'Clara AI Chat', keywords: ['clara', 'ai', 'chat', 'tanya', 'asisten', 'bot'], description: 'Asisten cerdas untuk tanya jawab seputar bisnis', url: "{{ route('clara-ai.index') }}", type: 'Action' },
            @endcan
            @endcanAccessFeature

            @canAccessFeature('account_settings')
            @can('edit profil')
            { label: 'Pengaturan Akun', keywords: ['akun', 'profil', 'profile', 'password', 'sandi', 'setting'], description: 'Ganti info profil, foto, dan keamanan akun', url: "{{ route('profile.edit') }}", type: 'Menu' },
            @endcan
            @endcanAccessFeature

            { label: 'Notifikasi & Peringatan', keywords: ['notifikasi', 'pesan', 'info', 'alert', 'stock', 'peringatan', 'expired'], description: 'Pusat pemberitahuan sistem dan stok kritis', url: "{{ route('stock-notifications.index') }}", type: 'Menu' },
        ],

        openSearch() {
            this.isOpen = true;
            this.query = '';
            this.results = [];
            document.body.classList.add('overflow-hidden');
            setTimeout(() => this.$refs.searchInput.focus(), 100);
        },

        closeSearch() {
            this.isOpen = false;
            document.body.classList.remove('overflow-hidden');
        },

        performSearch() {
            if (!this.query.trim()) {
                this.results = [];
                return;
            }

            this.isLoading = true;
            const q = this.query.toLowerCase().trim();

            setTimeout(() => {
                this.results = this.searchItems.filter(item => {
                    return item.label.toLowerCase().includes(q) || 
                           item.keywords.some(k => k.toLowerCase().includes(q));
                });
                this.isLoading = false;
            }, 300);
        }
    }
}
</script>
