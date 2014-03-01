/*global RM */
var validator = $('#group_create_form').validate({
  rules: {
    name: {
      required: true,
      minlength: 2,
      remote: {
        url: '<?php echo $this->url(array(), "adminCheckGroup") ?>',
        error: RM.onAjaxError
      }
    },
    description: 'required'
  },
  messages: {
    name: {
      required: 'Enter a name for the group',
      minlength: $.format('Enter at least {0} characters'),
      remote: $.format('{0} is already in use')
    },
    description: 'Enter a description'
  },
  errorClass: 'invalid',
  errorPlacement: function(error, element) {
    error.appendTo(element.parent());
  },
  success: 'valid'
});

$.ajaxSetup({async: false});
