@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Kelola Langganan')

@section('breadcrumb')
<li class="flex items-center text-sm">
    <span class="text-gray-400 mx-2">/</span>
    <span class="text-gray-900 font-medium tracking-tight">Kelola Langganan</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-[#f9fafb] shadow-sm md:shadow-none" x-data="{ activeTab: 'overview' }">
    <div class="max-w-6xl mx-auto space-y-8">
        
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="animate-fade-in-down">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Kelola Langganan</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Lihat status langganan, perpanjang durasi, atau upgrade tier akun Anda.</p>
            </div>
        </div>

        {{-- Main Layout Subgrid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Navigation - Sidebar --}}
            <aside class="lg:col-span-3 space-y-4">
                {{-- Desktop Sidebar --}}
                <nav class="hidden lg:flex flex-col gap-1.5 p-2 bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-chart-pie text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'overview' ? 'opacity-100' : ''"></i>
                        Ringkasan & Perpanjang
                    </button>
                    <button @click="activeTab = 'upgrade'" :class="activeTab === 'upgrade' ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'" class="flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all font-bold text-sm text-left group">
                        <i class="fas fa-rocket text-lg opacity-40 group-hover:opacity-100 transition-opacity" :class="activeTab === 'upgrade' ? 'opacity-100' : ''"></i>
                        Upgrade Tier
                    </button>
                </nav>

                {{-- Mobile Horizontal Tabs --}}
                <nav class="lg:hidden flex border border-gray-200 rounded-2xl bg-white p-1 overflow-x-auto no-scrollbar scroll-smooth shadow-sm">
                    <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-chart-pie"></i> Ringkasan
                    </button>
                    <button @click="activeTab = 'upgrade'" :class="activeTab === 'upgrade' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500'" class="flex-1 whitespace-nowrap px-4 py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-rocket"></i> Upgrade
                    </button>
                </nav>

                {{-- Help Card --}}
                <div class="hidden lg:block p-6 bg-gradient-to-br from-cuan-dark to-cuan-green rounded-2xl shadow-xl text-white relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-sm font-bold mb-2">Butuh Bantuan?</h4>
                        <p class="text-[11px] text-white/80 leading-relaxed font-medium mb-4">Hubungi tim support kami jika Anda memiliki pertanyaan mengenai tagihan.</p>
                        <a href="#" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-white/50 hover:text-white transition-colors">
                            Hubungi Kami <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                    <i class="fas fa-headset absolute -bottom-4 -right-4 text-7xl text-white/10"></i>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <div class="lg:col-span-9 space-y-6">
                
                {{-- Overview Tab --}}
                <section x-show="activeTab === 'overview'" class="animate-fade-in-up" x-cloak>
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-6">
                        <div class="p-6 md:p-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-cuan-yellow/20 text-cuan-dark flex items-center justify-center text-xl shadow-sm border border-cuan-yellow/50">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900">Status Langganan</h2>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">Informasi paket aktif dan masa berlaku Anda.</p>
                                </div>
                            </div>

                            {{-- Current Plan Card --}}
                            <div class="rounded-2xl bg-gradient-to-r from-gray-900 to-gray-800 text-white p-6 md:p-8 relative overflow-hidden shadow-lg">
                                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-5 rounded-full blur-3xl"></div>
                                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                                    <div>
                                        <div class="flex items-center space-x-2 text-cuan-yellow font-bold text-xs uppercase tracking-widest mb-2">
                                            <span class="px-2 py-0.5 rounded bg-white/10 border border-white/10">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                            <span>•</span>
                                            <span>{{ $currentTier->display_name }} Tier</span>
                                        </div>
                                        <h3 class="text-3xl font-black tracking-tight text-white mb-1">
                                            @php
                                                $now = now();
                                                $isTrial = $subscription->status === \App\Models\UserSubscription::STATUS_TRIAL;
                                                $expires = $isTrial 
                                                    ? \Carbon\Carbon::parse($subscription->trial_ends_at) 
                                                    : \Carbon\Carbon::parse($subscription->expires_at);
                                                
                                                $isExpired = $now->gt($expires);
                                                $diff = $now->diff($expires);
                                                
                                                $days = $diff->days;
                                                $hours = $diff->h;
                                                
                                                if ($isExpired) {
                                                    $remainingText = "Telah Berakhir";
                                                } elseif ($days > 0) {
                                                    $remainingText = "{$days} Hari {$hours} Jam";
                                                } else {
                                                    $remainingText = "{$hours} Jam Tersisa";
                                                }
                                            @endphp
                                            {{ $remainingText }}
                                        </h3>
                                        <p class="text-white/60 text-sm font-medium">
                                            Berakhir pada {{ $expires->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>
                                    <div class="md:text-right">
                                       <button @click="document.getElementById('extension-plans').scrollIntoView({behavior: 'smooth'})" class="px-6 py-3 bg-cuan-yellow text-cuan-dark font-black text-xs uppercase tracking-[0.2em] rounded-xl hover:bg-white transition-colors shadow-lg">
                                            Tambah Durasi
                                       </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Extension Plans --}}
                    <div id="extension-plans" class="space-y-4">
                        <div class="flex items-center justify-between px-2">
                            <h3 class="text-lg font-black text-gray-900 tracking-tight">Perpanjang Durasi</h3>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tier {{ $currentTier->display_name }}</span>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($extensionPlans as $plan)
                                <div class="bg-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg hover:border-cuan-green/30 transition-all group relative cursor-pointer"
                                    onclick="processExtension({{ $plan->id }}, {{ $plan->price }}, {{ $plan->duration_months }}, {{ $isTrial ? \Carbon\Carbon::parse($subscription->trial_ends_at)->timestamp : \Carbon\Carbon::parse($subscription->expires_at)->timestamp }})">
                                    
                                    @if($plan->discount_percentage > 0)
                                        <div class="absolute -top-3 -right-3 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded-lg shadow-lg rotate-12 group-hover:rotate-0 transition-transform">
                                             Hemat {{ number_format($plan->discount_percentage, 0) }}%
                                        </div>
                                    @endif

                                    <div class="absolute top-4 right-4 text-gray-300 group-hover:text-cuan-green transition-colors">
                                        <i class="fas fa-circle-plus text-xl"></i>
                                    </div>
                                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ $plan->duration_months }} Bulan</p>
                                    
                                    <div class="mb-4">
                                        @if($plan->discount_percentage > 0)
                                            <p class="text-xs text-gray-400 line-through font-medium">Rp {{ number_format($plan->original_price, 0, ',', '.') }}</p>
                                        @endif
                                        <p class="text-2xl font-black text-gray-900">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                                    </div>

                                    <button class="w-full py-3 rounded-xl bg-gray-50 text-gray-900 text-xs font-black uppercase tracking-widest group-hover:bg-cuan-dark group-hover:text-white transition-colors">
                                        Pilih Paket
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Upgrade Tab --}}
                <section x-show="activeTab === 'upgrade'" class="animate-fade-in-up" x-cloak>
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="p-6 md:p-8">
                             <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-sm border border-purple-100/50">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-black text-gray-900">Upgrade Tier</h2>
                                    <p class="text-xs text-gray-500 font-medium mt-0.5">Dapatkan fitur lebih lengkap dengan beralih ke tier yang lebih tinggi.</p>
                                </div>
                            </div>

                            @if($upgradeTiers->isEmpty())
                                <div class="text-center py-12 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                        <i class="fas fa-star text-2xl"></i>
                                    </div>
                                    <h3 class="text-gray-900 font-bold mb-1">Tier Tertinggi</h3>
                                    <p class="text-gray-500 text-sm">Anda sudah menggunakan paket terbaik kami.</p>
                                </div>
                            @else
                                <div class="space-y-6">
                                    @foreach($upgradeTiers as $tier)
                                        <div class="border border-gray-200 rounded-2xl overflow-hidden hover:shadow-md transition-shadow">
                                            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                                <h3 class="font-black text-gray-900 text-lg">{{ $tier->display_name }}</h3>
                                                <span class="bg-purple-100 text-purple-700 text-[10px] font-black uppercase px-2 py-1 rounded tracking-wider">Upgrade</span>
                                            </div>
                                            <div class="p-6">
                                                <p class="text-gray-600 text-sm mb-6 leading-relaxed">{{ $tier->description }}</p>
                                                
                                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                    @foreach($tier->plans as $plan)
                                                        <button onclick="confirmUpgrade({{ $plan->id }}, '{{ $tier->display_name }}', {{ $plan->price }}, {{ $plan->duration_months }}, {{ json_encode($tier->features->map(function($f){ return ['name' => $f->display_name ?? $f->name, 'description' => $f->description, 'icon' => $f->icon]; })) }})"
                                                            class="group relative flex flex-col p-4 border border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50/30 transition-all text-left">
                                                            
                                                            @if($plan->discount_percentage > 0)
                                                                <div class="absolute -top-2 -right-2 bg-purple-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded shadow-sm z-10">
                                                                    -{{ number_format($plan->discount_percentage, 0) }}%
                                                                </div>
                                                            @endif

                                                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider group-hover:text-purple-700">{{ $plan->duration_months }} Bulan</span>
                                                            
                                                            <div class="mt-1">
                                                                @if($plan->discount_percentage > 0)
                                                                    <span class="text-[10px] text-gray-400 line-through block leading-none">Rp {{ number_format($plan->original_price, 0, ',', '.') }}</span>
                                                                @endif
                                                                <span class="text-lg font-black text-gray-900">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                                                            </div>

                                                            <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity text-purple-600">
                                                                <i class="fa-solid fa-arrow-right"></i>
                                                            </div>
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</main>

