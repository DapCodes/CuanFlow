<div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
    @foreach($tiers as $tier)
        <div class="border rounded-2xl p-6 relative flex flex-col hover:shadow-xl transition-all duration-300 {{ $tier->name === 'gold' ? 'border-indigo-500 ring-2 ring-indigo-500/20 shadow-lg' : 'border-gray-200' }}" x-data="{ descExpanded: false, featuresExpanded: false }">
            
            @if($tier->name === 'gold')
                <div class="absolute top-0 right-8 -translate-y-1/2 bg-gradient-to-r from-indigo-600 to-violet-600 text-white text-[10px] font-bold tracking-wider px-3 py-1 rounded-full shadow-lg shadow-indigo-200 uppercase">
                    Pilihan Utama
                </div>
            @endif

            <div class="mb-4">
                <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">{{ $tier->display_name }}</h3>
                <div class="mt-1.5 relative">
                    <p class="text-gray-500 text-sm leading-relaxed transition-all duration-300" 
                       :class="descExpanded ? '' : 'line-clamp-1'">
                        {{ $tier->description }}
                    </p>
                    @if(strlen($tier->description) > 40)
                        <button type="button" @click="descExpanded = !descExpanded" 
                                class="text-indigo-600 text-[10px] font-bold hover:text-indigo-800 transition-colors uppercase tracking-widest mt-1">
                            <span x-text="descExpanded ? 'Ringkas' : 'Selengkapnya...'"></span>
                        </button>
                    @endif
                </div>
            </div>

            <div class="mb-8">
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl font-black text-gray-900 tracking-tighter">
                        Rp {{ number_format($tier->price, 0, ',', '.') }}
                    </span>
                    <span class="text-gray-400 text-sm font-medium">/bulan</span>
                </div>
            </div>

            <ul class="space-y-3.5 mb-8 flex-1 text-left">
                <li class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-50 flex items-center justify-center">
                        <i class="fa-solid fa-check text-[10px] text-emerald-600"></i>
                    </div>
                    <span class="text-sm font-semibold text-gray-700">
                        {{ $tier->max_outlets ? $tier->max_outlets . ' Outlet' : 'Unlimited Outlet' }}
                    </span>
                </li>
                @php
                    $allFeatures = is_array($tier->features_list) ? array_values($tier->features_list) : [];
                    $initialFeatures = array_slice($allFeatures, 0, 4);
                    $remainingFeatures = array_slice($allFeatures, 4);
                @endphp

                @foreach($initialFeatures as $featureDisplay)
                    <li class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-50 flex items-center justify-center">
                            <i class="fa-solid fa-check text-[10px] text-emerald-600"></i>
                        </div>
                        <span class="text-sm text-gray-600 font-medium">{{ $featureDisplay }}</span>
                    </li>
                @endforeach

                @if(count($remainingFeatures) > 0)
                    <div x-show="featuresExpanded" x-collapse>
                        <div class="space-y-3.5 pt-3.5 border-t border-gray-50 mt-3.5">
                            @foreach($remainingFeatures as $featureDisplay)
                                <li class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-5 h-5 rounded-full bg-emerald-50 flex items-center justify-center">
                                        <i class="fa-solid fa-check text-[10px] text-emerald-600"></i>
                                    </div>
                                    <span class="text-sm text-gray-600 font-medium">{{ $featureDisplay }}</span>
                                </li>
                            @endforeach
                        </div>
                    </div>
                    
                    <button type="button" 
                        @click="featuresExpanded = !featuresExpanded" 
                        class="group flex items-center gap-2 text-indigo-600 text-[11px] font-bold hover:text-indigo-800 transition-colors uppercase tracking-widest mt-4"
                    >
                        <span x-text="featuresExpanded ? 'Tutup Fitur' : 'Liat Semua (+' + {{ count($remainingFeatures) }} + ')'"></span>
                        <i class="fa-solid transition-transform duration-300" :class="featuresExpanded ? 'fa-chevron-up rotate-180' : 'fa-chevron-down'"></i>
                    </button>
                @endif
            </ul>

            <div class="mt-auto pt-6 border-t border-gray-100">
                <form action="{{ route('subscription.select-plan') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2 px-1">Pilih Durasi Paket</label>
                        <div class="relative group">
                            <select name="plan_id" class="appearance-none w-full bg-gray-50 border-none text-sm font-bold text-gray-800 py-3.5 pl-4 pr-10 rounded-xl focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer">
                                @foreach($tier->plans as $plan)
                                    <option value="{{ $plan->id }}">
                                        {{ $plan->duration_months ?? '1' }} Bulan — Rp {{ number_format($plan->price, 0, ',', '.') }} 
                                        @if($plan->discount_percentage > 0)
                                            (Hemat {{ $plan->discount_percentage }}%)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-hover:text-indigo-500 transition-colors">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 px-6 rounded-xl font-bold text-sm tracking-wide transition-all shadow-lg hover:shadow-indigo-200 transform hover:-translate-y-0.5 active:translate-y-0 {{ $tier->name === 'gold' ? 'bg-gradient-to-r from-indigo-600 to-violet-600 text-white hover:brightness-110 shadow-indigo-200' : 'bg-gray-900 text-white hover:bg-black shadow-gray-200' }}">
                        Konfirmasi & Bayar
                    </button>
                </form>

                @if($tier->name === 'silver' && !auth()->user()->subscriptions()->exists())
                     <form action="{{ route('subscription.trial.request') }}" method="POST" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full py-2 text-[11px] font-bold text-gray-400 hover:text-indigo-600 transition-colors uppercase tracking-widest">
                            Atau Mulai Trial {{ \App\Models\SubscriptionSetting::getTrialDays() }} Hari
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>
