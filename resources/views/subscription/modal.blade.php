@if(session('show_subscription_modal'))
    <div style="display: none !important;" 
         :style="{ display: show ? 'flex' : 'none' }"
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-gray-900/90 backdrop-blur-sm"
         x-data="{ show: true }"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
         
         <div class="bg-white rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-y-auto relative">
             
             <!-- Conditional Close Button (Only if not strictly forced, but here we force it mostly) -->
             @if(session('subscription_modal_reason') == 'locked_feature')
                <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-2xl"></i>
                </button>
             @endif

             <div class="p-8 text-center">
                 @if(session('subscription_modal_reason') == 'expired')
                     <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 text-red-600">
                         <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                     </div>
                     <h2 class="text-3xl font-bold text-gray-900 mb-3">Langganan Berakhir</h2>
                     <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-lg">Masa aktif langganan Anda telah habis. Perpanjang sekarang untuk mengembalikan akses penuh ke CuanFlow dan data Anda.</p>
                 
                 @elseif(session('subscription_modal_reason') == 'cancelled')
                     <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-100 text-orange-600">
                         <i class="fa-solid fa-ban text-3xl"></i>
                     </div>
                     <h2 class="text-3xl font-bold text-gray-900 mb-3">Langganan Nonaktif</h2>
                     <p class="text-gray-600 mb-8 max-w-2xl mx-auto text-lg">Langganan Anda saat ini tidak aktif. Pilih paket di bawah ini untuk melanjutkan.</p>
                 
                 @elseif(session('subscription_modal_reason') == 'no_subscription')
                     <div class="mb-4 inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-600">
                         <i class="fa-solid fa-rocket text-3xl"></i>
                     </div>
                     <h2 class="text-3xl font-bold text-gray-900 mb-3">Selamat Datang di CuanFlow!</h2>
                     <p class="text-gray-600 mb-6 max-w-2xl mx-auto text-lg">Untuk memulai mengelola bisnis Anda dengan maksimal, silakan pilih paket langganan yang sesuai dengan kebutuhan Anda.</p>
                     
                     @if(!auth()->user()->subscriptions()->exists())
                       <div class="mb-8">
                           <a href="{{ route('subscription.trial-verification') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-200 transform hover:-translate-y-0.5">
                               <i class="fa-solid fa-gift"></i>
                               Coba Gratis {{ \App\Models\SubscriptionSetting::getTrialDays() }} Hari
                           </a>
                       </div>
                     @endif
                 
                 @else
                     <h2 class="text-3xl font-bold text-gray-900 mb-3">Upgrade Paket</h2>
                     <p class="text-gray-600 mb-6 max-w-2xl mx-auto text-lg">Pilih paket yang sesuai untuk membuka fitur lebih banyak.</p>

                     @if(!auth()->user()->subscriptions()->exists())
                       <div class="mb-8">
                           <a href="{{ route('subscription.trial-verification') }}" class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold rounded-xl transition-all">
                               <i class="fa-solid fa-gift"></i>
                               Mulai Trial Gratis
                           </a>
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
        });
    </script>
@endif
