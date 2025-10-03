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
        /* ปุ่มกระชับ */
        .btn-compact{ padding:.25rem .5rem; font-size:.75rem; line-height:1.1; border-radius:.35rem; }
        /* กล่องปุ่มเรียงแถวเดียว มีช่องว่างเล็กน้อย */
        .dt-actions{ display:flex; align-items:center; gap:.35rem; flex-wrap:nowrap; white-space:nowrap; }
        /* ข้อความยาว แสดงเฉพาะจอใหญ่ (>=1200px) */
        .label-lg{ display:none; }
        @media (min-width: 1200px){ .label-lg{ display:inline; } }
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
                    <h2 class="text-xl font-bold">การเบิกสารเคมี</h2>
                </div>


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


    $(document).ready(async function () {
        try
        {
            showLoader();
            initializePage(); 
        } 
        finally 
        { 
            hideLoader(); 
        }
    });
    

  function initializePage(){
    const token = localStorage.getItem('mp_token');
    if(token){ $.ajaxSetup({ headers:{ 'Authorization':'Bearer '+token } }); }
  }


</script>
@endpush
