@extends('layouts.menu')
@section('page_title') User Management @endsection

@section('content')
  <div class="card card-elevate">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <button class="btn btn-primary" id="btnCreateUser">+ Create User</button>
      </div>

      <h5 class="mb-3">User List</h5>

      <div class="table-responsive">
        <table id="currentTable" class="table table-sm align-middle display" style="width:100%">
          <thead>
            <tr>
              <th>Username</th>
              <th>Name</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Modal สร้าง/แก้ไข เหมือนเดิม --}}
  <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true"> 
    <div class="modal-dialog modal-dialog-centered"> 
      <div class="modal-content"> 
        <div class="modal-header">
          <h5 class="modal-title" id="userModalTitle">Create User</h5> 
          <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
        </div> 
        <div class="modal-body"> 
          <input type="hidden" id="f_id" class="form-control"> 
          <div class="mb-2"> 
            <label class="form-label">Name</label> 
            <input type="text" id="f_name" class="form-control"> 
          </div> 
          <div class="mb-2"> 
            <label class="form-label">Email</label> 
            <input type="email" id="f_email" class="form-control"> 
          </div> 
          <div class="mb-2"> 
            <label class="form-label">Username</label> 
            <input type="text" id="f_username" class="form-control"> 
          </div> <div class="mb-2"> 
            <label class="form-label">Password</label> 
            <input type="password" id="f_password" class="form-control"> 
          </div> 
          <div id="modalError" class="alert alert-danger d-none"></div> 
        </div> 
        <div class="modal-footer"> 
          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button> 
          <button type="button" class="btn btn-primary" id="btnSaveUser" onclick="onSave()">Save</button> 
        </div> 
      </div> 
    </div> 
  </div>
@endsection

@push('scripts')
<script>
  let table;

  $(document).ready(function () {
    initializePage();
    getData();
  });

  function initializePage(){
    const token = localStorage.getItem('mp_token');
    if(token){ $.ajaxSetup({ headers:{ 'Authorization':'Bearer '+token } }); }

    $('#btnCreateUser').on('click', function(){
      $('#userModalTitle').text('Create User');
      $('#f_id,#f_name,#f_email,#f_username,#f_password').val('');
      $('#modalError').addClass('d-none').text('');
      new bootstrap.Modal(document.getElementById('userModal')).show();
    });
  }

  function getData() {
    $.ajax({
      url: '/api/users',
      method: 'GET',
      beforeSend: function(xhr){
        const t = localStorage.getItem('mp_token');
        if(t){ xhr.setRequestHeader('Authorization','Bearer '+t); }
      },
      success: function(res) {
        currentDatas = res.data || [];
        getTable(currentDatas);
      },
      error: function() {
        alert('Error fetching data.');
      }
    });
  }

  function getDataByID(val) {
    $.ajax({
      url: '/api/users/' + val,
      method: 'GET',
      beforeSend: function(xhr){
        const t = localStorage.getItem('mp_token');
        if(t){ xhr.setRequestHeader('Authorization','Bearer '+t); }
      },
      success: function(res) {
        openModalEdit(res);
      },
      error: function() {
        alert('Error fetching data.');
      }
    });
  }

  function openModalEdit(data){
    $('#userModalTitle').text('Edit User');
    $('#f_id').val(data.id||'');
    $('#f_name').val(data.name||'');
    $('#f_email').val(data.email||'');
    $('#f_username').val(data.username||'');
    $('#f_password').val('');
    $('#modalError').addClass('d-none').text('');
    const m = new bootstrap.Modal(document.getElementById('userModal')); 
    m.show();
  }
  
  function onSave(){
    let statusSave = $('#userModalTitle').text() == 'Create User' ? 0 : 1;
    const payload = {
      name: $('#f_name').val().trim(),
      email: $('#f_email').val().trim(),
      username: $('#f_username').val().trim(),
      password: $('#f_password').val(),
    };

    $('#modalError').addClass('d-none').text('');
    $.ajax({
      url: statusSave == 0 ? '/api/users' : '/api/users/' + $('#f_id').val(),
      method: statusSave == 0 ? 'POST' : 'PUT', 
      contentType:'application/json',
      data: JSON.stringify(payload),
      success: function(){
        bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
        getData();
      },
      error: function(xhr){
        $('#modalError').removeClass('d-none').text(xhr?.responseJSON?.message || 'Save failed');
      }
    });
  }

  async function onDelete(data){
    const ok = await msgConfirm('ยืนยันการลบพนักงาน?', 'เมื่อลบไปแล้วจะไม่สามารถย้อนกลับได้');
    if(!ok) return;
    $.ajax({
      url: '/api/users/' + data,
      method: 'DELETE', 
      contentType:'application/json',
      success: async function(){
        await msgNotify('success', 'ลบพนักงานสำเร็จ');
        getData();
      },
      error: function(xhr){
        msgNotify('error', 'ลบพนักงานไม่สำเร็จ', xhr.responseJSON.message);
      }
    });
  }
  
  function getTable(data) {
    if (table) table.destroy();
    table = $('#currentTable').DataTable({
      data: data,
      dom: 'lrtip',
      destroy: true,
      info: false,
      ordering: false,
      pageLength: 50,
      columns: [
        { data: 'username', name: 'username' },
        { data: 'name',     name: 'ชื่อ' },
        { data: 'email',     name: 'เมลล์' },
        {
          data: 'id',
          orderable: false,
          searchable: false,
          render: function(data){
            return `
              <button class="btn btn-warning btn-sm me-1 btn-edit" data-id="${data}" onclick="getDataByID(${data})">Edit</button>
              <button class="btn btn-danger btn-sm btn-del" data-id="${data}" onclick="onDelete(${data})">Delete</button>`;
          }
        }
      ]
    });
  }

</script>
@endpush
