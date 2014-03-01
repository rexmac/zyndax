/*global RM */
RM.makeGroupLockRequest = function(currentTarget) {
  var locked  = currentTarget.hasClass('ss_lock') ? 1 : 0,
      gid     = currentTarget.attr('id').substr(14),
      tr      = $('#group-' + gid),
      bgColor = tr.css('background-color');

  $.ajax({
    beforeSend: function(xhr, settings) {
      tr.showLoading();
    },
    complete: function(xhr, statusText) {
      tr.hideLoading();
    },
    data: 'locked=' + locked + '&format=json',
    dataType: 'json',
    error: RM.onAjaxError,
    success: function(data, statusText, xhr) {
      var a = $('#groupLockLink-' + data.groupId),
          o = $('option[value="locked"]'),
          matches = null,
          html = '';

      if(a.hasClass('ss_lock')) {
        tr.addClass('locked');
        a.removeClass('ss_lock').addClass('ss_lock_delete').html('Unlock');
        matches = o.html().match(/locked \((\d+)\)/);
        html = 'locked (' + (parseInt(matches[1], 10) + 1) + ')';
        o.html(html).attr('label', html);
      } else {
        tr.removeClass('locked');
        a.removeClass('ss_lock_delete').addClass('ss_lock').html('Lock');
        matches = o.html().match(/locked \((\d+)\)/);
        html = 'locked (' + (parseInt(matches[1], 10) - 1) + ')';
        o.html(html).attr('label', html);
      }

      tr.animate({backgroundColor: '#ffff99'}, 1000, 'swing', function() {
        tr.animate({backgroundColor: bgColor}, 1000);
      });
    },
    type: 'POST',
    url: '<?php echo $this->baseUrl(); ?>' + '/groups/edit/' + gid
  });
};

$('.groupLockLink').click(function() {
  e.preventDefault();
  RM.makeGroupLockRequest($(this));
});
