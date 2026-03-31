@extends('admin.layouts.app')

@section('title', 'Tiers Paket')
@section('page-title', 'Billing & Plan')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Subscription Tiers</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-crown text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Paket Berlangganan</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Kelola tingkatan paket berlangganan (Tiers) dan batasan fitur</p>
            </div>
        </div>
        <div>
                            <span>{{ $tier->subscriptions_count ?? 0 }} Pelanggan Aktif</span>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-between items-center">
                    <a href="{{ route('admin.subscription-tiers.edit', ['tier' => $tier->id]) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Edit Paket</a>
                    <!-- Custom delete form if needed or link -->
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
