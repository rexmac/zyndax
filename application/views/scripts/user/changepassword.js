var validator = $('#userPasswordChangeForm').validate({
  rules: {
    oldPassword: 'required',
    newPassword: {
      required: true,
      password: true
    },
    passwordConfirm: {
      required: true,
      equalTo: '#newPassword'
    }
  }
});

$('input[type="text"], input[type="password"]').addClass('ui-widget-content ui-corner-all');
$('#oldPassword').focus();
