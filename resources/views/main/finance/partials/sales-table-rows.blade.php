@forelse($sales as $sale)
<tr class="hover:bg-gray-50/50 transition-all duration-200 group border-b border-gray-50 last:border-0">
    <td class="px-5 py-4 whitespace-nowrap">
        <div class="flex flex-col gap-1">
            <span class="text-xs font-mono font-bold text-gray-800">{{ $sale->invoice_number }}</span>
            <span class="text-[10px] text-gray-400 font-medium">{{ $sale->created_at->format('d M Y, H:i') }}</span>
        </div>
    </td>
    <td class="px-5 py-4">
        <div class="flex flex-col gap-1">
            <span class="text-xs font-semibold text-gray-900">{{ $sale->cashier->name }}</span>
            <div class="flex items-center gap-1.5 text-[10px] text-gray-500">
                <i class="far fa-user text-[9px] opacity-60"></i>
                <span>{{ $sale->customer->name ?? 'Pelanggan Umum' }}</span>
            </div>
        </div>
    </td>
    <td class="px-5 py-4">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide
            @if($sale->payment_method == 'cash') bg-emerald-50 text-emerald-700 border border-emerald-200
            @elseif($sale->payment_method == 'qris') bg-blue-50 text-blue-700 border border-blue-200
            @else bg-purple-50 text-purple-700 border border-purple-200 @endif">
            @if($sale->payment_method == 'cash')
                <i class="fas fa-money-bill-wave text-[9px]"></i>
            @elseif($sale->payment_method == 'qris')
                <i class="fas fa-qrcode text-[9px]"></i>
            @else
                <i class="fas fa-credit-card text-[9px]"></i>
            @endif
            {{ $sale->payment_method == 'cash' ? 'Tunai' : ($sale->payment_method == 'qris' ? 'QRIS' : 'Transfer') }}
        </span>
    </td>
    <td class="px-5 py-4 text-right">
        <span class="text-sm font-black text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
    </td>
    @if($canViewDetail || $canPrint)
    <td class="px-5 py-4">
        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-200">
            @if($canViewDetail)
            <a href="{{ route('sales.show', $sale->id) }}" 
               class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 rounded-lg shadow-sm transition-all duration-200" 
               title="Lihat Detail">
                <i class="fas fa-eye text-[10px]"></i>
            </a>
            @endif
            @if($canPrint)
            <a href="{{ route('receipt.preview', $sale->id) }}" 
               class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 rounded-lg shadow-sm transition-all duration-200" 
               title="Cetak Struk">
                <i class="fas fa-print text-[10px]"></i>
            </a>
            @endif
        </div>
    </td>
    @endif
</tr>
@empty
<tr>
    <td colspan="{{ ($canViewDetail || $canPrint) ? '5' : '4' }}" class="px-5 py-16 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-receipt text-2xl text-gray-300"></i>
            </div>
            <p class="text-sm font-medium text-gray-400">Belum ada transaksi pada periode ini</p>
        </div>
    </td>
</tr>
@endforelse