@extends('layouts.app')

@section('title', 'Clara AI')

@section('breadcrumb')
    <li class="flex items-center">
        <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
        </svg>
        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600 font-medium">Dashboard</a>
    </li>
    <li class="flex items-center">
        <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                clip-rule="evenodd"></path>
        </svg>
        <span class="text-gray-900 font-medium">Clara AI</span>
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
</style>
@endpush

@section('content')

    <!-- Main Chat Interface -->
    <div class="flex bg-gray-50" style="height: calc(100vh - 64px - 57px);">

        <!-- Sidebar - History Chat -->
        <div id="chatSidebar"
            class="w-64 bg-white border-r border-gray-200 flex flex-col fixed lg:relative z-30 h-full lg:translate-x-0 -translate-x-full transition-transform duration-300 ease-in-out">

            <!-- Sidebar Header -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
                <h2 class="font-semibold text-gray-800 text-sm">Riwayat Chat</h2>
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- New Chat Button -->
            <div class="p-3 border-b border-gray-200 flex-shrink-0">
                <button onclick="createNewChat()"
                    class="w-full px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-lg hover:from-indigo-600 hover:to-blue-700 transition-all duration-200 text-sm font-medium shadow-sm hover:shadow">
                    <i class="fas fa-plus mr-2"></i>Chat Baru
                </button>
            </div>

            <!-- Chat History List -->
            <div class="flex-1 overflow-y-auto p-2" id="chatHistory">
                @foreach ($sessions as $s)
                    <button onclick="loadChat({{ $s->id }})"
                        class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-gray-100 transition-colors duration-150 mb-1 group {{ $s->id == $session->id ? 'bg-indigo-50 border border-indigo-200' : 'border border-transparent' }}">
                        <div
                            class="text-sm font-medium truncate {{ $s->id == $session->id ? 'text-indigo-700' : 'text-gray-700' }}">
                            {{ $s->title }}
                        </div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $s->created_at->diffForHumans() }}</div>
                    </button>
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
                            class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                            <!-- <i class="fas fa-robot text-white text-sm"></i> -->
                            <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">

                        </div>
                        <div class="min-w-0">
                            <h1 class="font-semibold text-gray-900 truncate text-sm">Clara AI</h1>
                            <p class="text-xs text-gray-500">Asisten Bisnis Cerdas</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-xs text-gray-500">Kuota:</span>
                        <span class="text-sm font-semibold text-indigo-600"
                            id="quotaDisplay">{{ auth()->user()->daily_chat_quota }}</span>
                        <span class="text-xs text-gray-400">/3</span>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto" id="chatContainer">
                <div class="max-w-3xl mx-auto px-4 py-6">
                    @forelse($messages as $message)
                        @if ($message->role === 'user')
                            <div class="flex justify-end mb-4">
                                <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[80%] shadow-sm break-words">
                                    <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">{{ $message->content }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex gap-3 mb-4">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-[80%] break-words overflow-hidden">
                                    <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap break-words word-break">{{ $message->content }}</p>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div id="emptyState" class="flex items-center justify-center h-full">
                            <div class="text-center max-w-md w-full">
                                <!-- <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center mx-auto mb-6 shadow-sm">
                                        <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                                    </div> -->
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">Halo! 👋</h3>
                                <p class="text-gray-600 text-sm mb-8">Saya Clara AI, siap membantu bisnis Anda dengan
                                    analisis data dan insight berharga</p>

                                <div class="space-y-2">
                                    <button onclick="askQuestion('Bagaimana trend penjualan minggu ini?')"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-xl text-sm text-left transition-all duration-200 shadow-sm hover:shadow group">
                                        <span class="text-gray-700 group-hover:text-indigo-700">Trend penjualan minggu
                                            ini</span>
                                    </button>
                                    <button onclick="askQuestion('Produk apa yang paling laris?')"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-xl text-sm text-left transition-all duration-200 shadow-sm hover:shadow group">
                                        <span class="text-gray-700 group-hover:text-indigo-700">Produk terlaris</span>
                                    </button>
                                    <button onclick="askQuestion('Produk mana yang stoknya menipis?')"
                                        class="w-full px-4 py-3 bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 rounded-xl text-sm text-left transition-all duration-200 shadow-sm hover:shadow group">
                                        <span class="text-gray-700 group-hover:text-indigo-700">Cek stok menipis</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Input Area -->
            <div class="bg-white border-t border-gray-200 flex-shrink-0" id="inputArea">
                @if (auth()->user()->daily_chat_quota > 0)
                    <div class="max-w-3xl mx-auto px-4 py-4">
                        <form id="chatForm" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="session_id" value="{{ $session->id }}">
                            <div class="flex-1 relative">
                                <input type="text" id="messageInput" name="message"
                                    placeholder="Tanyakan sesuatu tentang bisnis Anda..."
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm transition-shadow shadow-sm"
                                    maxlength="1000" autocomplete="off">
                            </div>
                            <button type="submit" id="sendButton"
                                class="px-5 py-3 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-xl font-medium hover:from-indigo-600 hover:to-blue-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex-shrink-0 shadow-sm hover:shadow">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </form>
                        <p class="text-xs text-gray-400 text-center mt-2">Clara AI dapat membuat kesalahan. Harap
                            verifikasi informasi penting.</p>
                    </div>
                @else
                    <div class="max-w-3xl mx-auto px-4 py-6">
                        <div
                            class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl px-6 py-4 text-center">
                            <div class="flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-orange-500 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h4 class="text-sm font-semibold text-gray-900">Kuota Chat Habis</h4>
                            </div>
                            <p class="text-sm text-gray-600">Kuota chat harian Anda sudah habis. Kuota akan direset besok
                                pukul 00:00 WIB</p>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            let currentQuota = {{ auth()->user()->daily_chat_quota }};

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

            function updateQuota(remaining) {
                currentQuota = remaining;
                document.getElementById('quotaDisplay').textContent = remaining;

                if (remaining <= 0) {
                    document.getElementById('inputArea').innerHTML = `
            <div class="max-w-3xl mx-auto px-4 py-6">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 border border-orange-200 rounded-xl px-6 py-4 text-center">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-6 h-6 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="text-sm font-semibold text-gray-900">Kuota Chat Habis</h4>
                    </div>
                    <p class="text-sm text-gray-600">Kuota chat harian Anda sudah habis. Kuota akan direset besok pukul 00:00 WIB</p>
                </div>
            </div>
        `;
                }
            }

            function askQuestion(q) {
                document.getElementById('messageInput').value = q;
                document.getElementById('chatForm').dispatchEvent(new Event('submit'));
            }

            function createNewChat() {
                if (currentQuota <= 0) {
                    alert('Kuota chat Anda sudah habis. Silakan coba lagi besok.');
                    return;
                }
                window.location.href = '{{ route('clara-ai.new-session') }}';
            }

            function loadChat(id) {
                // Close sidebar on mobile after selecting chat
                if (window.innerWidth < 1024) {
                    toggleSidebar();
                }
                window.location.href = `{{ route('clara-ai.index') }}?session_id=${id}`;
            }

            document.getElementById('chatForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();

                const input = document.getElementById('messageInput');
                const btn = document.getElementById('sendButton');
                const container = document.getElementById('chatContainer');
                const message = input.value.trim();

                if (!message) return;

                if (currentQuota <= 0) {
                    alert('Kuota chat Anda sudah habis.');
                    return;
                }

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
    <div class="flex justify-end mb-4">
        <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[80%] shadow-sm break-words">
            <p class="text-sm leading-relaxed whitespace-pre-wrap break-words">${escapeHtml(message)}</p>
        </div>
    </div>
`);

                // Add loading
                chatContent.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 mb-4" id="loading">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-2.5">
                <div class="flex gap-1">
                    <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                    <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
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
                            session_id: '{{ $session->id }}'
                        })
                    });

                    const data = await res.json();
                    document.getElementById('loading')?.remove();

                    if (data.success) {
                        chatContent.insertAdjacentHTML('beforeend', `
                            <div class="flex gap-3 mb-4">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <img src="{{ asset('assets/image/clara-ai.png') }}" class="p-1" alt="">
                                </div>
                                <div class="bg-gray-50 border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-[80%] break-words overflow-hidden">
                                    <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap break-words word-break">${escapeHtml(data.message)}</p>
                                </div>
                            </div>
                        `);

                        if (data.remaining_quota !== undefined) {
                            updateQuota(data.remaining_quota);
                        }
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
