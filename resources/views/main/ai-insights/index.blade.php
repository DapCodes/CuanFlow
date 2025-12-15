@extends('layouts.app')

@section('title', 'Insight - ' . (auth()->user()->outlet->name ?? 'CuanFlow'))

@section('breadcrumb')
<li class="flex items-center">
  <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
  </svg>
  <span class="text-gray-900 font-medium">Insight</span>
</li>
@endsection

@section('content')
<main class="flex-grow py-8 px-4 bg-gray-50">
  <div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <section class="bg-white border border-gray-200 rounded-xl shadow-sm px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-xl md:text-2xl font-semibold text-gray-900 flex items-center gap-2">
          <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-purple-50 text-purple-600 border border-purple-100">
            <i class="fas fa-lightbulb text-sm"></i>
          </span>
          <span>Insight Clara AI</span>
        </h1>
        <p class="mt-1 text-sm text-gray-500">
          Klik tanggal di kalender untuk melihat insight. Ada status Belum Dibaca, Sudah Dibaca, dan Dismiss.
        </p>
      </div>

      <div class="flex items-center gap-3 justify-start md:justify-end">
        <button id="btnToday"
          class="inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-violet-400 to-purple-600 px-4 py-2 text-xs font-semibold text-white hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-purple-300 focus:ring-offset-1">
          Hari Ini
        </button>
      </div>
    </section>

    {{-- Layout: Calendar + Legend / Quick stats --}}
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      {{-- Calendar --}}
      <div class="lg:col-span-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
          <div class="border-b border-gray-200 px-5 py-4 flex items-center justify-between">
            <div>
              <h2 class="text-sm font-semibold text-gray-900">Kalender Insight</h2>
              <p class="text-xs text-gray-500 mt-0.5">Indikator: U=Unread, R=Read, D=Dismissed</p>
            </div>
            <div class="text-xs text-gray-500">
              Klik tanggal untuk detail
            </div>
          </div>

          <div class="p-4">
            <div id="calendar" class="fc-theme-standard"></div>
          </div>
        </div>
      </div>

      {{-- Side panel: legend (simple) --}}
      <div class="lg:col-span-4">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5">
          <h3 class="text-sm font-semibold text-gray-900 mb-3">Legenda</h3>

          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between p-3 rounded-lg bg-purple-50 border border-purple-100">
              <span class="text-gray-700">Unread (U)</span>
              <span class="text-purple-700 font-semibold">badge ungu</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-gray-100">
              <span class="text-gray-700">Read (R)</span>
              <span class="text-gray-700 font-semibold">badge abu</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 border border-slate-200">
              <span class="text-gray-700">Dismissed (D)</span>
              <span class="text-slate-700 font-semibold">badge garis</span>
            </div>
          </div>

          <div class="mt-4 text-xs text-gray-500 leading-relaxed">
            Tips: kalau kamu ingin semua insight cepat “rapi”, gunakan tombol “Tandai semua sudah dibaca” di modal tanggal yang dipilih.
          </div>
        </div>
      </div>
    </section>
  </div>
</main>

