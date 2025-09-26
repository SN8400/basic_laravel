@extends('layouts.menu')

@section('content')
@csrf
<div class="container py-5">
  <div class="row">
    <div class="col">
      <h1 class="mb-4">Dashboard</h1>

      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
          <h5 class="card-title">ยินดีต้อนรับ</h5>
          <p class="card-text">
            คุณเข้าสู่ระบบด้วย token จาก <code>/api/auth/login</code>.
          </p>
          <p id="userInfo"></p>
          <button type="button" id="btnLogout" class="btn btn-outline-danger">Logout</button>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
  // โหลดข้อมูล user จาก localStorage
  let user = {};
  try { user = JSON.parse(localStorage.getItem('mp_user')) || {}; } catch(e){}
  if(user && user.name){
    $('#userInfo').text(`คุณคือ: ${user.name} (username: ${user.username || ''})`);
  }

  // ปุ่ม logout
  $('#btnLogout').on('click', function(){
    $.ajax({
      url:'/api/auth/logout',
      method:'POST',
      headers:{ 
          'Authorization':'Bearer '+localStorage.getItem('mp_token'),
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
      },
      complete: function(res){
        localStorage.removeItem('mp_token');
        localStorage.removeItem('mp_user');
        window.location.href = res.redirect_to || '/login';
      }
    });
  });
});
</script>
@endpush
