@extends('layouts.app')

@section('title','Jadwal Makanan Harian')

@push('styles')
<style>
  :root{ --cal-border:#e7eaf0; --cal-muted:#667085; --cal-bg:#f6f8fb; --cal-today:#2563eb; }

  .cal-wrap{max-width:1200px;margin:16px auto;padding:0 12px}
  .cal-toolbar{display:flex;gap:8px;align-items:center;margin-bottom:10px}
  .cal-title{margin:0 0 0 8px;font-size:16px}

  /* Header hari (desktop) */
  .cal-weekday{
    display:grid;grid-template-columns:repeat(7,1fr);
    color:var(--cal-muted);font-weight:600;font-size:.9rem;margin-bottom:4px;padding:0 6px
  }
  .cal-weekday>div{text-align:center}

  /* Grid kalender (desktop) */
  .cal-grid{
    display:grid;
    grid-template-columns:repeat(7,minmax(0,1fr));
    grid-auto-rows:140px;               /* tinggi tiap sel */
    border-left:1px solid var(--cal-border);
    border-top:1px solid var(--cal-border);
    border-radius:12px;
    overflow:hidden;
    background:#fff;
    box-shadow:0 4px 12px rgba(15,23,42,.08);
  }

  /* Sel hari */
  .cal-day{
    display:flex;                       /* penting utk scroll internal */
    flex-direction:column;
    min-width:0;
    min-height:140px;
    padding:.75rem;
    border-right:1px solid var(--cal-border);
    border-bottom:1px solid var(--cal-border);
    position:relative;
    background:#fff;
    overflow:hidden;                    /* cegah isi tumpah */
    transition:background .15s ease;
  }
  .cal-day:hover{background:#fbfcfe}

  .cal-daynum{font-size:.9rem;color:#94a3b8;font-weight:600}
  .cal-daynum .wk{display:none}         /* hari disembunyikan di desktop */
  .cal-out{background:#fafbff;color:#cbd5e1}
  .is-today .cal-daynum{color:var(--cal-today)}
  .is-today::after{
    content:"";position:absolute;top:.5rem;right:.5rem;width:.5rem;height:.5rem;border-radius:50%;
    background:var(--cal-today);box-shadow:0 0 0 4px rgba(37,99,235,.15)
  }

  /* Kontainer events (biar bisa shrink) */
  .cal-events{margin-top:.25rem;min-height:0}

  /* Header Paket (collapsible) */
  .cal-event{
    font-size:.82rem;border-radius:10px;padding:.4rem .6rem;margin-top:.35rem;
    background:#eef4ff;border:1px solid #dce6ff;color:#1d4ed8;
    cursor:pointer;display:flex;align-items:center;justify-content:space-between;gap:.5rem;
    user-select:none;
  }
  .cal-event .caret{font-size:.9em;opacity:.7;transition:transform .15s ease}
  .cal-event[aria-expanded="true"] .caret{transform:rotate(90deg)}

  /* Daftar isi paket — scroll internal */
  .cal-list{
    display:none;                       /* default hidden, di-toggle via JS */
    margin:.4rem 0 .15rem 1rem;
    padding-left:1rem;
    max-height:120px;                   /* << tinggi viewport internal */
    overflow-y:auto;
    overscroll-behavior:contain;
    -webkit-overflow-scrolling:touch;
  }
  .cal-list li{font-size:.82rem;margin:.15rem 0}

  /* Scrollbar halus (opsional) */
  .cal-list::-webkit-scrollbar{width:8px}
  .cal-list::-webkit-scrollbar-thumb{background:#cdd6e3;border-radius:8px}
  .cal-list:hover::-webkit-scrollbar-thumb{background:#b9c3d3}

  /* Mobile (≤640px) → list vertikal */
  @media (max-width: 640px){
    .cal-weekday{display:none}
    .cal-grid{
      grid-template-columns:1fr;grid-auto-rows:auto;border:none;border-radius:0;box-shadow:none;background:transparent;gap:10px
    }
    .cal-day{
      display:grid;grid-template-columns:120px 1fr;align-items:flex-start;
      border:1px solid var(--cal-border);border-radius:12px;padding:12px;background:#fff;
    }
    .cal-day:hover{background:#fff}
    .cal-daynum{color:#0f172a;font-weight:700}
    .cal-daynum .wk{display:inline;color:#94a3b8;font-weight:600;margin-left:.25rem}
    .cal-daynum .wk::before{content:", "}
    .cal-events{margin-top:0;padding-left:8px}
    .cal-list{max-height:180px}         /* sedikit lebih tinggi di mobile */
  }
</style>
@endpush



@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="cal-wrap">
    <div class="cal-toolbar">
      <button id="btnPrev" class="btn btn-outline-secondary btn-sm">‹</button>
      <button id="btnToday" class="btn btn-outline-secondary btn-sm">Today</button>
      <button id="btnNext" class="btn btn-outline-secondary btn-sm">›</button>
      <h5 id="title" class="cal-title"></h5>
    </div>

    <div class="cal-weekday">
      <div>Sun</div><div>Mon</div><div>Tue</div>
      <div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
    </div>

    <section id="grid" class="cal-grid"></section>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const gridEl   = document.getElementById('grid');
  const titleEl  = document.getElementById('title');
  const btnPrev  = document.getElementById('btnPrev');
  const btnNext  = document.getElementById('btnNext');
  const btnToday = document.getElementById('btnToday');

  let current = new Date(); // bulan aktif

  const pad = n => String(n).padStart(2,'0');
  const dateStr = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;

  // Matriks 6 minggu (42 hari)
  function getMonthMatrix(date, startMonday=false){
    const y = date.getFullYear(), m = date.getMonth();
    const first = new Date(y, m, 1);
    const start = new Date(first);
    const offset = startMonday ? (first.getDay()+6)%7 : first.getDay(); // minggu awal
    start.setDate(first.getDate() - offset);
    const arr = [];
    for(let i=0;i<42;i++){ const d=new Date(start); d.setDate(start.getDate()+i); arr.push(d); }
    return arr;
  }

  // ===== Dummy Paket (tanpa DB) =====
  const PAKET_SIANG = [
    'Nasi Merah',
    'Ayam Suwir Daun Kemangi',
    'Oseng Tempe Cabe Ijo',
    'Tumis Buncis Putren',
    'Buah Apel'
  ];
  const PAKET_MALEM = [
    'Potato Wedges',
    'Grilled Beef Brown Sauce',
    'Vegetable Salad',
    'Buah Jeruk'
  ];

  // helper: buat satu blok paket + list (toggle & scroll internal)
  function appendPackage(container, title, items, uid){
    const header = document.createElement('div');
    header.className = 'cal-event';
    header.setAttribute('role','button');
    header.setAttribute('aria-controls', uid);
    header.setAttribute('aria-expanded', 'false');
    header.innerHTML = `<span>${title}</span><span class="caret">›</span>`;

    const ul = document.createElement('ul');
    ul.className = 'cal-list';
    ul.id = uid;
    items.forEach(text=>{
      const li = document.createElement('li');
      li.textContent = text;
      ul.appendChild(li);
    });

    header.addEventListener('click', ()=>{
      // opsional: tutup saudara lainnya di tanggal yang sama
      const parent = header.parentElement;
      parent.querySelectorAll('.cal-list').forEach(x => { if (x !== ul) x.style.display = 'none'; });
      parent.querySelectorAll('.cal-event').forEach(h => { if (h !== header) h.setAttribute('aria-expanded','false'); });

      const open = ul.style.display === 'block';
      ul.style.display = open ? 'none' : 'block';
      header.setAttribute('aria-expanded', String(!open));
    });

    container.appendChild(header);
    container.appendChild(ul);
  }

  function renderGrid(){
    gridEl.innerHTML = '';
    titleEl.textContent = current.toLocaleDateString('en-US', {month:'long', year:'numeric'});
    const matrix = getMonthMatrix(current, /*startMonday*/ false);

    matrix.forEach(d=>{
      const isOut   = d.getMonth() !== current.getMonth();
      const isToday = dateStr(d) === dateStr(new Date());

      const cell = document.createElement('div');
      cell.className = `cal-day ${isOut?'cal-out':''} ${isToday?'is-today':''}`;

      const num = document.createElement('div');
      num.className = 'cal-daynum';
      const weekdayName = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][d.getDay()];
      num.innerHTML = `<span class="num">${d.getDate()}</span><span class="wk">${weekdayName}</span>`;

      const list = document.createElement('div');
      list.className = 'cal-events';

      if(!isOut){
        const ymd = dateStr(d).replaceAll('-','');
        appendPackage(list, 'Paket Siang', PAKET_SIANG, `siang-${ymd}`);
        appendPackage(list, 'Paket Malem', PAKET_MALEM, `malem-${ymd}`);
      }

      cell.appendChild(num);
      cell.appendChild(list);
      gridEl.appendChild(cell);
    });
  }

  // Navigasi
  btnPrev.onclick  = ()=>{ current.setMonth(current.getMonth()-1); renderGrid(); };
  btnNext.onclick  = ()=>{ current.setMonth(current.getMonth()+1); renderGrid(); };
  btnToday.onclick = ()=>{ current = new Date(); renderGrid(); };

  renderGrid();
});
</script>
@endpush


