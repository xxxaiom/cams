$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

function flatpicker() {
  $('#birthDate').attr('type', 'text');
  flatpickr('#birthDate', {
    dateFormat: 'Y-m-d', // Date and time format (YYYY-MM-DD HH:MM)
    altInput: true, // Use custom styled input
    altFormat: 'F j, Y', // Alternate format for displayed date and time
    theme: 'material_blue', // Optional: Material Blue theme
    maxDate: 'today',
    disableMobile: true
  });
}

$('#edit').click(function (e) {
  e.preventDefault();
  $('#hideEdit').hide();
  $('#showSubmit').show();
  $('#firstName, #middleName, #lastName, #suffix, #gender, #phoneNumber, #civil_status, #address, #birthDate').prop(
    'disabled',
    false
  );
  $('#firstName').focus();
  flatpicker();
});

$('#cancelEdit').click(function (e) {
  e.preventDefault();
  $('#showSubmit').hide();
  $('#hideEdit').show();
  $('#firstName, #middleName, #lastName, #suffix, #gender, #phoneNumber, #civil_status, #address, #birthDate').prop(
    'disabled',
    true
  );
  $('#firstName').focus();
  flatpicker();
});

$('#admin_details').on('submit', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'post',
    url: '/update-admin-details',
    data: $(this).serialize(),
    dataType: 'json',
    success: function (response) {
      if (response.code === 0) {
        $('#message').html(
          `
            <div class="alert alert-success" role="alert">
              ${response.message}
            </div>
          `
        );
        $(
          '#firstName, #middleName, #lastName, #suffix, #gender, #phoneNumber, #civil_status, #address, #birthDate'
        ).prop('disabled', true);
        flatpicker();
        $('#showSubmit').hide();
        $('#hideEdit').show();
      } else if (response.code === 1) {
        $('#message').html(
          `
            <div class="alert alert-danger" role="alert">
              ${response.message}
            </div>
          `
        );
      } else if (response.code === 3) {
        $('#message').html(
          `
            <div class="alert alert-info" role="alert">
              ${response.message}
            </div>
          `
        );
        $(
          '#firstName, #middleName, #lastName, #suffix, #gender, #phoneNumber, #civil_status, #address, #birthDate'
        ).prop('disabled', true);
        flatpicker();
        $('#showSubmit').hide();
        $('#hideEdit').show();
      } else {
        $('#message').html(
          `
            <div class="alert alert-success" role="alert">
              ${response.message}
            </div>
          `
        );
        $(
          '#firstName, #middleName, #lastName, #suffix, #gender, #phoneNumber, #civil_status, #address, #birthDate'
        ).prop('disabled', true);
        flatpicker();
        $('#showSubmit').hide();
        $('#hideEdit').show();
      }
    }
  });
});

$('#old_password').on('input', function (e) {
  e.preventDefault();
  let = password;
  password = $(this).val();
  console.log(password);

  if (password === '') {
    $('#errorMessage').html('');
    $('#password').val('');
    $('#password_confirmation').val('');
    document.getElementById('password').setAttribute('disabled', 'disabled');
    document.getElementById('password_confirmation').setAttribute('disabled', 'disabled');
  }

  $.ajax({
    type: 'get',
    url: '/getOldPassword',
    data: { password },
    dataType: 'json',
    success: function (response) {
      if (response.code === 0) {
        document.getElementById('password').removeAttribute('disabled');
        document.getElementById('password_confirmation').removeAttribute('disabled');
        $('#errorMessage').html('');
      } else if (response.code === 1 && password != '') {
        $('#errorMessage').html('Incorrect Password');
        $('#password').val('');
        $('#password_confirmation').val('');
        document.getElementById('password').setAttribute('disabled', 'disabled');
        document.getElementById('password_confirmation').setAttribute('disabled', 'disabled');
      }
    }
  });
});

$('#password_confirmation').on('input', function (e) {
  e.preventDefault();

  let confirmPass = $(this).val();
  let newPass = $('#password').val();

  if (confirmPass === '') {
    $('#checkSamePass').html('');
  }

  data = {
    confirmPass: confirmPass,
    newPass: newPass
  };

  $.ajax({
    type: 'get',
    url: '/confirmNewPassword',
    data: data,
    dataType: 'json',
    success: function (response) {
      if (response.code === 0) {
        $('#checkSamePass').html('');
      } else if (response.code === 1 && confirmPass != '') {
        $('#checkSamePass').html('New password not the same');
      }
    }
  });
});

$('#changePassword').on('submit', function (e) {
  e.preventDefault();

  let oldPass = $('#old_password').val();
  let password = $('#password').val();
  let password_confirmation = $('#password_confirmation').val();

  data = {
    oldPass: oldPass,
    password: password,
    password_confirmation: password_confirmation
  };

  console.log(data);

  $.ajax({
    type: 'post',
    url: '/changeAdminPassword',
    data: data,
    dataType: 'json',
    success: function (response) {
      if (response.code === 0) {
        $('#password').val('');
        $('#password_confirmation').val('');
        $('#checkOldPass').html('The password is the same with old password');
        setTimeout(() => {
          $('#checkOldPass').html('');
        }, 3000);
      } else if (response.code === 1) {
        $('#checkSamePass').html('Password not the same');
      } else if (response.code === 2) {
        $('#messageError').html(
          `
                  <div class="alert alert-success" role="alert">
                      Password successfully changed
                  </div>
              `
        );

        setTimeout(() => {
          $('#messageError').html('');
        }, 3000);

        $('#old_password').val('');
        $('#password').val('');
        $('#password_confirmation').val('');
        $('#checkSamePass').html('');
        document.getElementById('password').setAttribute('disabled', 'disabled');
        document.getElementById('password_confirmation').setAttribute('disabled', 'disabled');
      } else {
        $('#checkSamePass').html('');
        $('#messageError').html(
          `
                <div class="alert alert-danger" role="alert">
                    ${response.message}
                </div>
            `
        );
        setTimeout(() => {
          $('#messageError').html('');
        }, 3000);
        $('#password').val('');
        $('#password_confirmation').val('');
      }
    }
  });
});
