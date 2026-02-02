@extends('admin.layouts.app')

@section('title', 'Permintaan Trial')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Permintaan Trial</h1>
            <p class="text-gray-500 mt-1">Verifikasi pengajuan uji coba gratis dari pengguna baru.</p>
        </div>
        
        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg p-1 flex space-x-1 border border-gray-200">
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'pending']) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium transition {{ $status == 'pending' ? 'bg-indigo-100 text-indigo-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
               Menunggu
            </a>
            <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'approved']) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium transition {{ $status == 'approved' ? 'bg-green-100 text-green-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
               Disetujui
            </a>
             <a href="{{ route('admin.subscription-trial-requests.index', ['status' => 'rejected']) }}" 
               class="px-4 py-2 rounded-md text-sm font-medium transition {{ $status == 'rejected' ? 'bg-red-100 text-red-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50' }}">
               Ditolak
            </a>
        </div>
    </div>

    <!-- Trial Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User / Outlet</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Bisnis</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti Foto</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requests as $req)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <span class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($req->user->name ?? 'U', 0, 1) }}
                                    </span>
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
