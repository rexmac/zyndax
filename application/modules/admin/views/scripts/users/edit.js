/*global RM */
var validator = $('#userEditForm').validate({
  onkeyup: false,
  rules: {
    username: {
      required: true,
      minlength: 2,
      remote: {
        url: '<?php echo $this->url(array("userId" => $this->userId), "checkUser"); ?>',
        error: RM.onAjaxError
      }
    },
    email: {
      required: true,
      email: true,
      remote: {
        url: '<?php echo $this->url(array("userId" => $this->userId), "checkUser"); ?>',
        error: RM.onAjaxError
      }
    },
    role: 'required',
    password: {
      required: '#newPassword:filled',
      password: true
    },
    newPassword: {
      required: false,
      password: true
    },
    newPasswordConfirm: {
      required: '#newPassword:filled',
      equalTo: '#newPassword'
    }
  },
  messages: {
    username: {
      required: 'Enter a username for the user',
      minlength: $.format('Enter at least {0} characters'),
      remote: 'That name is unavailable.'
    },
    email: {
      required: 'Enter an email address for the user',
      email: 'Must be a valid email address',
      remote: 'That email address is already in use.'
    },
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

$('#cancel').click(function(e) {
  e.preventDefault();
  window.location.href = '<?php echo $this->url(array(), "adminUsers"); ?>';
});

$('#role').selectmenu({
  style: 'dropdown',
  maxHeight: 300,
  change: function(e) {
    validator.element($(this));
  }
});
$('input[type="text"]').addClass('ui-widget-content ui-corner-all');
$('input[type="password"]').addClass('ui-widget-content ui-corner-all');
$('input[type="checkbox"]').addClass('ui-widget-content ui-corner-all');
$('#username').focus();

$.ajaxSetup({async: false});
