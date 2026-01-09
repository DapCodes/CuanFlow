@component('mail::message')
# Permintaan Penarikan Saldo Baru

Terdapat permintaan penarikan saldo baru yang perlu diproses.

**Detail Permintaan:**

| Informasi | Detail |
|:----------|:-------|
| Nama User | {{ $user->name }} |
| Email | {{ $user->email }} |
| Outlet | {{ $outlet?->name ?? 'N/A' }} |
| Jumlah Penarikan | Rp {{ number_format($withdrawal->amount, 0, ',', '.') }} |
| Pajak ({{ $withdrawal->tax_percent }}%) | Rp {{ number_format($withdrawal->tax_amount, 0, ',', '.') }} |
| **Total Transfer** | **Rp {{ number_format($withdrawal->net_amount, 0, ',', '.') }}** |
| Bank / E-Wallet | {{ $withdrawal->payment_method }} |
| No. Rekening | {{ $withdrawal->account_number }} |
| Atas Nama | {{ $withdrawal->account_name }} |
| Tanggal Pengajuan | {{ $withdrawal->created_at->format('d M Y H:i') }} |

@component('mail::button', ['url' => route('admin.withdrawals.show', $withdrawal->id)])
Lihat Detail
@endcomponent

Silakan segera proses permintaan ini.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
