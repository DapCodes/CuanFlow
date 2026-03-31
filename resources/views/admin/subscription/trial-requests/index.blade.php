@extends('admin.layouts.app')

@section('title', 'Permintaan Trial')
@section('page-title', 'Verifikasi Bisnis')

@section('breadcrumb')
<li class="flex items-center gap-2">
    <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
    <span class="text-emerald-600 font-semibold tracking-wide text-sm">Permintaan Trial</span>
</li>
@endsection

@section('content')
<div class="px-4 lg:px-6 space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100/50">
                <i class="fas fa-id-card-clip text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight uppercase">Permintaan Trial</h1>
                <p class="text-sm text-gray-500 mt-0.5 font-medium italic">Verifikasi pendaftaran akun bisnis dan aktivasi uji coba gratis</p>
            </div>
        </div>
        
        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl p-1.5 flex gap-1.5 border border-gray-200 shadow-sm">
            @php
                $tabClasses = "px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95 whitespace-nowrap";
            @endphp
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'pending']) }}" 
               class="{{ $tabClasses }} {{ $status == 'pending' ? 'bg-amber-100 text-amber-700 shadow-sm shadow-amber-100/50' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Menunggu
            </a>
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'approved']) }}" 
               class="{{ $tabClasses }} {{ $status == 'approved' ? 'bg-emerald-100 text-emerald-700 shadow-sm shadow-emerald-100/50' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Disetujui
            </a>
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'rejected']) }}" 
               class="{{ $tabClasses }} {{ $status == 'rejected' ? 'bg-rose-100 text-rose-700 shadow-sm shadow-rose-100/50' : 'text-gray-400 hover:bg-gray-50 hover:text-gray-600' }}">
               Ditolak
            </a>
        </div>
    </div>

                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $req->user->name ?? 'Unknown User' }}</div>
                                    <div class="text-sm text-gray-500">{{ $req->outlet_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                            {{ $req->business_type ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $req->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex space-x-2">
                                @if($req->photo_store_front_path)
                                    <a href="{{ Storage::url($req->photo_store_front_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">Depan</a>
                                @endif
                                @if($req->photo_products_path)
                                    <a href="{{ Storage::url($req->photo_products_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">Produk</a>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $req->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                  ($req->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.subscription-trial-requests.show', $req) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            Tidak ada permintaan trial saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
