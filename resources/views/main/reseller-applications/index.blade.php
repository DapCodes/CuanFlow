@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Kelola Lamaran Reseller - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Lamaran Reseller</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER HALAMAN --}}
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-black text-gray-900">
                    Lamaran Reseller
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Review dan kelola permohonan reseller yang masuk ke outlet Anda.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                @if(auth()->user()->outlet)
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-xl border border-gray-200 shadow-sm transition-all hover:bg-gray-50">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Status Penerimaan</span>
                        <span id="acceptance-text" class="text-xs font-black uppercase tracking-tight {{ auth()->user()->outlet->accepts_reseller ? 'text-cuan-green' : 'text-red-500' }}">
                            {{ auth()->user()->outlet->accepts_reseller ? 'Menerima Lamaran' : 'Tutup Pendaftaran' }}
                        </span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="toggle-acceptance" class="sr-only peer" {{ auth()->user()->outlet->accepts_reseller ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cuan-green"></div>
                    </label>
                </div>
                @endif
            </div>
        </section>

        {{-- RINGKASAN STATISTIK --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Lamaran</p>
                <p class="mt-2 text-2xl font-black text-gray-900">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm transition-all hover:translate-y-[-2px]">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pending</p>
                <p class="mt-2 text-2xl font-black text-amber-500">{{ number_format($stats['pending'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Diterima</p>
                <p class="mt-2 text-2xl font-black text-cuan-green">{{ number_format($stats['approved'], 0, ',', '.') }}</p>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl px-5 py-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Ditolak</p>
                <p class="mt-2 text-2xl font-black text-red-600">{{ number_format($stats['rejected'], 0, ',', '.') }}</p>
            </div>
        </section>

        {{-- KONTEN UTAMA: TOOLBAR + TABEL --}}
        <x-card-container>
            {{-- Toolbar --}}
            <form id="filter-form" action="{{ route('reseller-applications.index') }}" method="GET" class="px-6 py-5 border-b border-gray-100 bg-white space-y-4 md:space-y-0 md:flex md:items-center md:gap-4">
                <div class="flex-1 relative">
                    <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Cari nama, email, atau telepon..."
                           class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900 placeholder:text-gray-400 focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all font-bold">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>

                <div class="flex flex-wrap gap-3">
                    <select name="status" id="status-select" onchange="this.form.submit()"
                            class="rounded-xl border border-gray-300 px-4 py-2.5 text-xs font-black uppercase tracking-widest focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green transition-all bg-white min-w-[160px]">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
            </form>
            
            <div id="table-container">
                {{-- Tabel --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">Pelamar</th>
                                <th class="px-6 py-4 text-left">Outlet</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-left">Waktu</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($applications as $app)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 group-hover:bg-cuan-green group-hover:text-white transition-all">
                                            <i class="fas fa-user text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="font-black text-gray-900 leading-tight">{{ $app->customer->name ?? 'Unknown' }}</div>
                                            <div class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">
                                                {{ $app->customer->email ?? $app->customer->phone ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="text-[11px] font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded-lg border border-gray-100 w-fit">
                                        {{ $app->outlet->name ?? 'Unknown' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if($app->status === 'approved')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/20">
                                            Diterima
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-red-50 text-red-600 border border-red-100">
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        {{ $app->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <button type="button" 
                                            onclick="openDetailModal({{ json_encode($app) }}, '{{ $app->customer->name ?? 'Unknown' }}', '{{ $app->document_path ? asset('storage/'.$app->document_path) : '' }}')"
                                            class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-all active:scale-95 shadow-sm border border-gray-100 mx-auto">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="w-16 h-16 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-inbox text-gray-200 text-xl"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Belum ada lamaran</h3>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Daftar lamaran reseller masih kosong.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($applications->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/20">
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        </x-card-container>
    </div>
</main>

{{-- Detail Modal --}}
<div id="applicationModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">
            <!-- Modal Header -->
            <div class="relative px-8 pt-8 pb-6 border-b border-gray-50 bg-gray-50/50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight" id="modalTitle">Detail Aplikasi</h3>
                        <div class="flex items-center gap-2 mt-2">
                            <span id="modalApplicantName" class="text-[10px] font-black uppercase tracking-widest text-gray-400">-</span>
                        </div>
                    </div>
                    <button onclick="closeDetailModal()" class="w-10 h-10 rounded-2xl bg-white hover:bg-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all shadow-sm border border-gray-100 transition-all active:scale-95">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-8">
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Deskripsi Diri</label>
                        <div class="p-5 bg-gray-50 border border-gray-100 rounded-3xl text-sm font-bold text-gray-700 leading-relaxed shadow-inner" id="modalDescription">
                            -
                        </div>
                    </div>
                    
                    <div id="modalDocumentSection" class="hidden">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Dokumen CV / Pendukung</label>
                        
                        {{-- File Viewer --}}
                        <div id="fileViewer" class="mb-4 hidden p-1.5 bg-gray-50 border border-gray-100 rounded-3xl overflow-hidden shadow-inner">
                            <!-- Content injected by JS -->
                        </div>

                        <a href="#" id="modalDocumentLink" target="_blank" 
                           class="inline-flex items-center justify-center gap-3 w-full px-6 py-4 bg-white border border-gray-200 text-gray-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                            <i class="fas fa-download text-cuan-green"></i>
                            Unduh Dokumen CV / Pendukung
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-8 pb-8">
                <form id="actionForm" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    @method('PATCH')
                    
                    <div id="actionButtons" class="contents">
                        <button type="submit" name="status" value="rejected" 
                                class="flex-1 px-6 py-4 bg-red-50 text-red-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all active:scale-95 border border-red-100">
                            Tolak Lamaran
                        </button>
                        <button type="submit" name="status" value="approved" 
                                class="flex-1 px-6 py-4 bg-cuan-green text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-cuan-dark transition-all active:scale-95 shadow-lg shadow-cuan-green/20">
                            Terima Reseller
                        </button>
                    </div>
                    
                    <button type="button" onclick="closeDetailModal()" id="closeModalBtn" class="hidden w-full px-6 py-4 bg-gray-100 text-gray-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all active:scale-95">
                        Tutup Window
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateURLParams(params) {
        const url = new URL(window.location);
        for (const [key, value] of Object.entries(params)) {
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        }
        window.history.replaceState({}, '', url);
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                iconColor: '#658C58',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#658C58',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'font-black text-gray-900',
                }
            });
        @endif

        const filterForm = document.getElementById('filter-form');
        const searchInput = document.getElementById('search-input');
        const statusSelect = document.getElementById('status-select');
        const tableContainer = document.getElementById('table-container');
        let timeout = null;

        function refreshTable() {
            const url = new URL(filterForm.action);
            const formData = new FormData(filterForm);
            
            // Add params to URL for consistent history
            for (let [key, value] of formData.entries()) {
                if (value) url.searchParams.set(key, value);
                else url.searchParams.delete(key);
            }

            // Show subtle loading state
            tableContainer.style.opacity = '0.5';
            tableContainer.style.transition = 'opacity 0.2s ease-in-out';

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('table-container');
                
                if (newContent) {
                    tableContainer.innerHTML = newContent.innerHTML;
                    // Update URL without reload
                    window.history.replaceState({}, '', url);
                }
            })
            .catch(err => console.error('Table refresh failed:', err))
            .finally(() => {
                tableContainer.style.opacity = '1';
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(refreshTable, 500);
            });
            // Prevent enter key from reloading
            searchInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') e.preventDefault(); });
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', refreshTable);
        }

        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('#table-container .pagination a');
            if (paginationLink) {
                e.preventDefault();
                const url = new URL(paginationLink.href);
                // Keep existing filters
                const formData = new FormData(filterForm);
                for (let [key, value] of formData.entries()) {
                    if (value && !url.searchParams.has(key)) url.searchParams.set(key, value);
                }
                
                tableContainer.style.opacity = '0.5';
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('table-container');
                    if (newContent) {
                        tableContainer.innerHTML = newContent.innerHTML;
                        window.history.pushState({}, '', url);
                        window.scrollTo({ top: tableContainer.offsetTop - 100, behavior: 'smooth' });
                    }
                })
                .finally(() => {
                    tableContainer.style.opacity = '1';
                });
            }
        });
    });

    function openDetailModal(appData, applicantName, docUrl) {
        const modal = document.getElementById('applicationModal');
        document.getElementById('modalApplicantName').textContent = applicantName;
        document.getElementById('modalDescription').textContent = appData.description;
        
        const docSection = document.getElementById('modalDocumentSection');
        const docLink = document.getElementById('modalDocumentLink');
        const fileViewer = document.getElementById('fileViewer');
        
        // Reset viewer
        fileViewer.innerHTML = '';
        fileViewer.classList.add('hidden');

        if (appData.document_path) {
            docSection.classList.remove('hidden');
            docLink.href = docUrl;

            // Handle Preview
            const ext = appData.document_path.split('.').pop().toLowerCase();
            let viewerContent = '';
            
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                viewerContent = `<img src="${docUrl}" class="w-full h-auto rounded-2xl shadow-sm border border-gray-100 mx-auto max-h-[500px] object-contain" />`;
            } else if (ext === 'pdf') {
                viewerContent = `<iframe src="${docUrl}" class="w-full h-[450px] border-none rounded-2xl shadow-inner" loading="lazy"></iframe>`;
            } else if (['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'].includes(ext)) {
                // Warning: Google Viewer might not work on localhost, but works on public domains
                const googleViewerUrl = `https://docs.google.com/gview?url=${encodeURIComponent(new URL(docUrl, window.location.origin).href)}&embedded=true`;
                viewerContent = `
                    <div class="space-y-3">
                        <iframe src="${googleViewerUrl}" class="w-full h-[450px] border-none rounded-2xl shadow-inner" loading="lazy"></iframe>
                        <div class="text-[9px] text-center text-gray-400 font-bold uppercase italic">
                            Pratinjau Office menggunakan Google Viewer (memerlukan internet)
                        </div>
                    </div>
                `;
            }
            
            if (viewerContent) {
                fileViewer.innerHTML = viewerContent;
                fileViewer.classList.remove('hidden');
            }
        } else {
            docSection.classList.add('hidden');
        }

        const form = document.getElementById('actionForm');
        const baseUrl = "{{ route('reseller-applications.index') }}";
        form.action = `${baseUrl}/${appData.id}`;
        
        const actionButtons = document.getElementById('actionButtons');
        const closeModalBtn = document.getElementById('closeModalBtn');

        if (appData.status !== 'pending') {
             actionButtons.classList.add('hidden');
             closeModalBtn.classList.remove('hidden');
        } else {
             actionButtons.classList.remove('hidden');
             closeModalBtn.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeDetailModal() {
        document.getElementById('applicationModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Ajax Toggle Acceptance
    document.getElementById('toggle-acceptance')?.addEventListener('change', function() {
        const isChecked = this.checked;
        const textEl = document.getElementById('acceptance-text');
        
        fetch("{{ route('reseller-applications.toggle-acceptance') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                textEl.textContent = data.accepts_reseller ? 'Menerima Lamaran' : 'Tutup Pendaftaran';
                textEl.className = `text-xs font-black uppercase tracking-tight ${data.accepts_reseller ? 'text-cuan-green' : 'text-red-500'}`;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#f0fdf4',
                    iconColor: '#22c55e',
                });
            } else {
                this.checked = !isChecked;
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Terjadi kesalahan saat mengubah status.',
                    iconColor: '#ef4444',
                });
            }
        })
        .catch(error => {
            this.checked = !isChecked;
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem.',
            });
        });
    });
</script>
@endpush
