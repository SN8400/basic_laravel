@extends('layouts.menu')

@section('content')
@csrf

<style>
  #global-loader{
    position:fixed; inset:0; z-index:9999;
    display:none; display:flex; align-items:center; justify-content:center;
    background:rgba(17,24,39,.55); backdrop-filter:saturate(120%) blur(2px);
  }
  .loader-wrap{display:flex;flex-direction:column;align-items:center;gap:.75rem}
  .spinner{width:64px;height:64px;border-radius:50%;border:6px solid rgba(255,255,255,.25);border-top-color:#38bdf8;animation:spin 1s linear infinite}
  .dots{color:#e5e7eb;font-weight:600;letter-spacing:.4px}
  @keyframes spin{to{transform:rotate(360deg)}}

  .btn-compact{ padding:.25rem .5rem; font-size:.8rem; line-height:1.1; border-radius:.35rem; }
  .dt-actions{ display:flex; align-items:center; gap:.35rem; flex-wrap:nowrap; white-space:nowrap; }
  .label-lg{ display:none; } @media (min-width:1200px){ .label-lg{ display:inline; } }
</style>

<!-- Loader Overlay -->
<div id="global-loader" aria-hidden="true">
  <div class="loader-wrap">
    <div class="spinner"></div>
    <div class="dots">Loading...</div>
  </div>
</div>

<div class="py-12">
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-3">
      <div class="p-6 text-gray-900">

        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="text-xl font-bold">Setting (sys-log)</h2>
        </div>

        <!-- Search + Create -->
        <div class="card mb-3">
          <div class="card-body">
            <form id="searchForm" class="row g-2 align-items-end">
              <div class="col-8 col-md-8">
                <label class="form-label mb-0">คำค้น (ชื่อ/สถานะ)</label>
                <input type="text" id="q" class="form-control" placeholder="เช่น feature_x / ACTIVE">
              </div>
              <div class="col-4 col-md-4 d-grid">
                <button class="btn btn-dark btn-sm" type="submit">ค้นหา</button>
              </div>
              {{-- <div class="col-6 col-md-2 d-grid ms-auto">
                <button type="button" class="btn btn-primary btn-sm" id="btnCreate">+ Create</button>
              </div> --}}
            </form>
          </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
          <table id="sysTable" class="table table-striped table-hover table-sm align-middle" style="width:100%">
            <thead class="table-light">
              <tr>
                <th>ชื่อระบบ</th>
                <th>สถานะ</th>
                <th>อัฟเดทล่าสุด</th>
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

<!-- Modal: Create -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Setting</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">sys_name <span class="text-danger">*</span></label>
          <input type="text" id="c_sys_name" class="form-control" placeholder="เช่น feature_toggle_xyz">
        </div>
        <div class="text-muted small">* จะถูกสร้างใน sys-log โดยกำหนดสถานะเริ่มต้นเป็น <code>ACTIVE</code></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-light btn-sm" data-bs-dismiss="modal">ปิด</button>
        <button class="btn btn-success btn-sm" id="btnSaveCreate">บันทึก</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  /** ========= Loader ========= **/
  let activeRequests = 0;
  function showLoader(){ if(++activeRequests===1) $('#global-loader').fadeIn(120); }
  function hideLoader(){ activeRequests=Math.max(0,activeRequests-1); if(activeRequests===0) $('#global-loader').fadeOut(120); }

  let sysDT = null;
  let createModal;

  $(document).ready(async function () {
    try{
      showLoader();
      initializePage();
      createModal = new bootstrap.Modal(document.getElementById('createModal'));
      bindEvents();
      await refreshTable();
    } finally { hideLoader(); }
  });

  function initializePage(){
    const token = localStorage.getItem('mp_token');
    if(token){ $.ajaxSetup({ headers:{ 'Authorization':'Bearer '+token } }); }
  }

  function bindEvents(){
    $('#searchForm').on('submit', async (e)=>{ e.preventDefault(); await refreshTable(); });
    $('#btnCreate').on('click', ()=>{ $('#c_sys_name').val(''); createModal.show(); });

    $('#btnSaveCreate').on('click', saveCreate);

    // delegate ปุ่ม Update ในแถว
    $('#sysTable').on('click', '.btn-update', function(){
      const row = sysDT.row($(this).closest('tr')).data();
      $('#u_sys_name').val(row.sys_name || '');
      setStatusSelect('#u_sys_status', row.sys_status);
    });
  }

  async function refreshTable(){
    try{
      showLoader();
      const q = $('#q').val()?.trim();
      // ปรับ endpoint ให้ตรงระบบจริงของคุณ
      const res = await $.get('/api/sys-log', { q });
      const rows = res?.data ?? res ?? [];
      buildDT(rows);
    }catch(err){
      console.error(err);
      Swal.fire('ผิดพลาด','โหลดข้อมูลไม่สำเร็จ','error');
    }finally{ hideLoader(); }
  }

  function buildDT(data){
    if (sysDT){
      sysDT.clear().rows.add(data).draw();
      return;
    }
    sysDT = $('#sysTable').DataTable({
      data,
      dom: 'lrtip',
      destroy: true,
      info: false,
      ordering: false,
      pageLength: 50,
      columns: [
        { data: 'sys_name',   title: 'ชื่อระบบ', defaultContent:'' },
        { data: 'sys_status', title: 'สถานะ', render: s => badgeStatus(s) },
        { data: 'updated_at', title: 'อัฟเดทล่าสุด', render: d => fmtDatetime(d) },
        {
          data: null, title:'Actions', className:'text-end', orderable:false, searchable:false,
          render: function(row){
            return `<button type="button" class="btn btn-outline-primary btn-sm btn-update" onclick="onUpdateService(${row.id})">Update</button>`;
          }
        }
      ]
    });
  }

  async function saveCreate(){
    try{
      const sys_name = ($('#c_sys_name').val()||'').trim();
      if (!sys_name){ Swal.fire('กรอกไม่ครบ','กรุณากรอก sys_name','warning'); return; }

      showLoader();
      // เริ่มต้นด้วย status = ACTIVE
      const payload = { sys_name, sys_status: 'ACTIVE' };
      // ปรับ endpoint ให้ตรงระบบจริงของคุณ
      await $.ajax({
        url: '/api/sys-log',
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json; charset=utf-8',
        headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() }
      });

      Swal.fire('สำเร็จ','สร้างรายการเรียบร้อย','success');
      createModal.hide();
      await refreshTable();
    }catch(err){
      console.error(err);
      const msg = err?.responseJSON?.message || 'บันทึกไม่สำเร็จ';
      Swal.fire('ผิดพลาด', msg, 'error');
    }finally{ hideLoader(); }
  }

  async function onUpdateService(sysId){
    try{
      showLoader();
      await $.ajax({
        url: '/api/sys-log/' + sysId,
        method: 'GET',
        contentType: 'application/json; charset=utf-8',
        headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() }
      });

      Swal.fire('สำเร็จ','สร้างรายการเรียบร้อย','success');
      await refreshTable();
    }catch(err){
      console.error(err);
      const msg = err?.responseJSON?.message || 'บันทึกไม่สำเร็จ';
      Swal.fire('ผิดพลาด', msg, 'error');
    }finally{ hideLoader(); }
  }

  
  
  /** ===== Helpers ===== */
  function setStatusSelect(sel, val){
    // ถ้า value ปัจจุบันไม่อยู่ใน option ให้ append เข้าไปก่อน
    const $sel = $(sel);
    if (val && $sel.find(`option[value="${val}"]`).length === 0){
      $sel.append(`<option value="${safe(val)}">${safe(val)}</option>`);
    }
    $sel.val(val || 'ACTIVE');
  }

  function badgeStatus(s){
    const v = (s||'').toUpperCase();
    const map = { 'ACTIVE':'success', 'INACTIVE':'secondary', 'DISABLED':'dark' };
    const cls = map[v] || 'info';
    return `<span class="badge bg-${cls}">${safe(s||'')}</span>`;
  }
  function safe(v){ return (v===null || v===undefined) ? '' : String(v).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
  function fmtDatetime(v){
    if(!v) return '';
    const d = new Date(v);
    if(isNaN(d)) return safe(v);
    const pad = n => n.toString().padStart(2,'0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }
</script>
@endpush
