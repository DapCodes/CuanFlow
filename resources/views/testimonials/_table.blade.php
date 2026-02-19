@if($testimonials->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rating & Ulasan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @foreach($testimonials as $testimonial)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if(isset($testimonial->image) && $testimonial->image)
                                    <img src="{{ Storage::url($testimonial->image) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 font-bold border border-indigo-100">
                                        {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $testimonial->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-wider flex items-center gap-1 mt-0.5">
                                        @if($testimonial->type === 'product')
                                            <i class="fas fa-shopping-bag text-orange-400"></i>
                                            <span class="text-orange-600">Produk: {{ $testimonial->product_name }}</span>
                                        @else
                                            <i class="fas fa-store text-blue-400"></i>
                                            <span>Layanan Umum</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center text-yellow-400 text-[10px] mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? '' : 'text-gray-200' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-600 text-xs leading-relaxed max-w-sm">{{ $testimonial->content }}</p>
                        </td>

                        <td class="px-6 py-4 text-gray-500 text-[11px] font-medium">
                            {{ \Carbon\Carbon::parse($testimonial->created_at)->format('d M Y') }}
                            <div class="text-[9px] text-gray-400">{{ \Carbon\Carbon::parse($testimonial->created_at)->format('H:i') }}</div>
                        </td>

                        <td class="px-6 py-4">
                            @if($testimonial->is_published)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Publik
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-50 text-gray-700 border border-gray-200 uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Hidden
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                @if($testimonial->type === 'general')
                                    @can('aktifkan nonaktifkan testimoni')
                                    <form action="{{ route('testimonials.toggle-status', $testimonial->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border transition-all duration-200 shadow-sm {{ $testimonial->is_published ? 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' : 'border-gray-200 text-gray-400 hover:bg-gray-50' }}"
                                                title="{{ $testimonial->is_published ? 'Sembunyikan' : 'Tampilkan Publik' }}">
                                            <i class="fas {{ $testimonial->is_published ? 'fa-eye' : 'fa-eye-slash' }} text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan

                                    @can('hapus testimoni')
                                    <form action="{{ route('testimonials.destroy', $testimonial->id) }}" method="POST" onsubmit="return confirm('Hapus testimoni ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg border border-red-200 text-red-600 hover:bg-red-50 transition-all shadow-sm" title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                    @endcan
                                @else
                                    {{-- For Product Reviews, maybe just view link or simple delete if allowed --}}
                                    <span class="text-[10px] font-bold text-gray-400 italic">Review Produk</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-gray-100 ajax-pagination text-sm">
            {{ $testimonials->appends(request()->query())->links() }}
        </div>
    </div>
@else
    <div class="p-16 text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-100">
            <i class="far fa-comments text-gray-300 text-3xl"></i>
        </div>
        <h3 class="text-xl font-black text-gray-900 mb-2">Belum Ada Testimoni</h3>
        <p class="text-gray-500 text-sm max-w-xs mx-auto">Tidak ada testimoni yang ditemukan untuk kriteria filter ini.</p>
    </div>
@endif
