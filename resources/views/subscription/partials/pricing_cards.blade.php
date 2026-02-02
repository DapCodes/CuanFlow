<div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
    @foreach($tiers as $tier)
        <div class="border rounded-xl p-6 relative flex flex-col hover:shadow-lg transition-shadow {{ $tier->name === 'gold' ? 'border-indigo-500 ring-1 ring-indigo-500 shadow-md' : 'border-gray-200' }}">
            
            @if($tier->name === 'gold')
                <div class="absolute top-0 right-0 bg-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-xl">
                    POPULER
                </div>
            @endif

            <h3 class="text-xl font-bold text-gray-900">{{ $tier->display_name }}</h3>
            <p class="text-gray-500 text-sm mt-1 mb-4 h-10">{{ $tier->description }}</p>

            <div class="mb-6">
                <span class="text-3xl font-bold text-gray-900">
                    Rp {{ number_format($tier->price, 0, ',', '.') }}
                </span>
                <span class="text-gray-500">/ bulan</span>
            </div>

            <ul class="space-y-3 mb-4 flex-1 text-left" x-data="{ expanded: false }">
                <li class="flex items-start">
                    <i class="fa-solid fa-check text-emerald-500 mt-1 mr-2"></i>
                    <span class="text-sm text-gray-600">
                        {{ $tier->max_outlets ? $tier->max_outlets . ' Outlet' : 'Unlimited Outlet' }}
                    </span>
                </li>
                @php
                    $allFeatures = is_array($tier->features_list) ? array_values($tier->features_list) : [];
                    $initialFeatures = array_slice($allFeatures, 0, 4);
                    $remainingFeatures = array_slice($allFeatures, 4);
                @endphp

                @foreach($initialFeatures as $featureDisplay)
                    <li class="flex items-start">
                        <i class="fa-solid fa-check text-emerald-500 mt-1 mr-2"></i>
                        <span class="text-sm text-gray-600">{{ $featureDisplay }}</span>
                    </li>
                @endforeach

                @if(count($remainingFeatures) > 0)
                    <div x-show="expanded" x-collapse>
                        @foreach($remainingFeatures as $featureDisplay)
                            <li class="flex items-start mt-3">
                                <i class="fa-solid fa-check text-emerald-500 mt-1 mr-2"></i>
                                <span class="text-sm text-gray-600">{{ $featureDisplay }}</span>
                            </li>
                        @endforeach
                    </div>
                    
                    <button type="button" 
                        @click="expanded = !expanded" 
                        class="text-indigo-600 text-xs font-bold hover:underline mt-2 flex items-center"
                    >
                        <span x-text="expanded ? 'Lihat Sedikit' : 'Lihat Semua (+' + {{ count($remainingFeatures) }} + ')'"></span>
                        <i class="fa-solid ms-1" :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                @endif
            </ul>

            <div class="mt-auto space-y-3">
                <form action="{{ route('subscription.select-plan') }}" method="POST">
                    @csrf
                    <!-- Default to 1 month plan for now, or user can select duration in next step/dropdown -->
                    <!-- Ideally show a dropdown for duration -->
                    <div class="mb-3">
                        <select name="plan_id" class="w-full text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($tier->plans as $plan)
                                <option value="{{ $plan->id }}">
                                    {{ $plan->duration_months ?? '1' }} Bulan - Rp {{ number_format($plan->price, 0, ',', '.') }} 
                                    @if($plan->discount_percentage > 0)
                                        (Hemat {{ $plan->discount_percentage }}%)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 rounded-lg font-semibold transition-colors {{ $tier->name === 'gold' ? 'bg-indigo-600 text-white hover:bg-indigo-700' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}">
                        Pilih Paket
                    </button>
                </form>

                @if($tier->name === 'silver' && !auth()->user()->subscriptions()->exists())
                     <form action="{{ route('subscription.trial.request') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2 px-4 text-sm text-indigo-600 font-medium hover:text-indigo-800 underline">
                            Coba Gratis {{ \App\Models\SubscriptionSetting::getTrialDays() }} Hari
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach
</div>
