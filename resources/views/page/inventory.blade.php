@extends('layouts.menu')

@section('content')
@csrf

<style>
  #global-loader{ position:fixed; inset:0; z-index:9999; display:none; display:flex; align-items:center; justify-content:center; background:rgba(17,24,39,.55); backdrop-filter:saturate(120%) blur(2px); }
  .loader-wrap{display:flex;flex-direction:column;align-items:center;gap:.75rem}
  .spinner{width:64px;height:64px;border-radius:50%;border:6px solid rgba(255,255,255,.25);border-top-color:#38bdf8;animation:spin 1s linear infinite}
  .dots{color:#e5e7eb;font-weight:600;letter-spacing:.4px}
  @keyframes spin{to{transform:rotate(360deg)}}

  .btn-compact{ padding:.35rem .6rem; font-size:.8rem; line-height:1.1; border-radius:.35rem; }
  .dt-actions{ display:flex; align-items:center; gap:.35rem; white-space:nowrap; }
  .label-lg{ display:none; } @media (min-width:1200px){ .label-lg{ display:inline; } }

  .table-sm td,.table-sm th{ padding:.45rem .6rem; }
  .input-qty{ width:110px; }
  .w-140{ width:140px; }

  /* ========== Product Modal compact ========== */
#productModal .modal-dialog { max-width: 980px; }            /* กว้างพอดีจอ */
#productModal .modal-body { padding: .75rem 1rem; }          /* บีบ padding โมดัล */

#productModal table.dataTable th,
#productModal table.dataTable td{
  padding: .32rem .5rem;                                     /* บีบระยะห่างเซลล์ */
  vertical-align: middle;
  white-space: nowrap;                                       /* กันแตกบรรทัด */
}

#productModal table.dataTable { table-layout: fixed; }       /* คุมความกว้างคอลัมน์ */
#productModal table.dataTable thead th {                     /* header ติดขอบบน */
  position: sticky; top: 0; z-index: 2; background: #fff;
}

/* ปุ่มเล็กกระชับ */
.btn-compact-xs{ padding:.18rem .45rem; font-size:.75rem; line-height:1; border-radius:.3rem; }
.no-wrap{ white-space:nowrap; }

/* ซ่อน UI ของ DataTables ที่เราไม่ใช้ (มีช่องค้นหาเองแล้ว) */
#productModal .dataTables_wrapper .dataTables_filter,
#productModal .dataTables_wrapper .dataTables_length { display:none; }

/* จัดกลางคอลัมน์ checkbox / actions */
#productTable td.dt-center, #productTable th.dt-center { text-align:center; }

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
      <div class="p-3">

        <div class="d-flex align-items-center justify-content-between mb-3">
          <h2 class="text-xl fw-bold">การเบิกสารเคมี</h2>
          <button id="btn-create" class="btn btn-primary btn-sm">
            + สร้างใบเบิก
          </button>
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

        <!-- Table: ae_inventory -->
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

