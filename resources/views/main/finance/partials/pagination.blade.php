@if($items->hasPages())
<div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <span class="font-medium">Menampilkan</span>
            <span class="font-bold text-gray-700">{{ $items->firstItem() }}</span>
            <span>-</span>
            <span class="font-bold text-gray-700">{{ $items->lastItem() }}</span>
            <span>dari</span>
            <span class="font-bold text-gray-700">{{ $items->total() }}</span>
            <span>data</span>
        </div>
        
        <div class="flex items-center gap-1">
            {{-- Previous Button --}}
            @if($items->onFirstPage())
                <button disabled class="px-3 py-1.5 text-xs font-semibold text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>
            @else
                <button onclick="loadPage({{ $items->currentPage() - 1 }})" 
                        class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>
            @endif

            {{-- Page Numbers --}}
            @foreach(range(1, $items->lastPage()) as $page)
                @if($page == $items->currentPage())
                    <button class="px-3 py-1.5 text-xs font-bold text-white bg-blue-600 border border-blue-600 rounded-lg">
                        {{ $page }}
                    </button>
                @elseif($page == 1 || $page == $items->lastPage() || abs($page - $items->currentPage()) <= 1)
                    <button onclick="loadPage({{ $page }})" 
                            class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all">
                        {{ $page }}
                    </button>
                @elseif(abs($page - $items->currentPage()) == 2)
                    <span class="px-2 text-gray-400">...</span>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if($items->hasMorePages())
                <button onclick="loadPage({{ $items->currentPage() + 1 }})" 
                        class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            @else
                <button disabled class="px-3 py-1.5 text-xs font-semibold text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            @endif
        </div>
    </div>
</div>
@endif