$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

function reset() {
  $('#user_username').val('');
  $('#user_password').val('');
  $('#user_username').focus();
}

function focus() {
  $('#user_username').focus();
  $('#user_username').val('');
  $('#user_password').val('');
  $('#user_message').text('');
}

$('.btn-focus').click(function (e) {
  e.preventDefault();
  focus();
});

// For modal input focus
var myModal = document.getElementById('register_user');
var myInput = document.getElementById('firstname');

myModal.addEventListener('shown.bs.modal', function () {
  myInput.focus();
});

// To reset data inputs
$('.close-btn').click(function (e) {
  var form = document.getElementById('registerCitizenInfo');
  form.reset();
  $('#register_message').text('');
});

// Close modal
function closeModal() {
  $('#register_user').modal('toggle');
}

// For User Registration
$(function(){
$('#registerCitizenInfo').on('submit', function (e) {
  e.preventDefault();

  $.ajax({
    url: '/register_user',
    method: 'post',
    data: $(this).serialize(),
    dataType: 'json',
    beforeSend:function(){
      $('#loginuser').text('Creating...');                       
      $('#loginuser').prop('disabled', true);
    },
    success: function (response) {

      console.log(response.code);
      $('#loginuser').text('Register');                       
      $('#loginuser').prop('disabled', false);

      if (response.code == 0) {
        closeModal();
        $('#user_message').html(
          `<div class="alert alert-success" role="alert">
                  ${response.message}
              </div>`
        );
      }
      if (response.code == 1) {
        $('#register_message').html(
          `<div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>`
        );
      }
      if (response.code == 2) {
        $('#register_message').html(
          `<div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>`
        );
      }
    }
  });
});
});

// For user login
$('#user_login').on('submit', function (e) {
  e.preventDefault();

  $.ajax({
    type: 'post',
    url: '/auth/user_login',
    data: $(this).serialize(),
    dataType: 'json',
    success: function (response) {
      if (response.code == 0) {
        $('#user_message').html(
          `<div class="alert alert-success" role="alert">
                      ${response.message}
                  </div>`
        );
        setTimeout(() => {
          location.href = '/user-dashboard';
        }, 1000);
      }
      if (response.code == 1) {
        $('#user_message').html(
          `<div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>`
        );
        reset();
        setTimeout(() => {
          $('#user_message').html('');
        }, 5000);
      }
      if (response.code == 2) {
        $('#user_message').html(
          `<div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>`
        );
        setTimeout(() => {
          $('#user_message').html('');
        }, 5000);
      }
    }
  });
});
