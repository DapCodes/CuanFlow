@if($testimonials->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50/50 text-gray-400 text-[10px] font-bold uppercase tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left">Pelanggan</th>
                    <th class="px-6 py-4 text-left">Rating & Ulasan</th>
                    <th class="px-6 py-4 text-left">Tanggal</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($testimonials as $testimonial)
                    <tr class="hover:bg-gray-50 transition-colors">
                        {{-- Pelanggan --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                @if(isset($testimonial->image) && $testimonial->image)
                                    <img src="{{ Storage::url($testimonial->image) }}" class="w-12 h-12 flex-shrink-0 aspect-square rounded-xl object-cover border-2 border-white shadow-sm transition-transform hover:scale-110">
                                @else
                                    <div class="w-12 h-12 flex-shrink-0 aspect-square rounded-xl bg-gradient-to-br from-cuan-green to-cuan-dark flex items-center justify-center border-2 border-white shadow-sm">
                                        <span class="text-white font-black text-xs">
                                            {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-gray-900 leading-tight">{{ $testimonial->name }}</div>
                                    <div class="text-[9px] font-black uppercase tracking-widest mt-1">
                                        @if($testimonial->type === 'product')
                                            <span class="text-orange-500">Produk: {{ $testimonial->product_name }}</span>
                                        @else
                                            <span class="text-gray-400">Layanan Umum</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        {{-- Rating & Ulasan --}}
                        <td class="px-6 py-5">
                            <div class="flex items-center text-yellow-400 text-[9px] mb-2 gap-0.5">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 text-[11px] leading-relaxed max-w-sm font-medium">{{ $testimonial->content }}</p>
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-6 py-5">
                            <div class="text-xs font-bold text-gray-900">{{ \Carbon\Carbon::parse($testimonial->created_at)->format('d M Y') }}</div>
                            <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">{{ \Carbon\Carbon::parse($testimonial->created_at)->format('H:i') }}</div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-5 whitespace-nowrap">
                            @if($testimonial->is_published)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-cuan-green/10 text-cuan-green border border-cuan-green/10">
                                    Publik
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-200">
                                    Hidden
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($testimonial->type === 'general')
                                    @can('aktifkan nonaktifkan testimoni')
                                    <form action="{{ route('testimonials.toggle-status', $testimonial->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="w-9 h-9 flex items-center justify-center rounded-xl transition-all active:scale-95 border {{ $testimonial->is_published ? 'bg-cuan-green/10 text-cuan-green border-cuan-green/20 hover:bg-cuan-green hover:text-white' : 'bg-gray-50 text-gray-400 border-gray-100 hover:bg-gray-100' }}"
                                                title="{{ $testimonial->is_published ? 'Sembunyikan' : 'Tampilkan Publik' }}">
                                            <i class="fas {{ $testimonial->is_published ? 'fa-eye' : 'fa-eye-slash' }} text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                @else
                                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-300 italic">Review Produk</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-gray-100 ajax-pagination">
            {{ $testimonials->appends(request()->query())->links() }}
        </div>
    </div>
@else
    <div class="p-20 text-center">
        <div class="w-20 h-20 bg-gray-50 border border-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="far fa-comments text-gray-200 text-3xl"></i>
        </div>
        <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Belum Ada Testimoni</h3>
        <p class="text-[11px] text-gray-500 font-bold uppercase tracking-widest mt-2 max-w-xs mx-auto">Tidak ada testimoni yang ditemukan untuk kriteria filter ini.</p>
    </div>
@endif