<!-- Modal: เลือกสินค้า -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">เลือกสารเคมี</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 mb-2">
          <div class="col-12 col-md-6">
            <input id="productSearch" type="text" class="form-control" placeholder="ค้นหาชื่อ/รหัสสารเคมี">
          </div>
        </div>
        <div class="table-responsive">
          <table class="table table-sm table-hover" id="productTable">
            <thead class="table-light">
              <tr>
                <th style="width:40px"><input type="checkbox" id="chkAllProducts"></th>
                <th>รหัส</th>
                <th>ชื่อสารเคมี</th>
                <th>หน่วย</th>
              </tr>
            </thead>
            <tbody><!-- render by JS --></tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <div class="text-muted small">เลือกได้หลายรายการ</div>
        <div>
          <button class="btn btn-light btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
          <button class="btn btn-primary btn-sm" id="btnPickProducts">ยืนยันการเลือก</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal/Page: สร้างใบเบิก -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">สร้างใบเบิก</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="close"></button>
      </div>
      <div class="modal-body">
        <!-- Header form -->
        <div class="row g-2 mb-3">
          <div class="col-6 col-md-3">
            <label class="form-label mb-0">Crop ID</label>
            <input type="number" id="f_crop_id" class="form-control" min="1" step="1">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label mb-0">Broker ID</label>
            <input type="number" id="f_broker_id" class="form-control" min="1" step="1">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label mb-0">Target date</label>
            <input type="date" id="f_target_date" class="form-control">
          </div>
          <div class="col-6 col-md-3">
            <label class="form-label mb-0">ชนิดเอกสาร</label>
            <input type="text" id="f_inventory_type" class="form-control" value="CHEM_REQUISITION" readonly>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0 fw-bold">รายการสารเคมี</h6>
          <button id="btn-add-product" class="btn btn-outline-primary btn-sm">+ เพิ่มสินค้า</button>
        </div>

        <div class="table-responsive">
          <table class="table table-sm table-bordered align-middle" id="createItemsTable">
            <thead class="table-light">
              <tr>
                <th style="width:40px"></th>
                <th>ชื่อสารเคมี</th>
                <th style="width:160px">จำนวน</th>
                <th style="width:160px">หน่วย</th>
              </tr>
            </thead>
            <tbody><!-- render items --></tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between">
          <div class="text-muted small">* กรอกจำนวนที่ต้องการเบิก</div>
          <div>
            <button class="btn btn-outline-danger btn-sm" id="btnRemoveSelected">ลบที่เลือก</button>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-light btn-sm" data-bs-dismiss="modal">ปิด</button>
        <button class="btn btn-success btn-sm" id="btnSaveRequisition">บันทึกใบเบิก</button>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
