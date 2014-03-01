/**
 * jQuery UI Combobox
 *
 * Copyright (c) 2012 Rex McConnell <rex@rexmac.com>
 *
 * Licensed under the MIT license:
 *   http://rexmac.github.com/license/mit.txt
 *
 *--
 *
 * 99% of code taken from the jQuery UI autocomplete/combobox demo at:
 * http://jqueryui.com/demos/autocomplete/#combobox
 *
 * Copyright (c) 2010 The jQuery Project and the jQuery UI Team
 */
/*global jQuery */
;(function($) {
  $.widget('ui.combobox', {
    options: {
      change: null,
      readonlyInput: false,
      select: null,
      validator: null
    },
    _create: function() {
      var self = this,
      select = this.element.hide(),
      selected = select.children(':selected'),
      value = selected.val() ? selected.text() : '',
      clearIcon = $('<span class="input-clear-icon"></span>'),
      tempDiv = $('<div/>').html(value),
      input = this.input = $('<input>');
      input.attr('class', select.attr('class'));
      input.width(select.width());
      value = tempDiv.text();
      tempDiv.remove();
      if(select.attr('disabled') === 'disabled') {
        input.attr('disabled', true);
      }
      if(self.options.readonlyInput) {
        input.attr('readonly', true);
      }
      input.insertAfter(select).val(value).attr('placeholder', 'Search...').autocomplete({
        delay: 0,
        minLength: 0,
        //disabled: select.attr('disabled') === 'disabled',

        source: function(request, response) {
          var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), 'i');
          response(select.children('option').map(function() {
            //var text = $(this).text();
            var text = $(this).attr('htmllabel');
            if(this.value && (!request.term || matcher.test(text))) {
              return {
                label: text.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(request.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<strong>$1</strong>"),
                //label: text.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(request.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "$1"),
                //value: text,
                value: text.replace(/<\/?span[^>]*>/g, ''),
                option: this
              };
            }
          }));
        },

        select: function(event, ui) {
          //console.log('select', this, $(this).val(), ui.item.option);
          ui.item.option.selected = true;
          self._trigger('selected', event, { item: ui.item.option });
          if(self.options.select !== null && typeof self.options.select === 'function') {
            self.options.select(ui.item.option);
          }
          $(this).next().fadeIn(300);
          // rex - remove focus and validate
          $(this).blur();
          if(self.options.validator !== null && typeof self.options.validator === 'object') {
            self.options.validator.element(select);
          }
        },

        change: function(event, ui) {
          //console.log('change', $(this).val(), ui.item);
          if(!ui.item) {
            var matcher = new RegExp('^' + $.ui.autocomplete.escapeRegex($(this).val()) + '$', 'i'),
            valid = false;
            select.children('option').each(function() {
              if($(this).text().match(matcher)) {
                this.selected = valid = true;
                if(self.options.validator !== null && typeof self.options.validator === 'object') {
                  self.options.validator.element(select);
                }
                return false;
              }
            });
            if(!valid) {
              // remove invalid value, as it didn't match anything
              $(this).val('').next().fadeOut(300);
              select.val('');
              input.data('autocomplete').term = '';
              if(self.options.validator !== null && typeof self.options.validator === 'object') {
                self.options.validator.element(select);
              }
              return false;
            }
          }
          if(self.options.validator !== null && typeof self.options.validator === 'object') {
            self.options.validator.element(select);
          }
          select.trigger('change'); // rex
        }
      })
      .addClass('ui-widget ui-widget-content ui-corner-left with-clear-icon');

      input.data('autocomplete')._renderItem = function(ul, item) {
        //console.log('item', item);
        return $('<li></li>').data('item.autocomplete', item).append('<a>' + item.label + '</a>').appendTo(ul);
      };

      this.button = $('<button type="button">&nbsp;</button>')
        .attr('tabIndex', -1)
        .attr('title', 'Show All Items')
        //.insertAfter(input)
        .button({
          icons: {
            primary: 'ui-icon-triangle-1-s'
          },
          text: false
        })
        .removeClass('ui-corner-all')
        .addClass('ui-corner-right ui-button-icon ui-combobox-button');

      input.keyup(function() {
        if($(this).val().length > 0) {
          $(this).next().fadeIn(300);
        } else {
          $(this).next().fadeOut(300);
        }
      });

      clearIcon.insertAfter(input)
        .after(this.button)
        .click(function() {
          $(this).prev().val('').autocomplete('widget').change();
          select.val('');
          //$(this).prev().val('');
          //self.value('');
          $(this).fadeOut(300);
        });
      if(select.val() === '') {
        clearIcon.hide();
      }

      if(select.attr('disabled') !== 'disabled') {
        this.button.click(function() {
          // close if already visible
          if(input.autocomplete('widget').is(':visible')) {
            input.autocomplete('close');
            return;
          }

          // work around a bug (likely same cause as #5265)
          $(this).blur();

          // pass empty string as value to search for, displaying all results
          input.autocomplete('search', '');
          input.focus();
        });
      }
    },

    destroy: function() {
      this.input.remove();
      this.button.remove();
      this.element.show();
      $.Widget.prototype.destroy.call(this);
    },

    get: function() {
      return this;
    },

    value: function(v) {
      var self = this;
      this.element.children('option').each(function() {
        if($(this).val() === v) {
          //self.input.val(v).blur();
          // rex
          if(v === '') {
            self.input.val('').autocomplete('widget').change();
          } else {
            //self.input.val($(this).text()).autocomplete('widget').change();
            self.input.val($(this).attr('htmllabel').replace(/<\/?span[^>]*>/g, '') || $(this).text()).autocomplete('widget').change();
          }
          return false;
        }
      });
    }

  });
}(jQuery));
