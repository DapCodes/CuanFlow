@extends('layouts.app')

@section('title', 'Kelola Lamaran Reseller')

@section('breadcrumb')
<li class="flex items-center">
    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-gray-900 font-medium">Kelola Lamaran Reseller</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Alert / Notification --}}
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm">
                <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        {{-- HEADER --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-orange-50 text-orange-500 border border-orange-100">
                        <i class="fas fa-handshake text-sm"></i>
                    </span>
                    <span>Kelola Lamaran Reseller</span>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Review dan kelola permohonan reseller yang masuk ke outlet Anda.
                </p>
            </div>
            {{-- Form removed as per request --}}
        </section>

        {{-- TABLE CONTENT --}}
        <section class="bg-white border border-gray-200 rounded-xl shadow-sm">
            {{-- Toolbar can be added here if needed, keeping it simple for now as per Discount style --}}
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelamar</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Outlet Tujuan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Waktu</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($applications as $app)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="openDetailModal({{ json_encode($app) }}, '{{ $app->customer->name ?? 'Unknown' }}', '{{ $app->document_path ? asset('storage/'.$app->document_path) : '' }}')">
                            <td class="px-6 py-3">
                                <div class="font-semibold text-gray-900">{{ $app->customer->name ?? 'Unknown Customer' }}</div>
                                <div class="text-xs text-gray-500">{{ $app->customer->email ?? $app->customer->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $app->outlet->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-3">
                                @if($app->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Diterima
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span> Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-500 text-xs">
                                {{ $app->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                <button class="text-orange-600 hover:text-orange-800 font-medium text-xs">
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-inbox text-2xl text-gray-300"></i>
                                    </div>
                                    <p>Belum ada aplikasi yang masuk.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             {{-- Pagination --}}
            @if($applications->hasPages())
                <div class="px-4 md:px-6 py-3 border-t border-gray-200">
                    {{ $applications->links() }}
                </div>
            @endif
        </section>
    </div>
</main>

{{-- Detail Modal --}}
<div id="applicationModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true" role="dialog">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-file-invoice text-orange-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                            Detail Aplikasi
                        </h3>
                        <div class="mt-2 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase">Pelamar</label>
                                <p class="text-sm font-bold text-gray-800" id="modalApplicantName">-</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase">Deskripsi</label>
                                <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg mt-1" id="modalDescription">-</p>
                            </div>
                            
                            <div id="modalDocumentSection" class="hidden">
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Dokumen CV / Pendukung</label>
                                <a href="#" id="modalDocumentLink" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                    <i class="fas fa-download mr-2 text-gray-400"></i> Unduh Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="actionForm" method="POST" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                @csrf
                @method('PATCH')
                
                {{-- Approved/Rejected buttons will be shown if status is pending --}}
                <div id="actionButtons" class="flex flex-row-reverse gap-2 w-full">
                    <button type="submit" name="status" value="approved" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Terima
                    </button>
                    <button type="submit" name="status" value="rejected" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Tolak
                    </button>
                    <button type="button" onclick="closeDetailModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openDetailModal(appData, applicantName, docUrl) {
        const modal = document.getElementById('applicationModal');
        document.getElementById('modalApplicantName').textContent = applicantName;
        document.getElementById('modalDescription').textContent = appData.description;
        
        // Handle Document
        const docSection = document.getElementById('modalDocumentSection');
        const docLink = document.getElementById('modalDocumentLink');
        if (appData.document_path) {
            docSection.classList.remove('hidden');
            docLink.href = docUrl;
        } else {
            docSection.classList.add('hidden');
        }

        // Handle Form Action & Buttons
        const form = document.getElementById('actionForm');
        // Use Blade to get base URL, then append ID.
        // Note: route('reseller-applications.index') gives .../reseller-applications
        // We need .../reseller-applications/{id}
        const baseUrl = "{{ route('reseller-applications.index') }}";
        form.action = `${baseUrl}/${appData.id}`;
        
        const actionButtons = document.getElementById('actionButtons');
        // Only show approve/reject if pending
        if (appData.status !== 'pending') {
             // Hide action buttons except close (which involves restructuring html slightly or JS manipulation)
             // Simplified: Re-render buttons or hide submit buttons
             Array.from(actionButtons.querySelectorAll('button[type="submit"]')).forEach(btn => btn.classList.add('hidden'));
        } else {
             Array.from(actionButtons.querySelectorAll('button[type="submit"]')).forEach(btn => btn.classList.remove('hidden'));
        }

        modal.classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('applicationModal').classList.add('hidden');
    }
</script>
@endpush

@endsection