<script>
    const farmerCode = @json($id ?? null);
  /** ========= Loader ========= **/
  let activeRequests = 0;
  function showLoader(){ if(++activeRequests===1) $('#global-loader').fadeIn(120); }
  function hideLoader(){ activeRequests=Math.max(0,activeRequests-1); if(activeRequests===0) $('#global-loader').fadeOut(120); }

  // NEW: DataTables handlers
  let invDT = null;       // DataTable สำหรับรายการใบเบิก ae_inventory
  let prodDT = null;      // DataTable สำหรับเลือกสินค้าในโมดัล

  let productCache = [];          // [{id, code, name, unit_id, unit_name, unit_detail?}]
  let selectedProducts = [];      // [{id, code, name, unit_id, unit_name, qty}]
  let productModal, createModal;

  $(document).ready(async function () {
    // attach token if any
    const token = localStorage.getItem('mp_token');
    if(token){ $.ajaxSetup({ headers:{ 'Authorization':'Bearer '+token } }); }

    productModal = new bootstrap.Modal(document.getElementById('productModal'));
    createModal  = new bootstrap.Modal(document.getElementById('createModal'));

    bindEvents();
    await refreshInventoryTable(); // initial load
  });

  /** ====== UI Events ====== */
  function bindEvents(){
    $('#searchForm').on('submit', async function(e){
      e.preventDefault();
      await refreshInventoryTable();
    });

    $('#btn-create').on('click', onCreateClick);
    $('#btn-add-product').on('click', openProductModal);
    $('#btnPickProducts').on('click', pickSelectedProducts);
    $('#btnRemoveSelected').on('click', removeSelectedItems);
    $('#btnSaveRequisition').on('click', saveRequisition);

    // ค้นหาสินค้าภายใน DataTables ด้วยช่องค้นหาเอง
    $('#productSearch').on('input', function(){
      const q = $(this).val();
      if (prodDT) prodDT.search(q).draw();
    });

    // เลือก/ไม่เลือกทั้งหมด (เฉพาะสินค้าที่หน้า/ฟิลเตอร์ปัจจุบัน)
    $('#chkAllProducts').on('change', function(){
      const checked = this.checked;
      $('#productTable tbody input[type=checkbox]').prop('checked', checked);
    });
  }

  /** ====== ae_inventory: DataTable ====== */
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
      // ปรับ endpoint ให้ตรงกับระบบของคุณ
      const res = await $.get('/api/requisitions', params);
      const rows = res?.data ?? res ?? [];

      buildInventoryDT(rows);
    }catch(e){
      console.error(e);
      Swal.fire('ผิดพลาด','ไม่สามารถดึงรายการใบเบิกได้','error');
    }finally{ hideLoader(); }
  }

  function buildInventoryDT(data){
    if (invDT) {
      invDT.clear().rows.add(data).draw();
      return;
    }
    invDT = $('#invTable').DataTable({
      data,
      dom: 'lrtip',
      destroy: true,
      info: false,
      ordering: false,
      pageLength: 50,
      columns: [
        { data: 'id',               title:'ID' },
        { data: 'crop_id',          title:'crop' },
        { data: 'broker_id',        title:'broker' },
        { data: 'inventory_code',   title:'inventory_code', defaultContent:'' },
        { data: 'inventory_type',   title:'inventory_type', defaultContent:'' },
        { data: 'target_date',      title:'target_date', render: d => fmtDate(d) },
        { data: 'inventory_status', title:'inventory_status', render: s => badgeStatus(s) },
        {
          data: null, title:'Actions', className:'text-end', orderable:false, searchable:false,
          render: function(row){
            return `<a class="btn btn-outline-secondary btn-sm" href="/inventory/${safe(row.id)}">ดู</a>`;
          }
        }
      ]
    });
  }

  /** ====== Create Flow ====== */
  function onCreateClick(){
    $('#f_crop_id').val('');
    $('#f_broker_id').val('');
    $('#f_target_date').val(new Date().toISOString().slice(0,10));
    selectedProducts = [];
    renderCreateItems();
    createModal.show();
  }

  async function openProductModal(){
    try{
      showLoader();
      if(productCache.length === 0){
        // ปรับ endpoint ให้ตรงระบบจริง
        const res = await $.get('/api/sup-stock', { is_active: 1, limit: 500 });
        console.log(res);

        productCache = (res?.data ?? []).map(x => ({
          id: x.id,
          code: x?.chemical?.code || '',
          name: x?.chemical?.name || '',
          unit_id: x?.unit?.id || null,
          unit_name: x?.unit?.name || '',
          unit_detail: x?.unit?.detail || ''
        }));
      }
        console.log(productCache);
      buildProductsDT(productCache);
      $('#productSearch').val('');
      prodDT.search('').draw();
      $('#chkAllProducts').prop('checked', false);
      productModal.show();
    }catch(e){
      console.error(e);
      Swal.fire('ผิดพลาด','โหลดรายการสารเคมีไม่สำเร็จ','error');
    }finally{ hideLoader(); }
  }

  /** ====== Modal: products DataTable (หลายเลือกด้วย checkbox) ====== */
  function buildProductsDT(data){
    if (prodDT) prodDT.destroy();

    prodDT = $('#productTable').DataTable({
      data: data,
      dom: 'lrtip',
      destroy: true,
      info: false,
      ordering: false,
      pageLength: 50,
      scrollY: '58vh',   
      columns: [
        {
          data: null, title: `<input type="checkbox" id="__chkHead">`,
          width: 40, orderable:false, searchable:false,
          render: function(row){
            const checked = selectedProducts.some(x => x.id === row.id) ? 'checked' : '';
            return `<input type="checkbox" class="chk-product" data-id="${row.id}" ${checked}>`;
          }
        },
        { data: 'code', title:'รหัสสารเคมี' },
        { data: 'name', title:'ชื่อสารเคมี' },
        { data: 'unit_name', title:'หน่วยสารเคมี'  }
      ]
    });

    // sync checkbox header กับรายการที่แสดงอยู่
    $('#productTable thead').off('change', '#__chkHead').on('change', '#__chkHead', function(){
      const checked = this.checked;
      $('#productTable tbody input.chk-product').prop('checked', checked);
    });
  }

  function pickSelectedProducts(){
    // เก็บเฉพาะแถวที่ติ๊กอยู่ (ตามฟิลเตอร์/เพจปัจจุบันทั้งหมด)
    const ids = [];
    $('#productTable tbody .chk-product:checked').each(function () {
      ids.push(parseInt($(this).data('id')));
    });
    if(ids.length === 0){
      Swal.fire('แจ้งเตือน','กรุณาเลือกอย่างน้อย 1 รายการ','info'); return;
    }

    // merge กันซ้ำ
    const addList = productCache.filter(p => ids.includes(p.id));
    addList.forEach(p => {
      if(!selectedProducts.some(x => x.id === p.id)){
        selectedProducts.push({ ...p, qty: '' });
      }
    });

    renderCreateItems();
    productModal.hide();
  }

  function removeSelectedItems(){
    const ids = [];
    $('#createItemsTable tbody .chk-row:checked').each(function(){ ids.push(parseInt($(this).data('id'))); });
    if(ids.length === 0){ return; }
    selectedProducts = selectedProducts.filter(x => !ids.includes(x.id));
    renderCreateItems();
  }

  function renderCreateItems(){
    const html = selectedProducts.map(p => `
      <tr>
        <td class="text-center">
          <input type="checkbox" class="chk-row" data-id="${p.id}">
        </td>
        <td>${safe(p.name)}</td>
        <td>
          <input type="number" min="0" step="0.01" class="form-control form-control-sm input-qty" 
                 data-id="${p.id}" value="${p.qty ?? ''}" placeholder="0.00"
                 oninput="onQtyInput(${p.id}, this.value)">
        </td>
        <td>${safe(p.unit_name||'')}</td>
      </tr>
    `).join('');
    $('#createItemsTable tbody').html(html || `<tr><td colspan="4" class="text-center text-muted">— ยังไม่มีรายการ —</td></tr>`);
  }

  function onQtyInput(id, val){
    const idx = selectedProducts.findIndex(x => x.id===id);
    if(idx >= 0){ selectedProducts[idx].qty = val; }
  }

  async function saveRequisition(){
    try{
      // validate header
      const crop_id = parseInt($('#f_crop_id').val()||0);
      const broker_id = parseInt($('#f_broker_id').val()||0);
      const target_date = $('#f_target_date').val();
      const inventory_type = $('#f_inventory_type').val();

      if(!crop_id || !broker_id || !target_date){
        Swal.fire('กรอกไม่ครบ','กรุณากรอก Crop ID, Broker ID และ Target Date','warning'); return;
      }
      // validate items
      const items = selectedProducts
        .filter(x => x.qty !== '' && !isNaN(parseFloat(x.qty)) && parseFloat(x.qty) > 0)
        .map(x => ({ product_id: x.id, qty_requested: parseFloat(x.qty), unit_id: x.unit_id }));

      if(items.length === 0){
        Swal.fire('ไม่มีรายการ','กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการและกรอกจำนวน','warning'); return;
      }

      showLoader();
      const payload = {
        crop_id, broker_id,
        target_date,
        inventory_type,                      // CHEM_REQUISITION
        inventory_status: 'PENDING',         // รออนุมัติ
        items                                  // [{product_id, qty_requested, unit_id}]
      };
      const res = await $.ajax({
        url: '/api/requisitions',
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json; charset=utf-8',
        headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() }
      });

      Swal.fire('สำเร็จ','บันทึกใบเบิกเรียบร้อย','success');
      createModal.hide();
      await refreshInventoryTable();

    }catch(e){
      console.error(e);
      const msg = e?.responseJSON?.message || 'บันทึกไม่สำเร็จ';
      Swal.fire('ผิดพลาด', msg, 'error');
    }finally{ hideLoader(); }
  }

  /** ====== Helpers ====== */
  function safe(v){ return (v===null || v===undefined) ? '' : String(v).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
  function fmtDate(v){ if(!v) return ''; const d = new Date(v); if(isNaN(d)) return safe(v); return d.toISOString().slice(0,10); }
  function fmtDatetime(v){
    if(!v) return '';
    const d = new Date(v);
    if(isNaN(d)) return safe(v);
    const pad = n => n.toString().padStart(2,'0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
  }
  function badgeStatus(s){
    const map = { PENDING:'warning', APPROVED:'success', REJECTED:'danger' };
    const cls = map[s] || 'secondary';
    return `<span class="badge bg-${cls}">${safe(s||'')}</span>`;
  }
</script>
@endpush
