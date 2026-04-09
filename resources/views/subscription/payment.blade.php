@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Pembayaran Langganan')

@section('content')
    <div class="max-w-3xl mx-auto py-12 px-4">
        <div class="mb-8">
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-900 transition-colors font-semibold group text-sm">
                <div
                    class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center group-hover:bg-gray-50 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </div>
                Kembali ke Dashboard
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-8 border-b border-gray-100">
                <h1 class="text-2xl font-bold text-gray-900">Konfirmasi Pembayaran</h1>
                <p class="text-gray-500 mt-1">Selesaikan pembayaran untuk mengaktifkan paket Anda.</p>
            </div>

            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900">{{ $plan->tier->display_name }}
                            ({{ $plan->duration_months ?? 1 }} Bulan)</h3>
                        <p class="text-sm text-gray-500">{{ $plan->tier->description }}</p>
                    </div>
                    <div class="text-right">
                        <span class="block text-2xl font-bold text-indigo-600">Rp
                            {{ number_format($amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Pajak (PPN 11%)</span>
                        <span class="font-medium">Rp {{ number_format($tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-gray-100 pt-4">
                        <span class="text-gray-900">Total Tagihan</span>
                        <span class="text-indigo-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-8">
                    <button id="pay-button"
                        class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
                        Bayar Sekarang via Midtrans
                    </button>
                    <div class="flex justify-center items-center mt-4 space-x-2 text-gray-400">
                        <i class="fa-solid fa-lock text-xs"></i>
                        <span class="text-xs">Pembayaran aman & terenkripsi oleh Midtrans</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script type="text/javascript"
            src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                var payButton = document.getElementById('pay-button');
                if (payButton) {
                    payButton.addEventListener('click', function () {
                        @if(config('app.env') === 'productions')
                            Swal.fire({
                                title: 'Informasi',
                                text: 'Metode pembayaran Midtrans sedang dalam penanganan (maintenance). Silakan hubungi admin untuk aktivasi manual.',
                                icon: 'info',
                                customClass: { popup: 'rounded-3xl' }
                            });
                            return;
                        @endif

                        window.snap.pay('{{ $snapToken }}', {
                            onSuccess: function (result) {
                                window.location.href = "{{ route('subscription.payment.finish') }}";
                            },
                            onPending: function (result) {
                                alert("Menunggu pembayaran!");
                            },
                            onError: function (result) {
                                window.location.href = "{{ route('subscription.payment.error') }}";
                            },
                            onClose: function () {
                                // Optional handling
                            }
                        });
                    });
                }
            });
        </script>
    @endpush
@endsection