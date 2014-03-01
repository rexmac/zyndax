/*global RM */
var validator = $('#userCreateForm').validate({
  rules: {
    username: {
      required: true,
      minlength: 2,
      remote: {
        url: '<?php echo $this->url(array(), "checkUser") ?>',
        error: RM.onAjaxError
      }
    },
    email: {
      required: true,
      email: true,
      remote: {
        url: '<?php echo $this->url(array(), "checkUser") ?>',
        error: RM.onAjaxError
      }
    },
    role: 'required',
    password: {
      required: true,
      password: true
    },
    passwordConfirm: {
      required: true,
      equalTo: '#password'
    },
    firstName: 'required',
    lastName: 'required'
  },
  messages: {
    username: {
      required: 'Enter a username for the user',
      minlength: $.format('Enter at least {0} characters'),
      remote: $.format('That name is unavailable.')
    },
    email: {
      required: 'Enter an email address for the user',
      email: 'Must be a valid email address',
      remote: 'That email address is already in use.'
    },
    passwordConfirm: {
      equalTo: 'Passwords do not match!'
    }
  }
});

$('#cancel').click(function(e) {
  e.preventDefault();
  window.location.href = '<?php echo $this->url(array(), "adminUsers"); ?>';
});

$('input[type="text"]').addClass('ui-widget-content ui-corner-all');
$('input[type="password"]').addClass('ui-widget-content ui-corner-all');
$('input[type="checkbox"]').addClass('ui-widget-content ui-corner-all');
$('#role').selectmenu({
  style: 'dropdown',
  maxHeight: 300,
  change: function(e) {
    validator.element($(this));
  }
});
$('#username').focus();

//$.ajaxSetup({async: false});
