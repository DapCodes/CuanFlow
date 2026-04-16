{{--
  Premium Category Card Component
  ─────────────────────────────────────────
--}}

@php
    $slug = $category->slug;
    $items = $category->featureItems;
    $itemCount = $items->count();

    $variant = match($slug) {
        'sales-pos'         => 'sell',
        'finance-cashflow'  => 'money',
        'product-inventory' => 'products',
        'ai-tools'          => 'ai',
        default             => 'more'
    };
    
    // Icon logic for category
    $catIconClass = $category->icon_value;
    if ($category->icon_type === 'phosphor' && !str_contains($catIconClass, 'ph-')) {
        $catIconClass = 'ph-bold ' . $catIconClass;
    } elseif ($category->icon_type === 'phosphor' && !str_contains($catIconClass, 'ph-bold')) {
        $catIconClass = 'ph-bold ' . $catIconClass;
    }
@endphp

<div class="main-card" onclick="openFolder('{{ $slug }}')">
  <div class="card-icon {{ $variant }}">
    @if($category->icon_type === 'image')
      <img src="{{ asset($category->icon_value) }}" class="w-8 h-8 object-contain" alt="">
    @else
      <i class="{{ $catIconClass }}"></i>
    @endif
  </div>

  <div class="card-content">
    <div class="card-title">{{ $category->name }}</div>
    <div class="card-desc">{{ $category->description }}</div>
  </div>

  <div class="card-meta">
    <div class="flex items-center gap-2">
       <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] text-slate-500">{{ $itemCount }} FITUR</span>
    </div>
    <div class="card-arrow">
      <i class="ph-bold ph-arrow-up-right"></i>
    </div>
  </div>
</div>

{{-- ═══ ULTRA PREMIUM DRAWER ═══ --}}
<div id="folder-{{ $slug }}"
     class="folder-overlay fixed inset-0 z-[100] hidden items-end sm:items-center justify-center"
     onclick="closeFolderOnBackdrop(event, '{{ $slug }}')">

  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md folder-backdrop"></div>

  <div class="drawer transform transition-all duration-500 folder-content">
    <div class="drawer-handle"></div>

    <div class="drawer-header">
      <div class="drawer-icon-wrap {{ $variant }}">
        @if($category->icon_type === 'image')
          <img src="{{ asset($category->icon_value) }}" class="w-10 h-10" alt="">
        @else
          <i class="{{ $catIconClass }}"></i>
        @endif
      </div>
      <div class="flex-1">
        <div class="drawer-title">{{ $category->name }}</div>
        <div class="drawer-desc">{{ $category->description }}</div>
      </div>
      <button type="button" onclick="closeFolder('{{ $slug }}')" class="close-btn">
        <i class="ph-bold ph-x"></i>
      </button>
    </div>

    <div class="drawer-items">
      @foreach($items as $item)
          @php
            $shouldRender = true;
            $needsFeatureCheck = !empty($item->feature_key);
          @endphp

          @if($shouldRender)
            @if($needsFeatureCheck)
              @canAccessFeature($item->feature_key)
                @include('components.menu.category-item', ['item' => $item])
              @endcanAccessFeature
            @else
                @include('components.menu.category-item', ['item' => $item])
            @endif
          @endif
      @endforeach
    </div>
  </div>
</div>
