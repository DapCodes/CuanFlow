{{--
  Premium Category Item (Inside Drawer)
  ─────────────────────────────────────────
--}}

@php
    $itemUrl = $item->url_type === 'route' ? route($item->url_value) : $item->url_value;
    $iconColor = $item->icon_bg_color ?? '#6366f1';
@endphp

<a href="{{ $itemUrl }}"
   class="ditem group"
   @if($item->open_in_new_tab) target="_blank" @endif
>
  <div class="ditem-icon" style="background-color: {{ $iconColor }}15; color: {{ $iconColor }};">
    @if($item->icon_type === 'image')
      <img src="{{ asset($item->icon_value) }}" class="w-6 h-6 object-contain" alt="">
    @else
      <i class="{{ $item->icon_class }} transition-transform duration-300 group-hover:scale-125"></i>
    @endif
    
    @if($item->is_highlight)
      <span class="absolute -top-1 -right-1 flex h-3 w-3">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
      </span>
    @endif
  </div>

  <div class="ditem-content">
    <div class="ditem-label">{{ $item->label }}</div>
    <div class="ditem-desc">{{ $item->description }}</div>
  </div>
</a>
