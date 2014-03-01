var $iframe = $('#pagePreview'),
    iframeDoc,
    $iframeBody;

$iframe.load(function() {
  var $overlay = $('<div/>');
  console.log('iframe loaded');
  iframeDoc = $iframe[0].contentDocument;
  $iframeBody = $(iframeDoc.body);

  $overlay.css({
    'height': $iframeBody.height(),
    'width':  $iframeBody.width(),
    'position': 'absolute',
    'top': 0,
    'left': 0
  });

  $iframeBody.append($overlay);

  $iframe.hideLoading();
}).showLoading({
  overlayBorder: true,
  afterShow: function() {
    $iframe.attr('src', '<?php echo $this->baseUrl(); ?>' + '/site/preview');
  }
});

/*
<div class="upload-progress">
  <div class="bar"></div>
  <div class="percent"></div>
</div>
*/

var $form = $('#siteThemeForm'),
    //$uploadProgressBar = $('.bar', $form),
    //$uploadProgressLabel = $('.percent', $uploadProgressBar);
    $uploadProgressBar = $('<div class="bar"></div>'),
    $uploadProgressLabel = $('<div class="percent"></div>');

if(window.ProgressEvent) { // XHR2 support
  $('#siteThemeForm-logo').after($('<div class="upload-progress"></div>').append($uploadProgressBar).append($uploadProgressLabel));

  $form.ajaxForm({
    beforeSend: function() {
      $uploadProgressBar.width('0%');
      $uploadProgressLabel.html('0%');
      $iframe.showLoading({overlayBorder: true});
    },
    uploadProgress: function(e, position, total, percentComplete) {
      $uploadProgressBar.width(percentComplete + '%');
      $uploadProgressLabel.html(percentComplete + '%');
    },
    complete: function(xhr) {
      var data = $.parseJSON(xhr.responseText),
          url = '<?php echo $this->url(array(), "previewImage"); ?>' + '?t=theme&n=' + data.filename;
      console.log('Complete', xhr);
      console.log('url', url);

      $iframeBody.find('#site-logo > a > img').load(function() {
        $(this).attr('width', data.width).attr('height', data.height).off('load');
        $iframe.hideLoading();
      }).attr('src', url);
    }
  });
}
