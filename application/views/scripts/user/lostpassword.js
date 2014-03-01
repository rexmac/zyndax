var validator = $('#user_lostpassword_form').validate({
  rules: {
    email: {
      required: true,
      email: true
    }
  },
  messages: {
    email: {
      required: 'Enter an email address for the user',
      email: 'Must be a valid email address'
    }
  },
  errorPlacement: function(error, element) {
    error.appendTo(element.parent());
  }
});

$('input[type="text"],input[type="password"]').addClass('ui-widget-content ui-corner-all');
$('#email').focus();
