/*global RM */
var validator = $('#userUpdateForm').validate({
  rules: {
    firstname: {
      required: true,
      minlength: 2
    },
    lastname: {
      required: true,
      minlength: 2
    },
    email: 'required',
    phone: {
      phoneUS: true
    },
    companyName: {
      required: true,
      minlength: 2
    },
    address1: {
      required: true,
      minlength: 3
    },
    city: {
      required: true,
      minlength: 2
    },
    state: {
      required: true,
      minlength: 2
    },
    country: {
      required: true
    },
    postalCode: {
      required: true,
      minlength: 5
    },
    companyPhone: {
      required: true,
      phoneUS: true
    },
    website: {
      uri: true
    }
  },
  errorPlacement: function(error, element) {
    if(element.is('select')) {
      if(element.next().hasClass('ui-autocomplete-input')) {
        error.insertAfter(element.next().next());
      } else {
        error.insertAfter(element.next());
      }
    } else if(element.is('input[type="checkbox"]')) {
      error.appendTo(element.parent().parent().parent());
    } else {
      error.appendTo(element.parent());
    }
  }
});

RM.addSocialNetworkField = function() {
  var form = $('#user_update_form'),
      lastSelect = $('select[name^="social"][name$="[network]"]:last', form),
      container = lastSelect.parent(),
      i = lastSelect.attr('name').match(/social(\d*)\[network\]/),
      newText = null,
      newSelect = null,
      options = null;

  i = (i[1].length > 0 ? parseInt(i[1], 10) + 1 : 2);
  newText   = $('<input type="text" name="social' + i + '[name]" id="social' + i + '-name">');
  newSelect = $('<select class="socialNetworkMenu" name="social' + i + '[network]" id="social' + i + '-network"></select>');
  container
    .append($('<label for="social' + i + '" class="optional hidden">Social Identity ' + i + ':</label>'))
    .append(newText)
    .append('&nbsp;&nbsp;')
    .append(newSelect)
    .append($('a.addSocialNetworkFieldLink', container))
    .append($('<br>'));

  options = $('option', lastSelect);
  options.each(function(i, option) {
    option = $(option);
    $('<option value="' + option.val() + '">')
      .text(option.text())
      .attr('label', option.attr('label'))
      .addClass(option.attr('class'))
      .appendTo(newSelect);
  });

  newText.addClass('ui-widget-content ui-corner-all');
  newSelect.selectmenu({
    style: 'dropdown',
    icons: [
      {find: '.icon-aim'},
      {find: '.icon-fb'},
      {find: '.icon-gtalk'},
      {find: '.icon-li'},
      {find: '.icon-live'},
      {find: '.icon-skype'},
      {find: '.icon-twitter'},
      {find: '.icon-yim'}
    ]
  });
  //container.insertAfter(lastSelect.parent());
  newText.focus();
};

$('input[type="text"], input[type="password"], textarea').addClass('ui-widget-content ui-corner-all');
$('#country').combobox({validator: validator}).children('option[value="--"]').val('');
$('#taxClass').selectmenu({
  style: 'dropdown',
  maxHeight: 300,
  change: function(e) {
    validator.element($(this));
  }
});
$('select.socialNetworkMenu').selectmenu({
  style: 'dropdown',
  maxHeight: 300,
  icons: [
    {find: '.icon-aim'},
    {find: '.icon-fb'},
    {find: '.icon-gtalk'},
    {find: '.icon-li'},
    {find: '.icon-live'},
    {find: '.icon-skype'},
    {find: '.icon-twitter'},
    {find: '.icon-yim'}
  ]
});
$('a.addSocialNetworkFieldLink').click(function(e) {
  e.preventDefault();
  RM.addSocialNetworkField();
});

$('#firstName').focus();

$.ajaxSetup({async: false});
