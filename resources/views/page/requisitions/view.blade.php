@extends('layouts.menu')

@section('content')
@csrf

<style>
  /* Loader */
  #global-loader{
    position:fixed; inset:0; z-index:9999;
    display:none; display:flex; align-items:center; justify-content:center;
    background:rgba(17,24,39,.55); backdrop-filter:saturate(120%) blur(2px);
  }
  .loader-wrap{display:flex;flex-direction:column;align-items:center;gap:.75rem}
  .spinner{width:64px;height:64px;border-radius:50%;border:6px solid rgba(255,255,255,.25);border-top-color:#38bdf8;animation:spin 1s linear infinite}
  .dots{color:#e5e7eb;font-weight:600;letter-spacing:.4px}
  @keyframes spin{to{transform:rotate(360deg)}}

  /* print เฉพาะกรอบพรีวิว */
  @media print {
    body * { visibility: hidden; }
    #requisition-preview, #requisition-preview * { visibility: visible; }
    #requisition-preview { position: absolute; left: 0; top: 0; width: 100%; }
  }
</style>

<!-- Loader -->
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

        <!-- Header + Actions -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="text-xl fw-bold mb-0">พรีวิวเอกสาร</h2>
          <div class="d-flex gap-2">
            <button id="btn-save-pdf" class="btn btn-outline-secondary btn-sm">Save PDF</button>
            <button id="btn-print"    class="btn btn-outline-secondary btn-sm">Print</button>
            <a href="javascript:history.back()" class="btn btn-primary btn-sm">← กลับ</a>
          </div>
        </div>

        <!-- โซนพรีวิว (พิมพ์/บันทึก PDF เฉพาะส่วนนี้) -->
        <div id="requisition-preview" class="p-3">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 id="pv-title" class="mb-1">เอกสารคลังสารเคมี</h4>
              <div>วันที่: <span id="pv-date">-</span></div>
              <div>โครงการ/แปลง (crop): <span id="pv-crop">-</span></div>
              <div>ผู้เกี่ยวข้อง (broker): <span id="pv-broker">-</span></div>
            </div>
            <div class="text-end">
              <div>เลขที่เอกสาร: <span id="pv-docno">—</span></div>
            </div>
          </div>

          <table class="table table-bordered table-sm w-100">
            <thead class="table-light">
              <tr>
                <th style="width:140px;">รหัสสารเคมี</th>
                <th>ชื่อสารเคมี</th>
                <th>รายละเอียด</th>
                <th style="width:120px;" class="text-end">จำนวน</th>
              </tr>
            </thead>
            <tbody id="pv-body">
              <tr><td colspan="4" class="text-center text-muted">—</td></tr>
            </tbody>
          </table>

          <div class="mt-3">
            หมายเหตุ: โปรดตรวจสอบความถูกต้องก่อนบันทึก/พิมพ์
          </div>
        </div>
          <div id="pv-approval-bar" class="d-flex justify-content-end gap-2 mt-3">
            <button id="btn-reject" type="button" class="btn btn-danger btn-lg"onclick="onUpdateService('UNAPPROVED')">ไม่อนุมัติ</button>
            <button id="btn-approve" type="button" class="btn btn-success btn-lg" onclick="onUpdateService('APPROVED')">อนุมัติ</button>
          </div>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  /** ========= Boot ========= **/
  const invId = @json($id ?? 0); // ส่งมาจาก Controller

  /** ========= Loader ========= **/
  let activeRequests = 0;
  function showLoader(){ if(++activeRequests===1) $('#global-loader').fadeIn(120); }
  function hideLoader(){ activeRequests=Math.max(0,activeRequests-1); if(activeRequests===0) $('#global-loader').fadeOut(120); }
  let userId;
  let brokerCode;
  $(document).ready(function () {
    // auth header (เหมือนหน้าอื่น ๆ)
    const token = localStorage.getItem('mp_token');
    if (token) $.ajaxSetup({ headers:{ 'Authorization':'Bearer '+token } });
    try{
      const u = JSON.parse(localStorage.getItem('mp_user')||'{}');
      if(u?.id){ userId = u.id; }
    }catch(e){}
    // โหลดข้อมูลเอกสาร
    (async () => {
      try{
        showLoader();
        await loadAndBuild(invId);
        bindPrintButtons();
      } finally { hideLoader(); }
    })();
  });

  async function loadAndBuild(id){
    return $.ajax({
      url: '/api/requisition-items/' + id,
      method: 'GET',
      success: (res) => {
        const inv = res?.data ?? res ?? {};
        console.log(inv);
        buildPreview(inv);
      },
      error: () => alert('ไม่สามารถโหลดรายละเอียดเอกสารได้')
    });
  }

  function buildPreview(inv){
    const firstData = inv[0].requisition;
    const typeText = mapTypeText(firstData?.inventory_type);
    $('#pv-title').text(`${typeText} (Preview)`);

    $('#pv-date').text(firstData?.request_date ?? '-');
    $('#pv-crop').text(firstData?.crop?.name ?? firstData?.crop_id ?? '-');
    brokerCode = firstData?.broker?.code || 0;
    const brokerName = (firstData?.broker?.fname || firstData?.broker?.lname)
      ? `${firstData.broker.fname ?? ''} ${firstData.broker.lname ?? ''}`.trim()
      : (firstData?.broker_id ?? '-');
    $('#pv-broker').text(brokerName || '-');

    $('#pv-docno').text(firstData?.inventory_code ?? '—');

    const $body = $('#pv-body').empty();
    if (Array.isArray(inv) && inv.length){
      inv.forEach(items => {
        const it = items?.stock ?? [];
        const chem = it?.chemical ?? it?.chem ?? it?.chemicals ?? {};
        const code = chem?.code ?? '';
        const name = chem?.name ?? '';
        const desc = chem?.details ?? chem?.detail ?? '';
        const qty  = items?.qty_requested ?? 0;
        const unit  = it?.unit.detail ?? '';

        $body.append(`
          <tr>
            <td>${escapeHtml(code)}</td>
            <td>${escapeHtml(name)}</td>
            <td>${escapeHtml(String(desc))}</td>
            <td class="text-end">${Number(qty) +' '+String(unit)}</td>
          </tr>
        `);
      });
    } else {
      $body.append(`<tr><td colspan="4" class="text-center text-muted">ไม่มีรายการ</td></tr>`);
    }
  }

  function bindPrintButtons(){
    // Save PDF (ต้องมี html2pdf ใน layout; ถ้าไม่มีจะแจ้งเตือน)
    $('#btn-save-pdf').on('click', function(){
      if (typeof html2pdf === 'undefined') { alert('ไม่พบไลบรารี html2pdf'); return; }
      const el = document.getElementById('requisition-preview');
      html2pdf().set({
        margin: 10,
        filename: `inventory_${new Date().toISOString().slice(0,10)}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      }).from(el).save();
    });

    // Print เฉพาะกรอบพรีวิว
    $('#btn-print').on('click', function(){
      const content = document.getElementById('requisition-preview').innerHTML;
      const w = window.open('', '', 'height=800,width=600');
      w.document.write('<html><head><title>เอกสารคลังสารเคมี</title></head><body>' + content + '</body></html>');
      w.document.close();
      w.print();
    });
  }

  /** ========= Utils ========= **/
  function mapTypeText(v){
    const x = String(v ?? '').toLowerCase();
    if (['w','withdraw','chem_withdraw','withdrawal','เบิก'].includes(x)) return 'ใบเบิกสินค้า';
    if (['d','deposit','chem_deposit','เติม'].includes(x)) return 'ใบเติมสินค้า';
    return 'เอกสารคลังสารเคมี';
  }

  function escapeHtml(str){
    return String(str).replace(/[&<>"']/g, s => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    }[s]));
  }

  async function onUpdateService(sVal) {
      const payload = {
        inventory_status: sVal,
        userId:userId,
      };

      const res = await $.ajax({
        url: '/api/requisitions/' + invId,
        method: 'PUT',
        data: JSON.stringify(payload),
        contentType: 'application/json; charset=utf-8',
        headers: { 'X-CSRF-TOKEN': $('input[name=_token]').val() }
      });
      console.log(res);
      Swal.fire('สำเร็จ','บันทึกใบเบิกเรียบร้อย','success').then(()=> {
        window.location.href = '/requisitions/' + brokerCode;
      });
  }
</script>
@endpush
