@php
    $preferredLayout = $_COOKIE['app_layout'] ?? 'grid';
@endphp
@extends($preferredLayout === 'sidebar' ? 'layouts.app-sidebar' : 'layouts.app')

@section('title', 'Insight - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center text-sm">
  <span class="text-gray-400 mx-2">/</span>
  <span class="text-gray-900 font-bold tracking-tight">AI Insights</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-4 sm:py-8 px-3 sm:px-4 bg-gray-50">
  <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

    {{-- HEADER HALAMAN --}}
    <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-xl md:text-2xl font-black text-gray-900">
          Insight Clara AI
        </h1>
        <p class="mt-1 text-sm text-gray-500">
          Statistik cerdas dan rekomendasi otomatis untuk performa bisnis Anda.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button id="btnToday"
          class="inline-flex items-center gap-2 rounded-xl bg-cuan-green px-5 py-3 text-sm font-black text-white hover:bg-cuan-dark transition-all shadow-lg shadow-cuan-green/20 active:scale-95">
          <i class="fas fa-calendar-day"></i>
          <span>Lihat Hari Ini</span>
        </button>
      </div>
    </section>

    {{-- Layout: Calendar + Legend / Quick stats --}}
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
      {{-- Calendar --}}
      <div class="lg:col-span-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
          <div class="bg-gray-50 px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
              <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Kalender Insight</h2>
              <p class="text-[10px] text-gray-500 font-medium">U: UNREAD • R: READ • D: DISMISSED</p>
            </div>
            <div class="text-[10px] font-black text-cuan-green uppercase tracking-widest">
              Klik Tanggal Untuk Detail
            </div>
          </div>

          <div class="p-3 sm:p-4">
            <div id="calendar" class="fc-theme-standard"></div>
          </div>
        </div>
      </div>

      {{-- Side panel: legend (simple) --}}
      <div class="lg:col-span-4">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
          <h3 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-4">Legenda Status</h3>

          <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 border border-emerald-100">
              <span class="text-[10px] font-black text-emerald-800 uppercase tracking-tight">Belum Dibaca (U)</span>
              <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
              <span class="text-[10px] font-black text-gray-600 uppercase tracking-tight">Sudah Dibaca (R)</span>
              <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-white border border-gray-100">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-tight">Diabaikan (D)</span>
              <span class="w-2.5 h-2.5 rounded-full border-2 border-slate-200"></span>
            </div>
          </div>

          <div class="mt-4 text-xs text-gray-500 leading-relaxed">
            Tips: kalau kamu ingin semua insight cepat "rapi", gunakan tombol "Tandai semua sudah dibaca" di modal tanggal yang dipilih.
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

{{-- Modal: Daily insights - IMPROVED RESPONSIVE --}}
<div id="dailyModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

  <div class="relative mx-auto w-full h-full sm:h-auto max-w-3xl px-2 sm:px-4 py-4 sm:py-0 sm:top-6 md:top-10 lg:top-14 flex items-start sm:items-center justify-center">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl border border-gray-200 overflow-hidden w-full max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-4rem)] flex flex-col">
      
      {{-- Modal Header --}}
      <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between gap-4 flex-shrink-0 bg-gray-50/50">
        <div class="flex-1">
          <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Detail Insight AI</p>
          <div id="modalDate" class="text-lg font-black text-gray-900 uppercase tracking-tighter">-</div>
          <div id="modalCounts" class="text-[10px] font-bold text-cuan-green mt-1 bg-cuan-green/10 px-3 py-1 rounded-full w-fit uppercase tracking-tighter">-</div>
        </div>
        <button id="btnCloseModal" class="text-gray-400 hover:text-gray-600 transition-colors">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      {{-- Tabs --}}
      <div class="px-5 py-3 border-b border-gray-100 flex-shrink-0 bg-white">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
          <button data-tab="all" class="tabBtn px-4 py-2 text-[10px] font-black rounded-xl bg-cuan-green text-white uppercase tracking-widest transition-all">Semua</button>
          <button data-tab="unread" class="tabBtn px-4 py-2 text-[10px] font-black rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 uppercase tracking-widest transition-all">Belum Dibaca</button>
          <button data-tab="read" class="tabBtn px-4 py-2 text-[10px] font-black rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 uppercase tracking-widest transition-all">Sudah Dibaca</button>
          <button data-tab="dismissed" class="tabBtn px-4 py-2 text-[10px] font-black rounded-xl bg-gray-50 text-gray-400 hover:bg-gray-100 uppercase tracking-widest transition-all">Dismiss</button>

          <div class="hidden sm:flex ml-auto">
            @can('tandai semua ai insight dibaca')
            <button id="btnMarkAllRead"
              class="px-4 py-2 text-[10px] font-black rounded-xl bg-cuan-green text-white uppercase tracking-widest hover:bg-cuan-dark transition-all shadow-lg shadow-emerald-100">
              Tandai Semua Dibaca
            </button>
            @endcan
          </div>
        </div>
      </div>

      {{-- Mark All Read Button for Mobile --}}
      <div class="sm:hidden px-5 py-3 border-b border-gray-100 bg-white">
        @can('tandai semua ai insight dibaca')
        <button id="btnMarkAllReadMobile"
          class="w-full py-3 text-[10px] font-black rounded-xl bg-cuan-green text-white uppercase tracking-widest shadow-lg shadow-emerald-100">
          Tandai Semua Sudah Dibaca
        </button>
        @endcan
      </div>

      {{-- Modal Body --}}
      <div class="flex-1 overflow-y-auto p-3 sm:p-5">
        <div id="modalEmpty" class="hidden text-center py-8 sm:py-10 text-gray-500 text-sm">
          Tidak ada insight di tanggal ini.
        </div>

        <div id="modalList" class="space-y-2.5 sm:space-y-3"></div>
      </div>

      {{-- Modal Footer --}}
      <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end bg-gray-50/50">
        <button id="btnCloseModal2" class="text-xs font-black text-gray-400 hover:text-gray-600 uppercase tracking-widest">
          Tutup Panel
        </button>
      </div>
    </div>
  </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">

