@component('mail::message')
# Status Penarikan Saldo Anda

Halo {{ $user->name }},

@if($status === 'approved')
Permintaan penarikan saldo Anda telah **disetujui** oleh admin.

Pembayaran akan segera diproses dan dikirimkan ke rekening Anda.
@elseif($status === 'rejected')
Mohon maaf, permintaan penarikan saldo Anda telah **ditolak**.

@if($withdrawal->admin_note)
**Alasan:** {{ $withdrawal->admin_note }}
@endif

Silakan hubungi admin jika Anda memiliki pertanyaan.
@elseif($status === 'paid')
Pembayaran penarikan saldo Anda telah **berhasil dikirim**.

Dana telah ditransfer sesuai dengan metode pembayaran yang Anda pilih.
@endif

**Detail Penarikan:**

| Informasi | Detail |
|:----------|:-------|
| Jumlah Penarikan | Rp {{ number_format($withdrawal->amount, 0, ',', '.') }} |
| Pajak ({{ $withdrawal->tax_percent }}%) | Rp {{ number_format($withdrawal->tax_amount, 0, ',', '.') }} |
| **Total Diterima** | **Rp {{ number_format($withdrawal->net_amount, 0, ',', '.') }}** |
| Bank / E-Wallet | {{ $withdrawal->payment_method }} |
| No. Rekening | {{ $withdrawal->account_number }} |
| Atas Nama | {{ $withdrawal->account_name }} |
| Status | **{{ $status == 'paid' ? 'BERHASIL' : ($status == 'approved' ? 'DISETUJUI' : 'DITOLAK') }}** |

@if($withdrawal->processed_at)
| Waktu Proses | {{ $withdrawal->processed_at->format('d M Y H:i') }} |
@endif

Terima kasih telah menggunakan layanan kami.

Salam,<br>
{{ config('app.name') }}
@endcomponent