@push('styles')
<style>
    /* Premium Animations (Copied from Profile) */
    @keyframes fade-in-up {
        0% { opacity: 0; transform: translateY(30px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-fade-in-up { animation: fade-in-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-down { animation: fade-in-down 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

    /* Hide Scrollbar for tabs */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    [x-cloak] { display: none !important; }
</style>
@endpush

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    // Helper to format currency
    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    }

    // Process Extension
    function processExtension(planId, price, duration, currentExpiresAtTimestamp) {
        const tax = price * 0.11;
        const total = price + tax;
        
        // Calculate new expiry date
        const currentExpiry = new Date(currentExpiresAtTimestamp * 1000);
        const now = new Date();
        
        // Logic for extension:
        // 1. If trial/active and still has time, start from current expiry (extend)
        // 2. If expired, start from now
        let startDate = (currentExpiry > now) ? currentExpiry : now;
        
        // Add months
        let newExpiry = new Date(startDate);
        newExpiry.setMonth(newExpiry.getMonth() + parseInt(duration));
        
        // Format date string
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        const newExpiryStr = newExpiry.toLocaleDateString('id-ID', options);

        Swal.fire({
            title: 'Konfirmasi Perpanjangan',
            html: `
                <div class="text-left space-y-3">
                    <p class="text-sm text-gray-500">Berikut adalah rincian pembayaran Anda:</p>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Harga Paket (${duration} Bulan)</span>
                            <span class="font-bold text-gray-900">${formatRupiah(price)}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">PPN (11%)</span>
                            <span class="font-bold text-gray-900">${formatRupiah(tax)}</span>
                        </div>
                        <div class="border-t border-gray-200 my-2"></div>
                        <div class="flex justify-between">
                            <span class="font-black text-gray-900 uppercase tracking-wide">Total Bayar</span>
                            <span class="font-black text-cuan-green text-lg">${formatRupiah(total)}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3 bg-blue-50 p-3 rounded-lg border border-blue-100 text-blue-800 text-xs">
                        <i class="fas fa-calendar-check mt-0.5"></i>
                        <div>
                            <span class="font-bold block mb-1">Estimasi Masa Aktif Baru:</span>
                            Langganan akan aktif hingga <span class="font-bold underline">${newExpiryStr}</span>.
                        </div>
                    </div>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#31694E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Bayar Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                callPaymentAPI('{{ route("subscription.manage.add-duration") }}', planId);
            }
        });
    }

    // Confirm Upgrade
    function confirmUpgrade(planId, tierName, price, duration, features) {
        const tax = price * 0.11;
        const total = price + tax;

        // Features list HTML
        let featuresHtml = '';
        if (features && features.length > 0) {
            featuresHtml = `
                <div class="mt-4 text-left bg-purple-50 p-4 rounded-xl border border-purple-100">
                    <p class="text-xs font-bold text-purple-800 mb-3 uppercase tracking-wide flex items-center gap-2">
                        <i class="fas fa-gift"></i> Fitur Unggulan Baru:
                    </p>
                    <ul class="space-y-3">`;
            
            features.forEach(feature => {
                const iconClass = feature.icon ? feature.icon : 'fas fa-check-circle';
                featuresHtml += `
                    <li class="flex items-start gap-3">
                        <div class="mt-0.5 w-5 h-5 rounded-full bg-purple-200 text-purple-700 flex items-center justify-center shrink-0">
                            <i class="${iconClass} text-[10px]"></i>
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-gray-900 leading-tight">${feature.name}</span>
                            ${feature.description ? `<span class="block text-xs text-gray-600 mt-0.5 leading-relaxed">${feature.description}</span>` : ''}
                        </div>
                    </li>`;
            });
            featuresHtml += `</ul></div>`;
        }

        Swal.fire({
            title: `Upgrade ke ${tierName}`,
            width: '600px',
            html: `
                <div class="text-left space-y-4">
                    <!-- Warning -->
                    <div class="bg-orange-50 p-4 rounded-xl border border-orange-200">
                        <p class="font-bold text-orange-800 mb-2 text-sm uppercase tracking-wide flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i> Perhatian Penting
                        </p>
                        <p class="text-xs text-orange-700 leading-relaxed font-medium">
                            Jika Anda melakukan upgrade sekarang, <b>sisa durasi paket Anda saat ini akan hangus</b> dan digantikan dengan paket baru <b>${tierName}</b> selama <b>${duration} Bulan</b>.
                        </p>
                    </div>

                    <!-- Payment Details -->
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 text-sm">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Paket ${tierName} (${duration} Bulan)</span>
                            <span class="font-bold text-gray-900">${formatRupiah(price)}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">PPN (11%)</span>
                            <span class="font-bold text-gray-900">${formatRupiah(tax)}</span>
                        </div>
                         <div class="border-t border-gray-200 my-2"></div>
                        <div class="flex justify-between">
                            <span class="font-black text-gray-900 uppercase tracking-wide">Total Bayar</span>
                            <span class="font-black text-cuan-green text-lg">${formatRupiah(total)}</span>
                        </div>
                    </div>

                    <!-- Features Preview -->
                    ${featuresHtml}
                </div>
            `,
            icon: null, // Custom HTML handles visuals
            showCancelButton: true,
            confirmButtonColor: '#31694E',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Upgrade & Bayar',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading();
                callPaymentAPI('{{ route("subscription.manage.upgrade") }}', planId);
            }
        });
    }

    function showLoading() {
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading() }
        });
    }

    function callPaymentAPI(url, planId) {
        @if(config('app.env') === 'production')
            Swal.fire({
                title: 'Informasi',
                text: 'Metode pembayaran Midtrans sedang dalam penanganan (maintenance). Silakan hubungi admin untuk aktivasi manual.',
                icon: 'info',
                customClass: { popup: 'rounded-3xl' }
            });
            return;
        @endif

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ plan_id: planId })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success && data.snap_token) {
                snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('subscription.payment.finish') }}";
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('subscription.payment.finish') }}";
                    },
                    onError: function(result) {
                        Swal.fire('Error', 'Pembayaran gagal', 'error');
                    },
                    onClose: function() {
                        // Do nothing
                    }
                });
            } else {
                Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
        });
    }
</script>
@endpush
@endsection