<style>
  /* Seragam: ungu minimal */
  #calendar {
    --fc-border-color: #f3f4f6;
    --fc-today-bg-color: rgba(16, 185, 129, .05); /* emerald soft */
  }

  .fc .fc-toolbar-title {
    font-size: .75rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: .02em;
  }

  @media (min-width: 640px) {
    .fc .fc-toolbar-title {
      font-size: .9rem;
    }
  }

  .fc .fc-button {
    border-radius: .5rem;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    box-shadow: none;
    padding: .25rem .45rem;
    font-size: .7rem;
  }

  @media (min-width: 640px) {
    .fc .fc-button {
      padding: .35rem .6rem;
      font-size: .8rem;
    }
  }

  .fc .fc-button:hover { background: #f3f4f6; color:#111827; }
  .fc .fc-today-button { display:none !important; }

  /* Event badge inside day */
  .cf-badge {
    display:inline-flex;
    align-items:center;
    gap:.25rem;
    padding:.1rem .25rem;
    border-radius: 9999px;
    font-size: .6rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
  }

  @media (min-width: 640px) {
    .cf-badge {
      gap:.35rem;
      padding:.15rem .35rem;
      font-size: .7rem;
    }
  }

  .cf-unread { background: rgba(16, 185, 129, .1); color: #059669; border: 1px solid rgba(16, 185, 129, .2); }
  .cf-read { background: #f9fafb; color:#6b7280; border: 1px solid #f3f4f6; }
  .cf-dismiss { background: #fff; color:#94a3b8; border: 1px dashed #e2e8f0; }

  /* Responsive calendar day cells */
  .fc-daygrid-day-number {
    font-size: .75rem;
    padding: .25rem;
  }

  @media (min-width: 640px) {
    .fc-daygrid-day-number {
      font-size: .875rem;
      padding: .5rem;
    }
  }

  /* Modal scroll improvements for mobile */
  @media (max-width: 639px) {
    #dailyModal .overflow-x-auto::-webkit-scrollbar {
      height: 4px;
    }
    
    #dailyModal .overflow-x-auto::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    #dailyModal .overflow-x-auto::-webkit-scrollbar-thumb {
      background: #cbd5e1;
      border-radius: 10px;
    }
  }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const routes = {
    summary: `{{ route('ai-insights.calendar.summary') }}`,
    daily: `{{ route('ai-insights.calendar.daily') }}`,
    markRead: (id) => `{{ route('ai-insights.mark-read', ':id') }}`.replace(':id', id),
    dismiss: (id) => `{{ route('ai-insights.dismiss', ':id') }}`.replace(':id', id),
    markAllRead: `{{ route('ai-insights.mark-all-read') }}`,
  };

  const permissions = {
      canMarkRead: @can('tandai ai insight dibaca') true @else false @endcan,
      canDismiss: @can('abaikan ai insight') true @else false @endcan,
      canMarkAllRead: @can('tandai semua ai insight dibaca') true @else false @endcan
  };

  const modal = document.getElementById('dailyModal');
  const modalDateEl = document.getElementById('modalDate');
  const modalCountsEl = document.getElementById('modalCounts');
  const modalList = document.getElementById('modalList');
  const modalEmpty = document.getElementById('modalEmpty');
  const btnClose = document.getElementById('btnCloseModal');
  const btnClose2 = document.getElementById('btnCloseModal2');
  const btnToday = document.getElementById('btnToday');
  const btnMarkAllRead = document.getElementById('btnMarkAllRead');
  const btnMarkAllReadMobile = document.getElementById('btnMarkAllReadMobile');

  let selectedDate = null;
  let currentTab = 'all';
  let lastDailyPayload = null;

  function fmtDateId(dateStr){
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('id-ID', { day:'numeric', month:'long', year:'numeric' });
  }

  function showModal(){ modal.classList.remove('hidden'); document.body.style.overflow='hidden'; }
  function hideModal(){ modal.classList.add('hidden'); document.body.style.overflow=''; }

  btnClose.addEventListener('click', hideModal);
  btnClose2.addEventListener('click', hideModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) hideModal(); });
  document.addEventListener('keydown', (e)=>{ if(!modal.classList.contains('hidden') && e.key==='Escape') hideModal(); });

  function pillSeverity(sev){
    if(sev==='critical') return 'bg-red-50 text-red-700 border-red-100';
    if(sev==='warning') return 'bg-amber-50 text-amber-700 border-amber-100';
    return 'bg-emerald-50 text-emerald-700 border-emerald-100';
  }

  function statusText(item){
    if(item.is_dismissed) return 'Dismiss';
    if(item.is_read) return 'Sudah dibaca';
    return 'Belum dibaca';
  }

  function matchesTab(item){
    if(currentTab==='all') return true;
    if(currentTab==='dismissed') return item.is_dismissed;
    if(currentTab==='read') return (!item.is_dismissed && item.is_read);
    if(currentTab==='unread') return (!item.is_dismissed && !item.is_read);
    return true;
  }

  function renderList(payload){
    lastDailyPayload = payload;
    modalDateEl.textContent = fmtDateId(payload.date);
    modalCountsEl.textContent =
      `Total ${payload.counts.total} • Unread ${payload.counts.unread} • Read ${payload.counts.read} • Dismiss ${payload.counts.dismissed}`;

    const items = payload.insights.filter(matchesTab);

    if(!payload.insights.length){
      modalEmpty.classList.remove('hidden');
      modalList.innerHTML = '';
      return;
    }

    modalEmpty.classList.add('hidden');

    if(!items.length){
      modalList.innerHTML = `<div class="text-center py-8 sm:py-10 text-gray-500 text-sm">Tidak ada insight untuk filter ini.</div>`;
      return;
    }

    modalList.innerHTML = items.map(i => `
      <div class="border border-gray-200 rounded-lg sm:rounded-xl p-3 sm:p-4 hover:bg-gray-50 transition">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
          <div class="flex-1 min-w-0">
            <div class="text-xs text-gray-500">${i.time}</div>
            <div class="font-semibold text-gray-900 text-sm sm:text-base break-words">${escapeHtml(i.title)}</div>
            <div class="mt-1.5 sm:mt-1 text-xs flex flex-wrap items-center gap-2">
              <span class="px-2 py-1 rounded-full border ${pillSeverity(i.severity)} text-[10px] sm:text-[11px] font-semibold">${i.severity.toUpperCase()}</span>
              <span class="text-gray-600">${statusText(i)}</span>
            </div>
          </div>
          <div class="flex flex-row sm:flex-row gap-2 flex-shrink-0">
            ${(!i.is_dismissed && !i.is_read && permissions.canMarkRead) ? `
              <button class="px-3 py-1.5 text-[10px] font-black rounded-lg bg-cuan-green/10 text-cuan-green border border-cuan-green/20 hover:bg-cuan-green/20 uppercase tracking-tighter whitespace-nowrap transition-all"
                onclick="markRead(${i.id})">Tandai Dibaca</button>` : ``}
            ${(!i.is_dismissed && permissions.canDismiss) ? `
              <button class="px-3 py-1.5 text-[10px] font-black rounded-lg bg-gray-50 text-gray-400 border border-gray-200 hover:bg-gray-100 uppercase tracking-tighter whitespace-nowrap transition-all"
                onclick="dismissInsight(${i.id})">Abaikan</button>` : ``}
          </div>
        </div>
        <div class="mt-2.5 sm:mt-3 text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words">
          ${escapeHtml(i.content)}
        </div>
      </div>
    `).join('');
  }

  function escapeHtml(str){
    return (str ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  async function loadDaily(dateStr){
    selectedDate = dateStr;
    currentTab = 'all';
    setActiveTab();

    showModal();
    modalList.innerHTML = `<div class="py-8 sm:py-10 text-center text-gray-500 text-sm"><i class="fas fa-circle-notch fa-spin mr-2"></i>Memuat...</div>`;
    modalEmpty.classList.add('hidden');

    const res = await fetch(`${routes.daily}?date=${dateStr}`, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
    const payload = await res.json();
    renderList(payload);
  }

  function setActiveTab(){
    document.querySelectorAll('.tabBtn').forEach(btn=>{
      const tab = btn.getAttribute('data-tab');
      const active = tab === currentTab;
      btn.classList.toggle('bg-cuan-green', active);
      btn.classList.toggle('text-white', active);
      btn.classList.remove('bg-purple-50', 'text-purple-700', 'border-purple-100'); // Clean up old classes if any
      btn.classList.toggle('bg-gray-50', !active);
      btn.classList.toggle('text-gray-400', !active);
      btn.classList.toggle('bg-white', false); // Ensure this is explicitly managed
    });
  }

  document.querySelectorAll('.tabBtn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      currentTab = btn.getAttribute('data-tab');
      setActiveTab();
      if(lastDailyPayload) renderList(lastDailyPayload);
    });
  });

  async function postJson(url){
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'X-Requested-With':'XMLHttpRequest',
        'Accept':'application/json',
      },
      body: JSON.stringify({})
    });
    const data = await res.json().catch(()=> ({}));
    if(!res.ok || data.success === false){
      throw new Error(data.message || `HTTP ${res.status}`);
    }
    return data;
  }

  window.markRead = async function(id){
    try{
      await postJson(routes.markRead(id));
      if(selectedDate) await refreshDailyAndCalendar();
    }catch(e){
      console.error(e);
      showToast('error', e.message || 'Gagal menandai dibaca');
    }
  }

  window.dismissInsight = async function(id){
    Swal.fire({
      title: 'Abaikan insight ini?',
      text: 'Insight ini tidak akan muncul lagi di daftar utama.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#10b981',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Abaikan',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      customClass: {
          container: 'rounded-2xl',
          popup: 'rounded-2xl',
          confirmButton: 'rounded-xl font-bold px-6 py-3',
          cancelButton: 'rounded-xl font-bold px-6 py-3'
      }
    }).then(async (result) => {
      if (result.isConfirmed) {
        try{
          await postJson(routes.dismiss(id));
          if(selectedDate) await refreshDailyAndCalendar();
          showToast('success', 'Insight berhasil diabaikan.');
        }catch(e){
          console.error(e);
          showToast('error', e.message || 'Gagal mengabaikan insight');
        }
      }
    });
  }

  // Mark all read untuk desktop dan mobile
  const markAllReadHandler = async () => {
    try{
      await postJson(routes.markAllRead);
      if(selectedDate) await refreshDailyAndCalendar();
    }catch(e){
      console.error(e);
      showToast('error', e.message || 'Gagal menandai semua dibaca');
    }
  };

  btnMarkAllRead.addEventListener('click', markAllReadHandler);
  btnMarkAllReadMobile.addEventListener('click', markAllReadHandler);

  let calendar = null;
  async function refreshDailyAndCalendar(){
    // reload daily
    const res = await fetch(`${routes.daily}?date=${selectedDate}`, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
    const payload = await res.json();
    renderList(payload);

    // refresh calendar events
    calendar.refetchEvents();
  }

  function renderEventContent(arg){
    const p = arg.event.extendedProps || {};
    const unread = p.unread || 0;
    const read = p.read || 0;
    const dismissed = p.dismissed || 0;

    // tampilkan badge kecil (minimal)
    const parts = [];
    if(unread) parts.push(`<span class="cf-badge cf-unread">U ${unread}</span>`);
    if(read) parts.push(`<span class="cf-badge cf-read">R ${read}</span>`);
    if(dismissed) parts.push(`<span class="cf-badge cf-dismiss">D ${dismissed}</span>`);

    return { html: `<div class="flex flex-wrap gap-1 mt-1">${parts.join('')}</div>` };
  }

  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'id',
      firstDay: 1,
      height: 'auto',
      selectable: true,
      fixedWeekCount: false,
      headerToolbar: { left: 'prev', center: 'title', right: 'next' },

      events: async (info, success, failure) => {
        try{
          const url = `/ai-insights/calendar/summary?start=${encodeURIComponent(info.startStr)}&end=${encodeURIComponent(info.endStr)}`;
          const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
          const events = await res.json();
          success(events);
        }catch(e){
          console.error(e);
          failure(e);
        }
      },

      eventContent: renderEventContent,

      dateClick: (info) => loadDaily(info.dateStr),
    });

    calendar.render();

    // Fix for calendar layout issue on initial load
    window.addEventListener('load', () => {
        setTimeout(() => {
            calendar.updateSize();
        }, 500);
    });

    btnToday.addEventListener('click', ()=>{
      const today = new Date().toISOString().slice(0,10);
      calendar.today();
      loadDaily(today);
    });
  });
</script>
@endpush
@endsection