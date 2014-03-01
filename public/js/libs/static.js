/*global Notifier, sprintf, RM */
if(!Array.indexOf) {
  Array.prototype.indexOf = function(obj, start) {
    var i = start || 0, l = this.length;
    for(; i < l; ++i) {
      if(this[i] === obj) {
        return i;
      }
    }
    return -1;
  };
}

if(!String.ucfirst) {
  String.prototype.ucfirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
  };
}

(function($) {
  var nav = $('ul.nav'),
      RM = window.RM = {};

  // Provide real URLs for JS-only anchors
  $('a[href="#"]').attr('href', window.location.href);

  // Superfish navbar
  if(nav && nav.length > 0) {
    nav.superfish();
  }

  // Default format for date pickers
  $.datepicker.setDefaults({dateFormat: 'yy-mm-dd'});

  // Close error/success message box on subsequent AJAX requests
  $(document).ajaxStart(function() {
    $('#content').showMessage.closeMessage();
  });

  // Style buttons
  $('button, input:submit').button();

  // Highlight <tr> on hover
  $('table.report tbody tr').hover(function() {
    $(this).addClass('highlight');
  },
  function() {
    $(this).removeClass('highlight');
  });

  // jQuery.dataTable - Default options
  $.extend(true, $.fn.dataTable.defaults, {
    'iDisplayLength': 25,
    'oLanguage': {
      'sZeroRecords': 'No data matches the given filters'
    }
  });

  // jQuery.validate - Default settings
  $.validator.setDefaults({
    errorClass: 'invalid',
    errorPlacement: function(error, element) {
      error.appendTo(element.parent());
    },
    onkeyup: false,
    success: 'valid'
  });

  // jQuery.validate - Custom password rule
  $.validator.addMethod('password', function(value, element) {
    var result = this.optional(element) || value.length >= 6 && /\d/.test(value) && /[a-z]/i.test(value),
        validator = null;
    if(!result) {
      element.value = '';
      validator = this;
      setTimeout(function() {
        validator.blockFocusCleanup = true;
        element.focus();
        validator.blockFocusCleanup = false;
      }, 1);
    }
    return result;
  }, "Your password must be at least 6 characters long and contain at least one number and one character.");

  // jQuery.validate - Custom phone number rule
  $.validator.addMethod("phoneUS", function(phoneNumber, element) {
    phoneNumber = phoneNumber.replace(/\s+/g, '');
    return this.optional(element) || phoneNumber.length > 9 && phoneNumber.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
  }, 'Please specify a valid phone number');

  // Pad int with leading 0
  RM.zeroPad = function(n) {
    return (n < 10 ? '0' : '') + n;
  };

  // Confirm dialog
  RM.confirmDialog = function(title, message, yesCallback, noCallback) {
    $('<div class="alignContentCenter"></div>').html(message).dialog({
      autoOpen: true,
      draggable: false,
      resizable: false,
      minHeight: 0,
      width: 'auto',
      modal: true,
      title: title || 'Confirm',
      buttons: {
        'Yes': function() {
          if($.isFunction(yesCallback)) {
            yesCallback.apply();
          }
          $(this).dialog('close');
        },
        'No': function() {
          if($.isFunction(noCallback)) {
            noCallback.apply();
          }
          $(this).dialog('close');
        }
      },
      close: function() {
      }
    });
  };

  RM.getMonetaryCssClass = function(v) {
    var c = 'monetary ';
    v = parseFloat(('' + v).replace(/[^0-9eE.-]/g, ''));
    if(v > 0) {
      c += 'positive';
    } else if(v < 0) {
      c += 'negative';
    } else {
      c += 'neutral';
    }
    return c;
  };

  RM.getPrettyCampaignName = function(campaignName, campaignId, campaignStatus) {
    return sprintf('%s%s%s',
      campaignName,
      ' <span class="entity-id">(' + campaignId + ')</span>',
      campaignStatus === 'paused' ? ' <span class="status paused">(PAUSED)</span>' : ''
    );
  };

  // AJAX error handler
  RM.onAjaxError = function(xhr, statusText, errorThrown) {
    var context = $(this),
        data = $.parseJSON(xhr.responseText),
        errors = [];

    // If "local" $.ajax({...}) method did not set context, then use div#content
    if(typeof context[0].xhr === 'function') {
      context = $('#content');
    }

    if(data.redirect) {
      context.showMessage({
        thisMessage: ['We are sorry, but your sesison has expired. You will now be redirected to the <a href="' + data.redirect + '" title="Login">login</a> page.'],
        className: 'error'
      });
      window.setTimeout(function() {
        window.location.replace(data.redirect);
      }, 5000);
    }
    if(data.message) {
      errors.push(data.message);
    } else if(typeof data.messages === 'object') {
      $.each(data.messages.error, function(k, v) {
        if(typeof v === 'object') {
          $.each(v, function(fieldName, error) {
            var ul = fieldName + '<ul>';
            $.each(error, function(type, message) {
              ul += '<li>' + message + '</li>';
            });
            ul += '</ul>';
            errors.push(ul);
          });
        } else if(typeof v === 'string') {
          errors.push(v);
        }
      });
    } else {
      errors.push('Unknown error');
    }

    //context.showMessage({thisMessage: errors, className: 'error'});
    Notifier.error(errors.join(''));
  };

  RM.onDateChange  = function() {
    var start = $.datepicker.formatDate('@', new Date($('#dateRange-start').val() + 'T00:00:00')) / 1000,
        stop  = $.datepicker.formatDate('@', new Date($('#dateRange-stop').val() + 'T23:59:59')) / 1000,
        i = 1;
    for(; i < 8; ++i) {
      if(start === parseInt(RM.timestamps[i][0], 10) && stop === parseInt(RM.timestamps[i][1], 10)) {
        $('#dateRange-range').selectmenu('index', i - 1);
        return true;
      }
    }
    $('#dateRange-range').selectmenu('index', 7);
    return true;
  };

  RM.onDateSelect = function() {
    var d1 = new Date(),
        d2 = new Date(),
        s = parseInt($(this).val(), 10);

    if(s === 2) { // Yesterday
      d1.setDate(d1.getDate() - 1);
      d2.setMonth(d1.getMonth());
      d2.setDate(d1.getDate());
    } else if(s === 3) { // Last 7 days
      d1.setDate(d1.getDate() - 6);
    } else if(s === 4) { // MTD
      d1.setDate(1);
    } else if(s === 5) { // Last MTD
      d1.setDate(1);
      d1.setMonth(d1.getMonth() - 1);
      d2.setFullYear(d1.getFullYear());
      d2.setMonth(d1.getMonth());
      if(d2.getMonth() !== d1.getMonth()) {
        if(d1.getMonth() === 1) {
          if(d1.getFullYear() % 4 === 0) {
            d2.setDate(29);
          } else {
            d2.setDate(28);
          }
        } else {
          d2.setDate(30);
        }
        d2.setMonth(d1.getMonth());
      }
    } else if(s === 6) { // Last month
      d1.setDate(1);
      d1.setMonth(d1.getMonth() - 1);
      d2.setFullYear(d1.getFullYear());
      d2.setMonth(d1.getMonth());
      d2.setDate(31);
      if(d2.getMonth() !== d1.getMonth()) {
        if(d1.getMonth() === 1) {
          if(d1.getFullYear() % 4 === 0) {
            d2.setDate(29);
          } else {
            d2.setDate(28);
          }
        } else {
          d2.setDate(30);
        }
        d2.setMonth(d1.getMonth());
      }
    } else if(s === 7) { // YTD
      d1.setDate(1);
      d1.setMonth(0);
    }

    $('#dateRange-start').val($.datepicker.formatDate($.datepicker.ISO_8601, d1));
    $('#dateRange-stop').val($.datepicker.formatDate($.datepicker.ISO_8601, d2));
  };

  // jQuery AJAX defaults
  $.ajaxSetup({
    async: true,
    dataType: 'json',
    error: RM.onAjaxError,
    type: 'POST'
  });

}(jQuery));
