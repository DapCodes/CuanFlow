@if(session('show_subscription_modal'))
    <div style="display: none !important;" 
         :style="{ display: show ? 'flex' : 'none' }"
         class="absolute inset-0 z-[35] flex items-center justify-center p-4 sm:p-6 bg-gray-950/80 backdrop-blur-md"
         x-data="{ show: true }"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
         
         <div class="bg-white rounded-3xl shadow-2xl w-full max-w-5xl max-h-full overflow-y-auto relative border border-white/20">
             
             <!-- Conditional Close Button -->
             @if(session('subscription_modal_reason') == 'locked_feature')
                <button @click="show = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-colors z-10">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
             @endif

             <div class="px-6 py-10 sm:p-12 text-center">
                 @if(session('subscription_modal_reason') == 'expired')
                     <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600">
                         <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                     </div>
                     <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Langganan Berakhir</h2>
                     <p class="text-gray-600 mb-10 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">Masa aktif langganan Anda telah habis. Perpanjang sekarang untuk mengembalikan akses penuh ke CuanFlow dan data Anda.</p>
                 
                 @elseif(session('subscription_modal_reason') == 'cancelled')
                     <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-600">
                         <i class="fa-solid fa-ban text-3xl"></i>
                     </div>
                     <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Langganan Nonaktif</h2>
                     <p class="text-gray-600 mb-10 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">Langganan Anda saat ini tidak aktif. Pilih paket di bawah ini untuk melanjutkan.</p>
                 
                 @elseif(session('subscription_modal_reason') == 'pending_verification')
                     <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 text-amber-600">
                         <i class="fa-solid fa-clock text-3xl"></i>
                     </div>
                     <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Menunggu Konfirmasi</h2>
                     <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">Permintaan uji coba gratis Anda sedang ditinjau oleh tim kami. Kami akan menghubungi Anda dalam 1x24 jam.</p>
                     
                     <div class="mb-6 flex flex-wrap items-center justify-center gap-4">
                         <button disabled class="inline-flex items-center gap-2 px-8 py-3 bg-gray-300 text-gray-500 font-bold rounded-xl cursor-not-allowed">
                             <i class="fa-solid fa-hourglass-half"></i>
                             Coba Gratis {{ \App\Models\SubscriptionSetting::getTrialDays() }} Hari
                         </button>
                     </div>
                     <p class="text-sm text-amber-600 mb-6"><i class="fa-solid fa-info-circle mr-1"></i> Atau pilih paket berbayar di bawah untuk akses langsung</p>
                 
                 @elseif(session('subscription_modal_reason') == 'no_subscription' || session('subscription_modal_reason') == 'user_requested')
                     <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-600">
                         <i class="fa-solid fa-rocket text-3xl"></i>
                     </div>
                     <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Selamat Datang di CuanFlow!</h2>
                     <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">Untuk memulai mengelola bisnis Anda dengan maksimal, silakan pilih paket langganan yang sesuai dengan kebutuhan Anda.</p>
                     
                     @if(!auth()->user()->subscriptions()->exists())
                       <div class="mb-6 flex flex-wrap items-center justify-center gap-4">
                           <a href="{{ route('subscription.trial-verification') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
                               <i class="fa-solid fa-gift"></i>
                               Coba Gratis {{ \App\Models\SubscriptionSetting::getTrialDays() }} Hari
                           </a>
                           <button id="modalReExploreTourBtn" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-gray-300 text-gray-600 hover:bg-gray-100 font-semibold rounded-xl transition-all">
                               <i class="fa-solid fa-compass"></i>
                               Jelajahi Lagi Fitur
                           </button>
                       </div>
                     @endif
                 
                 @else
                     <h2 class="text-2xl sm:text-4xl font-extrabold text-gray-900 mb-4 tracking-tight">Upgrade Paket</h2>
                     <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-base sm:text-lg leading-relaxed">Pilih paket yang sesuai untuk membuka fitur lebih banyak.</p>

                     @if(!auth()->user()->subscriptions()->exists())
                       <div class="mb-6 flex flex-wrap items-center justify-center gap-4">
                           <a href="{{ route('subscription.trial-verification') }}" class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold rounded-xl transition-all">
                               <i class="fa-solid fa-gift"></i>
                               Mulai Trial Gratis
                           </a>
                           <button id="modalReExploreTourBtn2" class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-gray-300 text-gray-600 hover:bg-gray-100 font-semibold rounded-xl transition-all">
                               <i class="fa-solid fa-compass"></i>
                               Jelajahi Lagi
                           </button>
                       </div>
                     @endif
                 @endif

                 <!-- Loading State -->
                 <div id="pricing-loader" class="flex justify-center py-12">
                      <i class="fa-solid fa-circle-notch fa-spin text-4xl text-indigo-500"></i>
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
