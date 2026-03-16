@if(session('show_subscription_modal'))
    <div style="display: none !important;" 
         :style="{ display: show ? 'flex' : 'none' }"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-gray-950/60 backdrop-blur-sm"
         x-data="{ show: true }"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
         
         <div class="bg-white rounded-xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-y-auto relative border border-gray-100 animate-fade-in-up">
             
             <!-- Conditional Close Button -->
             @if(session('subscription_modal_reason') == 'locked_feature')
                <button @click="show = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-colors z-10">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
             @endif

             <div class="px-6 py-12 sm:p-16 text-center">
                 @if(session('subscription_modal_reason') == 'expired')
                     <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-red-50 text-red-600 shadow-sm border border-red-100">
                         <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                     </div>
                     <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-4 tracking-tighter uppercase">Langganan Berakhir</h2>
                     <p class="text-gray-500 mb-10 max-w-2xl mx-auto text-sm sm:text-base font-medium leading-relaxed">Masa aktif langganan Anda telah habis. Perpanjang sekarang untuk mengembalikan akses penuh ke fitur premium CuanFlow.</p>
                 
                 @elseif(session('subscription_modal_reason') == 'cancelled')
                     <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gray-50 text-gray-400 shadow-sm border border-gray-100">
                         <i class="fa-solid fa-ban text-3xl"></i>
                     </div>
                     <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-4 tracking-tighter uppercase">Langganan Nonaktif</h2>
                     <p class="text-gray-500 mb-10 max-w-2xl mx-auto text-sm sm:text-base font-medium leading-relaxed">Langganan Anda saat ini tidak aktif. Pilih paket di bawah ini untuk melanjutkan operasional bisnis Anda.</p>
                 
                @elseif(session('subscription_modal_reason') == 'pending_verification')
                      <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gray-50 text-cuan-green shadow-sm border border-gray-100">
                          <i class="fa-solid fa-clock text-3xl"></i>
                      </div>
                      <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-4 tracking-tighter uppercase">Menunggu Verifikasi</h2>
                      <p class="text-gray-500 mb-8 max-w-2xl mx-auto text-sm sm:text-base font-medium leading-relaxed">Permintaan uji coba gratis Anda sedang ditinjau. Kami akan memberikan notifikasi dalam waktu maksimal 24 jam.</p>
                      
                      <div class="mb-6 flex flex-wrap items-center justify-center gap-4">
                          <button disabled class="inline-flex items-center gap-2 px-8 py-4 bg-gray-100 text-gray-400 text-xs font-black uppercase tracking-widest rounded-xl cursor-not-allowed border border-gray-200">
                              <i class="fa-solid fa-hourglass-half"></i>
                              Sedang Diproses
                          </button>
                      </div>
                      <p class="text-[10px] font-black uppercase tracking-widest text-cuan-green mb-6 bg-emerald-50 py-2 px-4 rounded-full inline-block">Atau pilih paket berbayar di bawah untuk akses instan</p>
                  
                 @elseif(session('subscription_modal_reason') == 'trial_rejected')
                      <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-red-50 text-red-600 shadow-sm border border-red-100">
                          <i class="fa-solid fa-circle-xmark text-3xl"></i>
                      </div>
                      <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-4 tracking-tighter uppercase">Uji Coba Ditolak</h2>
                      <p class="text-gray-500 mb-6 max-w-2xl mx-auto text-sm sm:text-base font-medium leading-relaxed">Maaf, permohonan uji coba gratis Anda tidak dapat disetujui saat ini.</p>
                      
                      @if(session('subscription_rejection_notes'))
                         <div class="bg-red-50 border border-red-100 rounded-xl p-5 mb-8 max-w-lg mx-auto text-left">
                             <h4 class="text-[10px] font-black uppercase text-red-800 tracking-widest mb-2 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i> Alasan Penolakan
                             </h4>
                             <p class="text-xs text-red-600 font-medium leading-relaxed">{{ session('subscription_rejection_notes') }}</p>
                         </div>
                      @endif

                      @if(session('subscription_retry_available'))
                         <div class="mb-10">
                             <a href="{{ route('subscription.trial-verification') }}" class="inline-flex items-center gap-2 px-8 py-4 bg-cuan-green text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-100 hover:bg-cuan-dark active:scale-95">
                                 <i class="fa-solid fa-rotate-right"></i>
                                 Ajukan Trial Kembali
                             </a>
                         </div>
                      @elseif(session('subscription_retry_wait_time'))
                         <div class="mb-10">
                              <button disabled class="inline-flex items-center gap-2 px-8 py-4 bg-gray-100 text-gray-400 text-xs font-black uppercase tracking-widest rounded-xl cursor-not-allowed border border-gray-200">
                                 <i class="fa-solid fa-clock"></i>
                                 Tunggu {{ session('subscription_retry_wait_time') }}
                             </button>
                         </div>
                      @endif

                      <p class="text-gray-500 mb-10 max-w-2xl mx-auto text-sm sm:text-base font-medium leading-relaxed">Silakan pilih paket langganan di bawah ini untuk mulai menggunakan CuanFlow.</p>

                  
                 @elseif(session('subscription_modal_reason') == 'no_subscription' || session('subscription_modal_reason') == 'user_requested')
                      <div class="mb-6 inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-emerald-50 text-cuan-green shadow-sm border border-emerald-100">
                          <i class="fa-solid fa-rocket text-3xl"></i>
                      </div>
                      <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-4 tracking-tighter uppercase">Wujudkan Bisnis Impian</h2>
                      <p class="text-gray-500 mb-10 max-w-2xl mx-auto text-sm sm:text-base font-medium leading-relaxed">Kelola outlet Anda dengan lebih cerdas dan efisien. Pilih paket langganan yang paling sesuai untuk pertumbuhan bisnis Anda.</p>
                      
                      @if(!auth()->user()->subscriptions()->exists())
                        <div class="mb-10 flex flex-wrap items-center justify-center gap-4">
                            <a href="{{ route('subscription.trial-verification') }}" class="inline-flex items-center gap-3 px-10 py-4 bg-cuan-green text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-emerald-100 hover:bg-cuan-dark active:scale-95">
                                <i class="fa-solid fa-gift text-sm"></i>
                                Coba Gratis {{ \App\Models\SubscriptionSetting::getTrialDays() }} Hari
                            </a>
                            <button id="modalReExploreTourBtn" class="inline-flex items-center gap-3 px-8 py-4 border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 text-xs font-black uppercase tracking-widest rounded-xl transition-all">
                                <i class="fa-solid fa-compass text-sm"></i>
                                Jelajahi Fitur
                            </button>
                        </div>
                      @endif
                  
                 @else
                      <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-4 tracking-tighter uppercase">Upgrade Performa Bisnis</h2>
                      <p class="text-gray-500 mb-10 max-w-2xl mx-auto text-sm sm:text-base font-medium leading-relaxed">Tingkatkan efisiensi outlet Anda dengan fitur-fitur premium eksklusif CuanFlow.</p>

                      @if(!auth()->user()->subscriptions()->exists())
                        <div class="mb-10 flex flex-wrap items-center justify-center gap-4">
                            <a href="{{ route('subscription.trial-verification') }}" class="inline-flex items-center gap-3 px-8 py-4 border border-cuan-green text-cuan-green hover:bg-cuan-green hover:text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                <i class="fa-solid fa-gift"></i>
                                Mulai Trial Gratis
                            </a>
                            <button id="modalReExploreTourBtn2" class="inline-flex items-center gap-3 px-8 py-4 border border-gray-200 text-gray-400 hover:text-gray-900 hover:bg-gray-50 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">
                                <i class="fa-solid fa-compass"></i>
                                Jelajahi Fitur
                            </button>
                        </div>
                      @endif
                  @endif

                  <!-- Loading State -->
                  <div id="pricing-loader" class="flex flex-col items-center justify-center py-20 grayscale opacity-40">
                       <i class="fa-solid fa-circle-notch fa-spin text-4xl text-cuan-green mb-4"></i>
                       <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Menyiapkan Paket...</p>
                  </div>

                  <!-- Content Container -->
                  <div id="pricing-container" class="hidden opacity-0 transition-opacity duration-500">
                       <!-- Loaded via AJAX -->
                  </div>
              </div>
         </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load pricing content
            fetch('{{ route('subscription.index') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const container = document.getElementById('pricing-container');
                const loader = document.getElementById('pricing-loader');
                
                container.innerHTML = html;
                
                // Fade effect
                loader.classList.add('hidden');
                container.classList.remove('hidden');
                requestAnimationFrame(() => {
                    container.classList.remove('opacity-0');
                });
            })
            .catch(err => {
                console.error('Failed to load pricing:', err);
                document.getElementById('pricing-loader').innerHTML = '<p class="text-red-500">Gagal memuat paket. Silakan refresh halaman.</p>';
            });

            // Re-explore tour buttons
            const reExploreBtns = document.querySelectorAll('#modalReExploreTourBtn, #modalReExploreTourBtn2');
            reExploreBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Set localStorage flag to show welcome modal
                    localStorage.setItem('cuanflow_show_welcome', '1');
                    // Remove onboarding completed flag so modal shows again if needed
                    localStorage.removeItem('cuanflow_onboarding_completed');
                    
                    // Clear the subscription modal session via AJAX first
                    fetch('{{ route("subscription.clear-modal") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    }).finally(() => {
                        // Reload the page after clearing session
                        window.location.reload();
                    });
                });
            });
        });
    </script>
@endif
