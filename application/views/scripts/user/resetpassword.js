var validator = $('#user_passwordreset_form').validate({
  onkeyup: false,
  rules: {
    password: {
      required: true,
      password: true
    },
    passwordConfirm: {
      required: true,
      equalTo: '#password'
    }
  },
  messages: {
    passwordConfirm: {
      equalTo: 'Passwords do not match!'
    }
  },
  errorClass: 'invalid',
  errorPlacement: function(error, element) {
    error.appendTo(element.parent());
  },
  success: 'valid'
});

$('input[type="password"]').addClass('ui-widget-content ui-corner-all');
$('#password').focus();
