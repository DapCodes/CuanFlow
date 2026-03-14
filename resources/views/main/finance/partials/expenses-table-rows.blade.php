@forelse($expenses as $expense)
<tr class="hover:bg-gray-50/50 transition-all duration-200 group border-b border-gray-50 last:border-0">
    <td class="px-5 py-4">
        <div class="flex flex-col gap-1.5">
            <span class="text-xs font-semibold text-gray-900 leading-tight">{{ Str::limit($expense->description, 45) }}</span>
            <div class="flex items-center gap-2 flex-wrap">
                @if($expense->amount < 0)
                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-emerald-700 uppercase bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md">
                        <i class="fas fa-arrow-up text-[8px]"></i>
                        Pemasukan
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-[9px] font-bold text-red-700 uppercase bg-red-50 border border-red-200 px-2 py-0.5 rounded-md">
                        <i class="fas fa-arrow-down text-[8px]"></i>
                        Pengeluaran
                    </span>
                @endif
                <span class="text-[9px] font-medium text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md">
                    {{ $expense->category->name ?? 'Lainnya' }}
                </span>
                <span class="text-[10px] text-gray-400 font-medium">
                    <i class="far fa-calendar text-[9px] mr-1"></i>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                </span>
            </div>
        </div>
    </td>
    <td class="px-5 py-4">
        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-gray-700 bg-white border border-gray-200 px-2.5 py-1 rounded-md">
            @if($expense->payment_method == 'cash')
                <i class="fas fa-money-bill-wave text-[9px] text-emerald-600"></i>
            @elseif($expense->payment_method == 'qris')
                <i class="fas fa-qrcode text-[9px] text-blue-600"></i>
            @elseif($expense->payment_method == 'transfer')
                <i class="fas fa-credit-card text-[9px] text-purple-600"></i>
            @else
                <i class="fas fa-wallet text-[9px] text-gray-600"></i>
            @endif
            {{ $expense->payment_method == 'cash' ? 'Tunai' : ($expense->payment_method == 'qris' ? 'QRIS' : ucfirst($expense->payment_method)) }}
        </span>
    </td>
    <td class="px-5 py-4 text-right">
        @if($expense->amount < 0)
            <span class="text-sm font-black text-emerald-600">+ Rp {{ number_format(abs($expense->amount), 0, ',', '.') }}</span>
        @else
            <span class="text-sm font-black text-red-600">- Rp {{ number_format($expense->amount, 0, ',', '.') }}</span>
        @endif
    </td>
    <td class="px-5 py-4">
        <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-200">
            @php
                $editPermission = $expense->amount < 0 ? 'edit pemasukan' : 'edit pengeluaran';
                $deletePermission = $expense->amount < 0 ? 'hapus pemasukan' : 'hapus pengeluaran';
            @endphp
            
            @can($editPermission)
            <a href="{{ route('expenses.edit', $expense->id) }}" 
               class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50 rounded-lg shadow-sm transition-all duration-200" 
               title="Edit">
               <i class="fas fa-edit text-[10px]"></i>
            </a>
            @endcan
            
            @can($deletePermission)
            <form action="{{ route('finance.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center justify-center w-7 h-7 bg-white border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-300 hover:bg-red-50 rounded-lg shadow-sm transition-all duration-200" 
                        title="Hapus">
                   <i class="fas fa-trash text-[10px]"></i>
                </button>
            </form>
            @endcan
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-5 py-16 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-file-invoice text-2xl text-gray-300"></i>
            </div>
            <p class="text-sm font-medium text-gray-400">Belum ada data pengeluaran atau pemasukan</p>
        </div>
    </td>
</tr>
@endforelse