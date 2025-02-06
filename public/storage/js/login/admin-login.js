$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

function reset() {
  $('#username').val('');
  $('#password').val('');
  $('#username').focus();
}

$('#login').on('submit', function (e) {
  e.preventDefault();

  $.ajax({
    type: 'post',
    url: '/admin-login',
    data: $(this).serialize(),
    dataType: 'json',
    success: function (response) {
      console.log(userRole);
      if (response.code === 0) {
        $('#message').html(
          `
            <div class="alert alert-success" role="alert">
                ${response.message}
            </div>
          `
        );
        setTimeout(() => {
          if (userRole === 'admin') {
            location.href = '/admin-map';
          } else {
            location.href = '/accounts-admin';
          }
        }, 1000);
      } else if (response.code === 1) {
        $('#message').html(
          `
              <div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>
            `
        );
        reset();
        setTimeout(() => {
          $('#message').html('');
        }, 3000);
      } else if (response.code === 2) {
        $('#message').html(
          `
              <div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>
            `
        );
        reset();
        setTimeout(() => {
          $('#message').html('');
        }, 3000);
      } else if (response.code === 3) {
        $('#message').html(
          `
              <div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>
            `
        );
        setTimeout(() => {
          $('#message').html('');
        }, 3000);
      }
    }
  });
});