{{-- Modal: Daily insights --}}
<div id="dailyModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

  <div class="relative mx-auto w-full max-w-3xl px-4 top-10 sm:top-14">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
      {{-- Modal Header --}}
      <div class="px-5 py-4 border-b border-gray-200 flex items-start justify-between gap-4">
        <div>
          <div class="text-xs text-gray-500">Insight tanggal</div>
          <div id="modalDate" class="text-lg font-bold text-gray-900">-</div>
          <div id="modalCounts" class="text-xs text-gray-500 mt-1">-</div>
        </div>
        <button id="btnCloseModal" class="text-gray-400 hover:text-gray-700">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      {{-- Tabs --}}
      <div class="px-5 py-3 border-b border-gray-200 flex flex-wrap gap-2 items-center">
        <button data-tab="all" class="tabBtn px-3 py-1.5 text-xs font-semibold rounded-lg bg-purple-50 text-purple-700 border border-purple-100">Semua</button>
        <button data-tab="unread" class="tabBtn px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">Belum Dibaca</button>
        <button data-tab="read" class="tabBtn px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">Sudah Dibaca</button>
        <button data-tab="dismissed" class="tabBtn px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-700 border border-gray-200 hover:bg-gray-50">Dismiss</button>

        <div class="ml-auto flex gap-2">
          <button id="btnMarkAllRead"
            class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-gradient-to-r from-violet-400 to-purple-600 text-white hover:opacity-95">
            Tandai semua sudah dibaca
          </button>
        </div>
      </div>

      {{-- Modal Body --}}
      <div class="p-5">
        <div id="modalEmpty" class="hidden text-center py-10 text-gray-500">
          Tidak ada insight di tanggal ini.
        </div>

        <div id="modalList" class="space-y-3 max-h-[60vh] overflow-y-auto pr-1"></div>
      </div>

      {{-- Modal Footer --}}
      <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-between">
        <a href="{{ route('ai-insights.index') }}"
           class="text-sm font-semibold text-purple-700 hover:text-purple-800">
          Lihat halaman ini
        </a>
        <button id="btnCloseModal2" class="text-sm font-semibold text-gray-700 hover:text-gray-900">
          Tutup
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
    --fc-border-color: #e5e7eb;
    --fc-today-bg-color: rgba(167, 139, 250, .16); /* violet soft */
  }

  .fc .fc-toolbar-title {
    font-size: .9rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: .02em;
  }

  .fc .fc-button {
    border-radius: .5rem;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #6b7280;
    box-shadow: none;
    padding: .35rem .6rem;
    font-size: .8rem;
  }
  .fc .fc-button:hover { background: #f3f4f6; color:#111827; }
  .fc .fc-today-button { display:none !important; }

  /* Event badge inside day */
  .cf-badge {
    display:inline-flex;
    align-items:center;
    gap:.35rem;
    padding:.15rem .35rem;
    border-radius: 9999px;
    font-size: .7rem;
    font-weight: 800;
    line-height: 1;
    white-space: nowrap;
  }
  .cf-unread { background: rgba(167,139,250,.18); color: #5b21b6; border: 1px solid rgba(167,139,250,.35); }
  .cf-read { background: #f3f4f6; color:#374151; border: 1px solid #e5e7eb; }
  .cf-dismiss { background: #fff; color:#334155; border: 1px dashed #cbd5e1; }
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

  const modal = document.getElementById('dailyModal');
  const modalDateEl = document.getElementById('modalDate');
  const modalCountsEl = document.getElementById('modalCounts');
  const modalList = document.getElementById('modalList');
  const modalEmpty = document.getElementById('modalEmpty');
  const btnClose = document.getElementById('btnCloseModal');
  const btnClose2 = document.getElementById('btnCloseModal2');
  const btnToday = document.getElementById('btnToday');
  const btnMarkAllRead = document.getElementById('btnMarkAllRead');

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
    return 'bg-purple-50 text-purple-700 border-purple-100';
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
      modalList.innerHTML = `<div class="text-center py-10 text-gray-500 text-sm">Tidak ada insight untuk filter ini.</div>`;
      return;
    }

    modalList.innerHTML = items.map(i => `
      <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="text-xs text-gray-500">${i.time}</div>
            <div class="font-semibold text-gray-900">${escapeHtml(i.title)}</div>
            <div class="mt-1 text-xs inline-flex items-center gap-2">
              <span class="px-2 py-1 rounded-full border ${pillSeverity(i.severity)} text-[11px] font-semibold">${i.severity.toUpperCase()}</span>
              <span class="text-gray-600">${statusText(i)}</span>
            </div>
          </div>
          <div class="flex gap-2">
            ${(!i.is_dismissed && !i.is_read) ? `
              <button class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-purple-50 text-purple-700 border border-purple-100 hover:bg-purple-100"
                onclick="markRead(${i.id})">Tandai dibaca</button>` : ``}
            ${(!i.is_dismissed) ? `
              <button class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-white text-gray-700 border border-gray-200 hover:bg-gray-50"
                onclick="dismissInsight(${i.id})">Dismiss</button>` : ``}
          </div>
        </div>
        <div class="mt-3 text-sm text-gray-700 leading-relaxed whitespace-pre-line">
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
    modalList.innerHTML = `<div class="py-10 text-center text-gray-500 text-sm"><i class="fas fa-circle-notch fa-spin mr-2"></i>Memuat...</div>`;
    modalEmpty.classList.add('hidden');

    const res = await fetch(`${routes.daily}?date=${dateStr}`, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
    const payload = await res.json();
    renderList(payload);
  }

  function setActiveTab(){
    document.querySelectorAll('.tabBtn').forEach(btn=>{
      const tab = btn.getAttribute('data-tab');
      const active = tab === currentTab;
      btn.classList.toggle('bg-purple-50', active);
      btn.classList.toggle('text-purple-700', active);
      btn.classList.toggle('border-purple-100', active);
      btn.classList.toggle('bg-white', !active);
      btn.classList.toggle('text-gray-700', !active);
      btn.classList.toggle('border-gray-200', !active);
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
      alert(e.message || 'Gagal menandai dibaca');
    }
  }

  window.dismissInsight = async function(id){
    if(!confirm('Yakin ingin dismiss insight ini?')) return;
    try{
      await postJson(routes.dismiss(id));
      if(selectedDate) await refreshDailyAndCalendar();
    }catch(e){
      console.error(e);
      alert(e.message || 'Gagal dismiss insight');
    }
  }

  btnMarkAllRead.addEventListener('click', async ()=>{
    try{
      await postJson(routes.markAllRead);
      if(selectedDate) await refreshDailyAndCalendar();
    }catch(e){
      console.error(e);
      alert(e.message || 'Gagal menandai semua dibaca');
    }
  });

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

    btnToday.addEventListener('click', ()=>{
      const today = new Date().toISOString().slice(0,10);
      calendar.today();
      loadDaily(today);
    });
  });
</script>
@endpush
@endsection
