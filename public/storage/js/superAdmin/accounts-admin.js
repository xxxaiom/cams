$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(document).ready(function () {
  var myModal = document.getElementById('newAdmin');
  var myInput = document.getElementById('username');

  myModal.addEventListener('shown.bs.modal', function () {
    myInput.focus();
  });
});

function clearInput() {
  $('#username').val('');
  $('#password').val('');
  $('#password_confirmation').val('');
  $('#message').html('');
}

$('#newAdminCred').on('submit', function (e) {
  e.preventDefault();

  console.log($('#username').val());

  $.ajax({
    type: 'post',
    url: '/new-admin',
    data: $(this).serialize(),
    dataType: 'json',
    success: function (response) {
      console.log(response.message);
      if (response.code === 0) {
        $('#message').html(
          `
            <div class="alert alert-success" role="alert">
              New admin account created!
            </div>
          `
        );
        setTimeout(() => {
          $('#newAdmin').modal('toggle');
          clearInput();
        }, 2000);
      } else if (response.code === 1) {
        $('#message').html(
          `
            <div class="alert alert-danger" role="alert">
              System Error!
            </div>
          `
        );
        setTimeout(() => {
          $('#message').html('');
        }, 2000);
      } else {
        $('#message').html(
          `
            <div class="alert alert-danger" role="alert">
              ${response.message}
            </div>
          `
        );
        setTimeout(() => {
          $('#message').html('');
        }, 2000);
      }
    }
  });
});

$('.close-btn').click(function (e) {
  e.preventDefault();
  clearInput();
});
