@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Clara AI')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-bold tracking-tight">Clara AI</span>
</li>
@endsection

@push('styles')
<style>
    /* Force word wrapping untuk text panjang */
    .word-break {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        hyphens: auto;
    }
    
    /* Pastikan container chat tidak overflow */
    #chatContainer {
        overflow-x: hidden;
    }
    
    /* Responsive text bubbles */
    @media (max-width: 640px) {
        .max-w-\[80\%\] {
            max-width: 85%;
        }
    }

    /* Hover effect untuk delete button */
    .chat-item:hover .delete-btn {
        opacity: 1;
    }

    .delete-btn {
        opacity: 0;
        transition: opacity 0.2s;
    }

    .chat-item.active .delete-btn {
        opacity: 1;
    }
</style>
@endpush

@section('content')

    <!-- Main Chat Interface -->
    <div class="flex bg-gray-50" style="height: calc(100vh - 64px - 57px);">

        <!-- Sidebar - History Chat -->
        <div id="chatSidebar"
            class="w-64 bg-white border-r border-gray-200 flex flex-col fixed lg:relative z-30 h-full lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out">

            <!-- Sidebar Header -->
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0 bg-gray-50/50">
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Riwayat Chat</h2>
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- New Chat Button -->
            <div class="p-4 border-b border-gray-100 flex-shrink-0 bg-white">
                @can('sesi baru clara ai')
                <button onclick="createNewChat()"
                    class="w-full px-4 py-3 bg-cuan-green text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-lg shadow-emerald-100 active:scale-95">
                    <i class="fas fa-plus mr-2"></i>Mulai Chat Baru
                </button>
                @endcan
            </div>

            <!-- Chat History List -->
            <div class="flex-1 overflow-y-auto p-2" id="chatHistory">
                @foreach ($sessions as $s)
                    <div data-session-id="{{ $s->id }}" 
                        class="chat-item relative mb-1 {{ $s->id == $session->id ? 'active' : '' }}">
                        <div class="relative group">
                            <button onclick="loadChat({{ $s->id }})"
                                class="w-full text-left px-3 py-3 pr-10 rounded-xl transition-all duration-200 {{ $s->id == $session->id ? 'bg-emerald-50 border border-emerald-100 shadow-sm' : 'border border-transparent hover:bg-gray-50' }}">
                                <div class="text-xs font-black uppercase tracking-tight truncate session-title {{ $s->id == $session->id ? 'text-emerald-700' : 'text-gray-700' }}">
                                    {{ $s->title }}
                                </div>
                                <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase">{{ $s->created_at->diffForHumans() }}</div>
                            </button>
                            @can('hapus sesi clara ai')
                            <button onclick="confirmDelete(event, {{ $s->id }})" 
                                class="delete-btn absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Overlay untuk mobile -->
        <div id="sidebarOverlay"
            class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden hidden transition-opacity duration-300"
            onclick="toggleSidebar()"></div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-white">

            <!-- Header -->
            <div class="bg-white border-b border-gray-200 px-4 py-3 flex-shrink-0">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Toggle Sidebar Button (Mobile) -->
                        <button onclick="toggleSidebar()"
                            class="lg:hidden text-gray-600 hover:text-gray-900 flex-shrink-0 p-2 -ml-2 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        <div
                            class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-100">
                            <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                        </div>
                        <div class="min-w-0">
                            <h1 class="font-black text-gray-900 truncate text-sm uppercase tracking-tighter">Clara AI</h1>
                            <p class="text-[10px] font-bold text-cuan-green uppercase tracking-widest">Asisten Bisnis Cerdas</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <!-- Kuota display removed -->
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto" id="chatContainer">
                <div class="max-w-3xl mx-auto px-4 py-6">
                    @forelse($messages as $message)
                        @if ($message->role === 'user')
                            <div class="flex justify-end mb-6">
                                <div class="bg-gray-900 text-white rounded-2xl rounded-tr-none px-5 py-3.5 max-w-[85%] shadow-lg shadow-gray-200/50 break-words">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $message->content }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-4 mb-6">
                                <div class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-xl shadow-emerald-50">
                                    <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                                </div>
                                <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-none px-5 py-3.5 max-w-[85%] shadow-sm break-words overflow-hidden border-l-4 border-l-cuan-green">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap break-words word-break transition-all duration-300">{{ $message->content }}</p>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div id="emptyState" class="flex items-center justify-center h-full">
                            <div class="text-center max-w-md w-full">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Halo! 👋</h3>
                                <p class="text-gray-600 text-sm mb-8">Saya Clara AI, siap membantu bisnis Anda dengan
                                    analisis data dan insight berharga</p>

                                <div class="space-y-3">
                                    @can('chat dengan clara ai')
                                    <button onclick="askQuestion('Bagaimana trend penjualan minggu ini?')"
                                        class="w-full px-5 py-4 bg-white border border-gray-100 hover:border-cuan-green hover:shadow-emerald-100 rounded-2xl text-[10px] font-black uppercase tracking-widest text-left transition-all duration-300 shadow-sm hover:shadow-xl group flex items-center justify-between">
                                        <span class="text-gray-400 group-hover:text-cuan-green">Trend penjualan minggu ini</span>
                                        <i class="fas fa-chevron-right text-gray-200 group-hover:text-cuan-green group-hover:translate-x-1 transition-all"></i>
                                    </button>
                                    <button onclick="askQuestion('Produk apa yang paling laris?')"
                                        class="w-full px-5 py-4 bg-white border border-gray-100 hover:border-cuan-green hover:shadow-emerald-100 rounded-2xl text-[10px] font-black uppercase tracking-widest text-left transition-all duration-300 shadow-sm hover:shadow-xl group flex items-center justify-between">
                                        <span class="text-gray-400 group-hover:text-cuan-green">Produk terlaris</span>
                                        <i class="fas fa-chevron-right text-gray-200 group-hover:text-cuan-green group-hover:translate-x-1 transition-all"></i>
                                    </button>
                                    <button onclick="askQuestion('Produk mana yang stoknya menipis?')"
                                        class="w-full px-5 py-4 bg-white border border-gray-100 hover:border-cuan-green hover:shadow-emerald-100 rounded-2xl text-[10px] font-black uppercase tracking-widest text-left transition-all duration-300 shadow-sm hover:shadow-xl group flex items-center justify-between">
                                        <span class="text-gray-400 group-hover:text-cuan-green">Cek stok menipis</span>
                                        <i class="fas fa-chevron-right text-gray-200 group-hover:text-cuan-green group-hover:translate-x-1 transition-all"></i>
                                    </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Input Area (quota removed, always available) -->
            <div class="bg-white border-t border-gray-200 flex-shrink-0" id="inputArea">
                <div class="max-w-3xl mx-auto px-4 py-4">
                    <form id="chatForm" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $session->id }}">
                        
                        <div class="flex-1">
                            @can('chat dengan clara ai')
                            <input type="text" id="messageInput" name="message"
                                placeholder="Tanyakan tren bisnis Anda..."
                                class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-cuan-green/10 focus:border-cuan-green focus:bg-white text-sm transition-all shadow-sm"
                                maxlength="1000" autocomplete="off">
                            @else
                            <input type="text" disabled
                                placeholder="Izin akses Clara AI ditangguhkan"
                                class="w-full px-6 py-4 bg-gray-100 border border-gray-200 rounded-2xl text-sm cursor-not-allowed">
                            @endcan
                        </div>

                        @can('chat dengan clara ai')
                        <button type="submit" id="sendButton"
                            class="w-12 h-12 bg-cuan-green text-white rounded-2xl font-black hover:bg-cuan-dark transition-all shadow-xl shadow-emerald-100 active:scale-95 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-paper-plane text-lg"></i>
                        </button>
                        @endcan
                    </form>
                    <p class="text-xs text-gray-400 text-center mt-2">Clara AI dapat membuat kesalahan. Harap
                        verifikasi informasi penting.</p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            let currentSessionId = {{ $session->id }};

            // Toggle Sidebar
            function toggleSidebar() {
                const sidebar = document.getElementById('chatSidebar');
                const overlay = document.getElementById('sidebarOverlay');

                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            // Close sidebar when clicking outside (mobile)
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    document.getElementById('chatSidebar').classList.remove('-translate-x-full');
                    document.getElementById('sidebarOverlay').classList.add('hidden');
                }
            });

            // quota handling removed

            function updateSessionTitle(sessionId, newTitle) {
                // Update title di sidebar
                const sessionItem = document.querySelector(`[data-session-id="${sessionId}"]`);
                if (sessionItem) {
                    const titleElement = sessionItem.querySelector('.session-title');
                    if (titleElement) {
                        titleElement.textContent = newTitle;
                    }
                }
            }

            function askQuestion(q) {
                document.getElementById('messageInput').value = q;
                document.getElementById('chatForm').dispatchEvent(new Event('submit'));
            }

            function createNewChat() {
                window.location.href = '{{ route('clara-ai.new-session') }}';
            }

            function loadChat(id) {
                // Close sidebar on mobile after selecting chat
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
                window.location.href = `{{ route('clara-ai.index') }}?session_id=${id}`;
            }

            function confirmDelete(event, sessionId) {
                event.stopPropagation(); // Prevent triggering loadChat

                Swal.fire({
                    title: 'Hapus sesi chat?',
                    text: 'Semua pesan dalam sesi ini akan dihapus secara permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        container: 'rounded-2xl',
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl font-bold px-6 py-3',
                        cancelButton: 'rounded-xl font-bold px-6 py-3'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteSession(sessionId);
                    }
                });
            }

            async function deleteSession(sessionId) {
                try {
                    const res = await fetch(`/clara-ai/session/${sessionId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (data.success) {
                        // Jika session yang dihapus adalah session aktif
                        if (sessionId == currentSessionId) {
                            // Redirect ke session baru atau halaman utama
                            window.location.href = '{{ route('clara-ai.new-session') }}';
                        } else {
                            // Hapus dari sidebar tanpa reload
                            const sessionItem = document.querySelector(`[data-session-id="${sessionId}"]`);
                            if (sessionItem) {
                                sessionItem.remove();
                            }
                        }
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal menghapus chat session: ' + data.message,
                            icon: 'error',
                            customClass: { popup: 'rounded-2xl' }
                        });
                    }
                } catch (err) {
                    console.error('Error deleting session:', err);
                    Swal.fire({
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menghapus chat session.',
                        icon: 'error',
                        customClass: { popup: 'rounded-2xl' }
                    });
                }
            }

            document.getElementById('chatForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();

                const input = document.getElementById('messageInput');
                const btn = document.getElementById('sendButton');
                const container = document.getElementById('chatContainer');
                const message = input.value.trim();

                if (!message) return;

                // quota removed — always allow submit

                btn.disabled = true;
                input.disabled = true;

                // Remove empty state if exists
                const emptyState = document.getElementById('emptyState');
                if (emptyState) {
                    emptyState.remove();
                }

                // Add user message
                const chatContent = container.querySelector('.max-w-3xl') || container;
                chatContent.insertAdjacentHTML('beforeend', `
                    <div class="flex justify-end mb-6">
                        <div class="bg-gray-900 text-white rounded-2xl rounded-tr-none px-5 py-3.5 max-w-[85%] shadow-lg shadow-gray-200/50 break-words">
                            <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message)}</p>
                        </div>
                    </div>
                `);

                // Add loading
                chatContent.insertAdjacentHTML('beforeend', `
                    <div class="flex gap-4 mb-6" id="loading">
                        <div class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-xl shadow-emerald-50">
                            <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                        </div>
                        <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-none px-5 py-3.5 shadow-sm">
                            <div class="flex gap-1.5">
                                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                                <div class="w-2 h-2 bg-cuan-green rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                            </div>
                        </div>
                    </div>
                `);

                input.value = '';
                container.scrollTop = container.scrollHeight;

                try {
                    const res = await fetch('{{ route('clara-ai.chat') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message: message,
                            session_id: currentSessionId
                        })
                    });

                    const data = await res.json();
                    document.getElementById('loading')?.remove();

                    if (data.success) {
                        chatContent.insertAdjacentHTML('beforeend', `
                            <div class="flex gap-4 mb-6">
                                <div class="w-9 h-9 rounded-xl bg-cuan-green flex items-center justify-center flex-shrink-0 shadow-xl shadow-emerald-50">
                                    <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                                </div>
                                <div class="bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-none px-5 py-3.5 max-w-[85%] shadow-sm break-words overflow-hidden border-l-4 border-l-cuan-green">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap break-words word-break">${escapeHtml(data.message)}</p>
                                </div>
                            </div>
                        `);

                        // Update title jika ini chat pertama
                        if (data.new_title) {
                            updateSessionTitle(data.session_id, data.new_title);
                        }

                        // quota removed — no update needed
                    } else {
                        chatContent.insertAdjacentHTML('beforeend', `
                            <div class="flex gap-3 mb-4">
                                <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-2.5 max-w-[80%]">
                                    <p class="text-sm text-red-600">${escapeHtml(data.message)}</p>
                                </div>
                            </div>
                        `);
                    }
                } catch (err) {
                    document.getElementById('loading')?.remove();
                    chatContent.insertAdjacentHTML('beforeend', `
                        <div class="flex gap-3 mb-4">
                            <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-2.5 max-w-[80%]">
                                <p class="text-sm text-red-600">Terjadi kesalahan koneksi. Silakan coba lagi.</p>
                            </div>
                        </div>
                    `);
                } finally {
                    btn.disabled = false;
                    input.disabled = false;
                    input.focus();
                    container.scrollTop = container.scrollHeight;
                }
            });

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Auto focus input on load
            document.addEventListener('DOMContentLoaded', function() {
                const input = document.getElementById('messageInput');
                if (input && window.innerWidth >= 768) {
                    input.focus();
                }

                // Scroll to bottom on load
                const container = document.getElementById('chatContainer');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        </script>
    @endpush
@endsection