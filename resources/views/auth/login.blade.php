@extends('layouts.app')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-11 col-sm-8 col-md-6 col-lg-4">
        <div class="text-center mb-3">
          <a href="#" class="text-decoration-none fw-semibold"><img src="{{ asset('assets/img/banner1.png') }}" width="128" height="128" alt=""></a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
          <div class="card-body p-4">
            <form id="loginForm" autocomplete="off">
              <div class="mb-3">
                <label class="form-label">user_name</label>
                <input type="text" name="username" class="form-control" required autofocus>
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <div id="loginError" class="alert alert-danger d-none" role="alert"></div>

              <div class="d-grid d-md-flex justify-content-md-end">
                <button type="submit" id="btnLogin" class="btn btn-primary px-4">
                  <span class="btn-text">Log in</span>
                  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="py-4"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function presetAuthHeader(){
  const t = localStorage.getItem('mp_token');
  if(t){ $.ajaxSetup({ headers: { 'Authorization':'Bearer '+t } }); }
})();

$('#loginForm').on('submit', function(e){
  e.preventDefault();
  const $btn = $('#btnLogin'), $txt=$btn.find('.btn-text'), $sp=$btn.find('.spinner-border');
  const $err = $('#loginError').addClass('d-none').text('');

  $btn.prop('disabled', true); $txt.addClass('d-none'); $sp.removeClass('d-none');

  const payload = {
    username: this.username.value.trim(),
    password: this.password.value,
    device_name: 'web'
  };

  $.ajax({
    url: '/api/auth/login',
    // url: '/auth/login',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify(payload),
    success: function(res){
      localStorage.setItem('mp_token', res.token);
      localStorage.setItem('mp_user', JSON.stringify(res.user || {}));
      $.ajaxSetup({ headers: { 'Authorization':'Bearer '+res.token } });
      window.location.href = "{{ url('/') }}"; // เปลี่ยนปลายทางตามจริง
    },
    error: function(xhr){
      const msg = xhr?.responseJSON?.message || 'Login failed';
      $err.removeClass('d-none').text(msg);
    },
    complete: function(){
      $btn.prop('disabled', false); $txt.removeClass('d-none'); $sp.addClass('d-none');
    }
  });
});
</script>
@endpush
