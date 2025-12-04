@extends('layouts.app')

@section('content')
<div class="flex h-[calc(100vh-4rem)] bg-gray-50">
    
    <!-- Sidebar - History Chat -->
    <div class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <button onclick="createNewChat()" class="w-full px-4 py-2 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-lg hover:from-indigo-600 hover:to-blue-700 transition text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Chat Baru
            </button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-3" id="chatHistory">
            @foreach(auth()->user()->aiChatSessions()->latest()->take(20)->get() as $s)
            <button onclick="loadChat({{ $s->id }})" 
                class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-100 transition mb-1 {{ $s->id == $session->id ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700' }}">
                <div class="text-sm font-medium truncate">{{ $s->title }}</div>
                <div class="text-xs text-gray-500">{{ $s->created_at->diffForHumans() }}</div>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Main Chat Area -->
    <div class="flex-1 flex flex-col">
        
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-robot text-white"></i>
                    </div>
                    <div>
                        <h1 class="font-semibold text-gray-900">Clara AI</h1>
                        <p class="text-xs text-gray-500">Asisten Bisnis Cerdas</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <span id="quotaDisplay">{{ auth()->user()->daily_chat_quota }}</span>/3 chat tersisa
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto px-6 py-4" id="chatContainer">
            @forelse($messages as $message)
                @if($message->role === 'user')
                    <div class="flex justify-end mb-6">
                        <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl px-4 py-3 max-w-[80%]">
                            <p class="text-sm">{{ $message->content }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex gap-3 mb-6">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-white text-xs"></i>
                        </div>
                        <div class="bg-white rounded-2xl px-4 py-3 max-w-[80%] shadow-sm">
                            <p class="text-sm text-gray-800">{{ $message->content }}</p>
                        </div>
                    </div>
                @endif
            @empty
                <div class="flex items-center justify-center h-full">
                    <div class="text-center max-w-md">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-blue-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-robot text-indigo-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Halo! 👋</h3>
                        <p class="text-gray-600 text-sm mb-6">Saya Clara AI, siap membantu bisnis Anda</p>
                        
                        <div class="space-y-2">
                            <button onclick="askQuestion('Bagaimana trend penjualan minggu ini?')" class="w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm text-left transition">
                                📊 Trend penjualan minggu ini
                            </button>
                            <button onclick="askQuestion('Produk apa yang paling laris?')" class="w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm text-left transition">
                                ⭐ Produk terlaris
                            </button>
                            <button onclick="askQuestion('Produk mana yang stoknya menipis?')" class="w-full px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg text-sm text-left transition">
                                📦 Cek stok menipis
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Input Area -->
        <div class="bg-white border-t border-gray-200 px-6 py-4" id="inputArea">
            <form id="chatForm" class="flex gap-3">
                @csrf
                <input type="hidden" name="session_id" value="{{ $session->id }}">
                <input type="text" id="messageInput" name="message" placeholder="Tanyakan sesuatu..." 
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    maxlength="1000">
                <button type="submit" id="sendButton"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-lg font-medium hover:from-indigo-600 hover:to-blue-700 transition disabled:opacity-50">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
let currentQuota = {{ auth()->user()->daily_chat_quota }};

function updateQuota(remaining) {
    currentQuota = remaining;
    document.getElementById('quotaDisplay').textContent = remaining;
    
    if (remaining <= 0) {
        document.getElementById('inputArea').innerHTML = `
            <div class="text-center py-4">
                <p class="text-sm text-gray-600">Kuota chat harian Anda sudah habis. Reset besok pukul 00:00</p>
            </div>
        `;
    }
}

function askQuestion(q) {
    document.getElementById('messageInput').value = q;
    document.getElementById('chatForm').dispatchEvent(new Event('submit'));
}

function createNewChat() {
    window.location.href = '{{ route("clara-ai.new-session") }}';
}

function loadChat(id) {
    window.location.href = `{{ route("clara-ai.index") }}?session=${id}`;
}

document.getElementById('chatForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const btn = document.getElementById('sendButton');
    const container = document.getElementById('chatContainer');
    const message = input.value.trim();
    
    if (!message) return;
    
    btn.disabled = true;
    input.disabled = true;
    
    // Add user message
    container.insertAdjacentHTML('beforeend', `
        <div class="flex justify-end mb-6">
            <div class="bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-2xl px-4 py-3 max-w-[80%]">
                <p class="text-sm">${escapeHtml(message)}</p>
            </div>
        </div>
    `);
    
    // Add loading under user message
    container.insertAdjacentHTML('beforeend', `
        <div class="flex gap-3 mb-6" id="loading">
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-robot text-white text-xs"></i>
            </div>
            <div class="bg-white rounded-2xl px-4 py-3 shadow-sm">
                <div class="flex gap-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>
    `);
    
    input.value = '';
    container.scrollTop = container.scrollHeight;
    
    try {
        const res = await fetch('{{ route("clara-ai.chat") }}', {
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
        document.getElementById('loading').remove();
        
        if (data.success) {
            container.insertAdjacentHTML('beforeend', `
                <div class="flex gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-robot text-white text-xs"></i>
                    </div>
                    <div class="bg-white rounded-2xl px-4 py-3 max-w-[80%] shadow-sm">
                        <p class="text-sm text-gray-800">${escapeHtml(data.message)}</p>
                    </div>
                </div>
            `);
            
            if (data.remaining_quota !== undefined) {
                updateQuota(data.remaining_quota);
            }
        } else {
            container.insertAdjacentHTML('beforeend', `
                <div class="flex gap-3 mb-6">
                    <div class="bg-red-50 rounded-2xl px-4 py-3 max-w-[80%]">
                        <p class="text-sm text-red-600">${data.message}</p>
                    </div>
                </div>
            `);
        }
    } catch (err) {
        document.getElementById('loading').remove();
        alert('Terjadi kesalahan koneksi');
    }
    
    btn.disabled = false;
    input.disabled = false;
    input.focus();
    container.scrollTop = container.scrollHeight;
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endpush
@endsection