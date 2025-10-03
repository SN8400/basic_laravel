@extends('layouts.menu')

@section('content')
@csrf

<style>
  #global-loader{ position:fixed; inset:0; z-index:9999; display:none; display:flex; align-items:center; justify-content:center; background:rgba(17,24,39,.55); backdrop-filter:saturate(120%) blur(2px); }
  .loader-wrap{display:flex;flex-direction:column;align-items:center;gap:.75rem}
  .spinner{width:64px;height:64px;border-radius:50%;border:6px solid rgba(255,255,255,.25);border-top-color:#38bdf8;animation:spin 1s linear infinite}
  .dots{color:#e5e7eb;font-weight:600;letter-spacing:.4px}
  @keyframes spin{to{transform:rotate(360deg)}}

  .table-sm td,.table-sm th{ padding:.45rem .6rem; }
  .label-lg{ display:none; } @media (min-width:1200px){ .label-lg{ display:inline; } }
</style>

<div id="global-loader" aria-hidden="true">
  <div class="loader-wrap">
    <div class="spinner"></div>
    <div class="dots">Loading...</div>
  </div>
</div>

<div class="py-12">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-3">
      <div class="p-3">

        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="text-xl fw-bold">การเบิกสารเคมี</h2>
          @if(isset($id))
          <a href="/requisitions/create/{{ $id }}" class="btn btn-primary btn-sm">+ สร้างใบเบิก</a>
          @endif
        </div>

        <!-- Search Box -->
        <div class="card mb-3">
          <div class="card-body">
            <form id="searchForm" class="row g-2 align-items-end">
              <div class="col-12 col-md-3">
                <label class="form-label mb-0">คำค้น</label>
                <input type="text" class="form-control" id="q" placeholder="เลขที่ใบเบิก / inventory_code / ผู้สร้าง">
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label mb-0">สถานะ</label>
                <select id="s" class="form-select">
                  <option value="">ทั้งหมด</option>
                  <option value="PENDING">PENDING</option>
                  <option value="APPROVED">APPROVED</option>
                  <option value="REJECTED">REJECTED</option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label mb-0">ชนิด</label>
                <select id="t" class="form-select">
                  <option value="">ทั้งหมด</option>
                  <option value="CHEM_REQUISITION">CHEM_REQUISITION</option>
                </select>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label mb-0">วันที่เป้าหมาย (จาก)</label>
                <input type="date" id="d1" class="form-control">
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label mb-0">ถึง</label>
                <input type="date" id="d2" class="form-control">
              </div>
              <div class="col-12 col-md-1 d-grid">
                <button class="btn btn-dark btn-sm" type="submit">ค้นหา</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
          <table class="table table-striped table-hover table-sm align-middle" id="invTable">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>crop_id</th>
                <th>broker_id</th>
                <th>inventory_code</th>
                <th>inventory_type</th>
                <th>target_date</th>
                <th>inventory_status</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const brokerId = @json($id ?? 0);
  let activeRequests = 0;
  function showLoader(){ if(++activeRequests===1) $('#global-loader').fadeIn(120); }
  function hideLoader(){ activeRequests=Math.max(0,activeRequests-1); if(activeRequests===0) $('#global-loader').fadeOut(120); }

  let invDT = null;

  $(document).ready(async function () {
    const token = localStorage.getItem('mp_token');
    if(token){ $.ajaxSetup({ headers:{ 'Authorization':'Bearer '+token } }); }

    $('#searchForm').on('submit', function(e){ e.preventDefault(); refreshInventoryTable(); });
    refreshInventoryTable();
    hideLoader();
  });

  async function refreshInventoryTable(){
    try{
      showLoader();
      const params = {
        q: $('#q').val()?.trim(),
        status: $('#s').val(),
        type: $('#t').val(),
        date_from: $('#d1').val(),
        date_to: $('#d2').val()
      };

      const res = brokerId ? await $.get('/api/requisitions/broker/' + brokerId, params) :  await $.get('/api/requisitions');
      const rows = res?.data ?? res ?? [];
      console.log(rows);
      buildInventoryDT(rows);
    }catch(e){
      console.error(e);
      Swal.fire('ผิดพลาด','ไม่สามารถดึงรายการใบเบิกได้','error');
    }finally{ hideLoader(); }
  }

  function buildInventoryDT(data){
    if (invDT) { invDT.clear().rows.add(data).draw(); return; }
    invDT = $('#invTable').DataTable({
      data,
      dom: 'lrtip',
      destroy: true,
      info: false,
      ordering: false,
      pageLength: 50,
      columns: [
        { data: 'id' },
        { data: 'crop', render: data => data ? safe(data.name) : ''  },
        { data: 'broker_name' },
        { data: 'inventory_code', defaultContent:'' },
        { data: 'inventory_type', defaultContent:'' },
        { data: 'target_date', render: d => fmtDate(d) },
        { data: 'inventory_status', render: s => badgeStatus(s) },
        {
          data: null, className:'text-end', orderable:false, searchable:false,
          render: row => `<a class="btn btn-outline-secondary btn-sm" href="/requisitions/view/${safe(row.id)}">ดู</a>`
        }
      ]
    });
  }

  function safe(v){ return (v===null||v===undefined)?'':String(v).replace(/[&<>"']/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[s])); }
  function fmtDate(v){ if(!v) return ''; const d=new Date(v); if(isNaN(d)) return safe(v); return d.toISOString().slice(0,10); }
  function badgeStatus(s){ const m={PENDING:'warning',APPROVED:'success',REJECTED:'danger'}; const cls=m[s]||'secondary'; return `<span class="badge bg-${cls}">${safe(s||'')}</span>`; }
</script>
@endpush
