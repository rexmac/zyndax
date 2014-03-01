/*global Notifier, RM */
var $table = $('#users');

RM.makeUserActivateRequest = function(currentTarget) {
  var uid     = currentTarget.attr('id').split('-')[1],
      ctr     = currentTarget.parent().parent();

  $.ajax({
    beforeSend: function(xhr, settings) {
      ctr.parent().showLoading();
    },
    complete: function(xhr, statusText) {
      ctr.parent().hideLoading();
    },
    data: 'active=1&format=json',
    success: function(data, statusText, xhr) {
      var bgColor = ctr.css('background-color');

      // Remove activate link
      currentTarget.remove();

      // Update row
      ctr.removeClass('new');
      ctr.animate({backgroundColor: '#FFFF99'}, 1000, 'swing', function() {
        ctr.animate({backgroundColor: bgColor}, 1000, 'swing', function() {
          $table.fnUpdate(ctr.attr('class'), ctr[0], 0);
        });
      });
    },
    url: '<?php echo $this->baseUrl(); ?>' + '/users/edit/' + uid
  });
};

RM.makeUserLockRequest = function(currentTarget) {
  var locked = currentTarget.hasClass('ss_lock') ? 1 : 0,
      ctr    = currentTarget.parent().parent(),
      uid    = ctr.attr('id').split('-')[1];

  $.ajax({
    beforeSend: function(xhr, settings) {
      ctr.parent().showLoading();
    },
    complete: function(xhr, statusText) {
      ctr.parent().hideLoading();
    },
    data: 'locked=' + locked + '&format=json',
    success: function(data, statusText, xhr) {
      var a = currentTarget,
          bgColor = ctr.css('background-color');

      if(locked) {
        ctr.addClass('locked');
        a.removeClass('ss_lock').addClass('ss_lock_delete').html('Unlock');

        // Notify user
        Notifier.success('User ' + data.userName + ' has been locked');
      } else {
        ctr.removeClass('locked');
        a.removeClass('ss_lock_delete').addClass('ss_lock').html('Lock');

        // Notify user
        Notifier.success('User ' + data.userName + ' has been unlocked');
      }

      ctr.animate({backgroundColor: '#ffff99'}, 1000, 'swing', function() {
        ctr.animate({backgroundColor: bgColor}, 1000, 'swing', function() {
          $table.fnUpdate(ctr.attr('class'), ctr[0], 0);
        });
      });
    },
    url: '<?php echo $this->baseUrl(); ?>' + '/users/edit/' + uid
  });
};

$('.userActivateLink').click(function(e) {
  e.preventDefault();
  RM.makeUserActivateRequest($(this));
});

$('.userLockLink').click(function(e) {
  e.preventDefault();
  RM.makeUserLockRequest($(this));
});

$table.dataTable({
  'aaSorting': [[1, 'asc']],
  'aoColumnDefs': [
    {'bSearchable': false, 'bVisible': false, 'aTargets': [0]},
    {'bSearchable': false, 'bSortable': false, 'aTargets': [7]}
  ],
  'sDom': 'l<"status-filter">frtip'
});
$('div.status-filter').html('<label>Status: <select id="status-filter-select"><option value="active">active</option><option value="all">all</option><option value="locked">locked</option><option value="new">new</option></select></label>');
$('#status-filter-select').change(function() {
  var v = $(this).val();
  $table.fnFilter(v === 'all' ? '' : v, 0);
}).selectmenu({style: 'dropdown', maxHeight: 300});
$table.fnFilter('active', 0);
