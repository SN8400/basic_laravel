<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'App' }}</title>

  {{-- ใช้ไฟล์โลคัลที่โหลดไว้ใน public/assets --}}
  <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/datatables/jquery.dataTables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">
  <style>
    body{background:#f3f4f6;}
    .app-topbar{background:#fff;border-bottom:1px solid #e5e7eb;}
    .app-brand{display:flex;align-items:center;gap:.5rem;font-weight:700;}
    .app-nav a{color:#111827;text-decoration:none;padding:1rem .75rem;display:inline-block;border-bottom:3px solid transparent;}
    .app-nav a.active{border-bottom-color:#6366f1;color:#111827;}
    .content-wrap{padding:2rem 0;}
    .card-elevate{border:0;border-radius:.75rem;box-shadow:0 10px 25px rgba(0,0,0,.05);}
  </style>
</head>
<body>
  @csrf
  {{-- Topbar --}}
  <header class="app-topbar">
    <div class="container d-flex align-items-center justify-content-between py-2">
      <div class="d-flex align-items-center gap-4">
        <div class="app-brand">
          <img src="{{ asset('assets/img/laco_monster.png') }}" width="64" height="64" alt="">
          <span>Man-Power</span>
        </div>
        <nav class="app-nav d-none d-md-block">
          <a href="{{ url('/rooms') }}" class="{{ request()->is('rooms*') ? 'active' : '' }}">จัดการห้อง</a>
          <a href="{{ url('/works') }}" class="{{ request()->is('works*') ? 'active' : '' }}">จัดการงาน</a>
          <a href="{{ url('/plans') }}" class="{{ request()->is('plans*') ? 'active' : '' }}">แผนงาน</a>
          <a href="{{ url('/users') }}" class="{{ request()->is('users*') ? 'active' : '' }}">User</a>
        </nav>
      </div>

      {{-- Profile dropdown ขวาบน --}}
      <div class="dropdown">
        <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="btnProfile">
          <span id="topUsername">User</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
          <li><a class="dropdown-item" href="{{ url('/profile') }}">Profile</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#" id="menuLogout">Log Out</a></li>
        </ul>
      </div>
    </div>
  </header>

  {{-- Page Title (optional slot) --}}
  @hasSection('page_title')
    <div class="container py-3">
      <h4 class="mb-0">@yield('page_title')</h4>
    </div>
  @endif

  {{-- Page content --}}
  <main class="content-wrap">
    <div class="container">
      @yield('content')
    </div>
  </main>

  <script src="{{ asset('assets/jquery/jquery-3.7.1.min.js') }}"></script>
  <script src="{{ asset('assets/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
  <script>
    const iconMap = { success:'success', failed:'error', error:'error' };
    
    $(document).ready(function () {
      try{
        const u = JSON.parse(localStorage.getItem('mp_user')||'{}');
        if(u?.username){ document.getElementById('topUsername').textContent = u.username; }
      }catch(e){}
    });

    // logout
    $('#menuLogout').on('click', function(e){
      e.preventDefault();
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
          location.href = res.redirect_to || '/login';
        }
      });
    });

    function msgNotify(type='success', header='', details='', timer=null) {
      const key = String(type || '').toLowerCase();
      const icon = iconMap[key] || 'info';
      const base = {
        icon,
        title: header || (icon === 'success' ? 'สำเร็จ' : icon === 'error' ? 'ไม่สำเร็จ' : 'แจ้งเตือน'),
        text: details || '',
        confirmButtonText: 'ตกลง',
      };
      const timed = timer ? { timer, showConfirmButton: false, timerProgressBar: true } : {};
      return Swal.fire({ ...base, ...timed });
    }

    function msgConfirm(header='', details='') {
      return Swal.fire({
        icon: 'question',
        title: header || 'ยืนยันการทำรายการ?',
        text: details || '',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        reverseButtons: true,
        focusCancel: true,
      }).then(res => res.isConfirmed === true);
    }

  </script>
  @stack('scripts')
</body>
</html>
