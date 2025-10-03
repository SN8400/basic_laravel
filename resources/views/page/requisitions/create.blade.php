@extends('layouts.menu')

@section('content')
@csrf

<style>
  #global-loader{ position:fixed; inset:0; z-index:9999; display:none; display:flex; align-items:center; justify-content:center; background:rgba(17,24,39,.55); backdrop-filter:saturate(120%) blur(2px); }
  .loader-wrap{display:flex;flex-direction:column;align-items:center;gap:.75rem}
  .spinner{width:64px;height:64px;border-radius:50%;border:6px solid rgba(255,255,255,.25);border-top-color:#38bdf8;animation:spin 1s linear infinite}
  .dots{color:#e5e7eb;font-weight:600;letter-spacing:.4px}
  @keyframes spin{to{transform:rotate(360deg)}}

  .input-qty{ width:140px; }

  /* Product Modal compact */
  #productModal .modal-dialog { max-width: 980px; }
  #productModal .modal-body { padding: .75rem 1rem; }
  #productModal table.dataTable th,
  #productModal table.dataTable td{ padding:.32rem .5rem; vertical-align: middle; white-space: nowrap; }
  #productModal table.dataTable { table-layout: fixed; }
  /* #productModal table.dataTable thead th { position: sticky; top: 0; z-index: 2; background: #fff; } */
  #productModal .dataTables_wrapper .dataTables_filter,
  #productModal .dataTables_wrapper .dataTables_length { display:none; }
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
          <h2 class="text-xl fw-bold">สร้างใบเบิกสารเคมี</h2>
          <div class="d-flex gap-2">
            <a href="/requisitions/{{ $id }}" class="btn btn-light btn-sm">← กลับรายการ</a>
            <button class="btn btn-success btn-sm" id="btnSaveRequisition">บันทึกใบเบิก</button>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2">
          <h6 class="mb-0 fw-bold">รายการสารเคมี</h6>
          <button id="btn-add-product" class="btn btn-outline-primary btn-sm">+ เพิ่มสินค้า</button>
        </div>

        <div class="table-responsive mb-2">
          <table class="table table-sm table-bordered align-middle" id="createItemsTable">
            <thead class="table-light">
              <tr>
                <th style="width:40px"></th>
                <th>ชื่อสารเคมี</th>
                <th>คงเหลือ</th>
                <th style="width:160px">จำนวนเบิก</th>
                <th style="width:160px">หน่วยเบิก</th>
              </tr>
            </thead>
            <tbody><!-- render items --></tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between">
          <div class="text-muted small">* กรอกจำนวนที่ต้องการเบิก</div>
          <button class="btn btn-outline-danger btn-sm" id="btnRemoveSelected">ลบที่เลือก</button>
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
                <th>คงเหลือ</th>
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
@endsection

@push('scripts')
<script>
  const brokerId = @json($id ?? 0);
  let activeRequests = 0;
  function showLoader(){ if(++activeRequests===1) $('#global-loader').fadeIn(120); }
  function hideLoader(){ activeRequests=Math.max(0,activeRequests-1); if(activeRequests===0) $('#global-loader').fadeOut(120); }

  let prodDT = null;
  let productCache = [];
  let selectedProducts = [];
  let productModal;
  let userId;

  $(document).ready(async function () {
    const token = localStorage.getItem('mp_token');
    if(token){ $.ajaxSetup({ headers:{ 'Authorization':'Bearer '+token } }); }
    try{
      const u = JSON.parse(localStorage.getItem('mp_user')||'{}');
      if(u?.id){ userId = u.id; }
    }catch(e){}

    productModal = new bootstrap.Modal(document.getElementById('productModal'));

    // bindings
    $('#btn-add-product').on('click', openProductModal);
    $('#btnPickProducts').on('click', pickSelectedProducts);
    $('#btnRemoveSelected').on('click', removeSelectedItems);
    $('#btnSaveRequisition').on('click', saveRequisition);
    $('#productSearch').on('input', function(){ const q=$(this).val(); if(prodDT) prodDT.search(q).draw(); });
    $('#chkAllProducts').on('change', function(){ $('#productTable tbody input[type=checkbox]').prop('checked', this.checked); });

    // initial
    renderCreateItems();
    hideLoader();
  });

  async function openProductModal(){
    try{
      showLoader();
      if(productCache.length === 0){
        const res = await $.get('/api/sup-stock/' + brokerId, { is_active: 1, limit: 500 });
        console.log(res);
        productCache = (res?.data ?? []).map(x => ({
          id: x.id,
          code: x?.chemical?.code || '',
          name: x?.chemical?.name || '',
          unit_id: x?.unit?.id || null,
          unit_name: x?.unit?.name || '',
          unit_detail: x?.unit?.detail || '',
          stock_qty: x?.value || ''
        }));
      }
    console.log(productCache);
      $('#productSearch').val('');
      $('#chkAllProducts').prop('checked', false);
      productModal.show();
      $('#productModal').one('shown.bs.modal', () => {
        if (!$.fn.DataTable.isDataTable('#productTable')) {
          buildProductsDT(productCache);
        } else {
          prodDT.clear().rows.add(productCache).draw(false);
        }
        prodDT.columns.adjust().draw(false); // คำนวณ width หลังแสดง
        prodDT.search('').draw();
      });
    }catch(e){
      console.error(e);
      Swal.fire('ผิดพลาด','โหลดรายการสารเคมีไม่สำเร็จ','error');
    }finally{ hideLoader(); }
  }

  function buildProductsDT(data){
    if (prodDT) prodDT.destroy();
    prodDT = $('#productTable').DataTable({
      data,
      dom: 'lrtip',
      destroy: true,
      info: false,
      ordering: false,
      pageLength: 50,
      scrollY: '58vh',
      autoWidth: false,
      responsive: false,
      columns: [
        {
          data: null, title: `<input type="checkbox" id="__chkHead">`,
          width: '40px', orderable:false, searchable:false,
          render: row => {
            const checked = selectedProducts.some(x => x.id === row.id) ? 'checked' : '';
            return `<input type="checkbox" class="chk-product" data-id="${row.id}" ${checked}>`;
          }
        },
        { data: 'code', title:'รหัสสารเคมี' },
        { data: 'name', title:'ชื่อสารเคมี' },
        { data: 'stock_qty', title:'คงเหลือ' },
        { data: 'unit_name', title:'หน่วยสารเคมี'  }
      ]
    });

    // header checkbox sync
    $('#productTable thead').off('change', '#__chkHead').on('change', '#__chkHead', function(){
      const checked = this.checked;
      $('#productTable tbody input.chk-product').prop('checked', checked);
    });
  }

  function pickSelectedProducts(){
    const ids = [];
    $('#productTable tbody .chk-product:checked').each(function(){ ids.push(parseInt($(this).data('id'))); });
    if(ids.length === 0){ Swal.fire('แจ้งเตือน','กรุณาเลือกอย่างน้อย 1 รายการ','info'); return; }

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
    console.log(selectedProducts);
    const html = selectedProducts.map(p => `
      <tr>
        <td class="text-center">
          <input type="checkbox" class="chk-row" data-id="${p.id}">
        </td>
        <td>${safe(p.name)}</td>
        <td>${safe(p.stock_qty)} ${safe(p.unit_name)}</td>
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
      const items = selectedProducts
        .filter(x => x.qty !== '' && !isNaN(parseFloat(x.qty)) && parseFloat(x.qty) > 0)
        .map(x => ({ product_id: x.id, qty_requested: parseFloat(x.qty), unit_id: x.unit_id }));

      if(items.length === 0){
        Swal.fire('ไม่มีรายการ','กรุณาเพิ่มสินค้าอย่างน้อย 1 รายการและกรอกจำนวน','warning'); return;
      }

      showLoader();
      const payload = {
        inventory_status: 'PENDING',
        request_by:brokerId,
        userId:userId,
        items
      };

      const res = await $.ajax({
        url: '/api/requisitions',
        method: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json; charset=utf-8',
        headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() }
      });

      Swal.fire('สำเร็จ','บันทึกใบเบิกเรียบร้อย','success').then(()=> {
        window.location.href = '/requisitions/' + brokerId;
      });
    }catch(e){
      console.error(e);
      const msg = e?.responseJSON?.message || 'บันทึกไม่สำเร็จ';
      Swal.fire('ผิดพลาด', msg, 'error');
    }finally{ hideLoader(); }
  }

  function safe(v){ return (v===null || v===undefined) ? '' : String(v).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
</script>
@endpush
