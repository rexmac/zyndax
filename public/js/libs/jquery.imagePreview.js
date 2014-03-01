/**
 * jQuery Image Preview Plugin v1.00 (29-JUL-2012)
 *
 * Copyright (c) 2012 Rex McConnell <rex@rexmac.com>
 *
 * Licensed under the MIT license:
 *   http://rexmac.github.com/license/mit.txt
 *
 */
/*global jQuery */
;(function($) {
  var xOffset = 30,
      yOffset = 10;

  $('img.preview')
    .hover(function(e) {
      var imgPreview = $('<div id="image-preview"></div>').append('<img alt="Image preview" />');
      imgPreview.css({
        'top': e.pageY,
        'left': e.pageX
      }).hide();
      $('body').append(imgPreview);
      imgPreview.find('img').attr('src', this.src.replace(/_t/, '')).load(function() {
        var h    = Math.floor(imgPreview.outerHeight() / 2),
            minY = $(window).scrollTop() + yOffset,
            y    = e.pageY - h;
        imgPreview.data('h', h);
        imgPreview.data('minY', minY);
        imgPreview.css({
          'top': (y < minY ? minY : y) + 'px',
          'left': (e.pageX + xOffset) + 'px'
        }).fadeIn('fast');
      });
    }, function(e) {
      $('#image-preview').remove();
    })
    .mousemove(function(e) {
      var imgPreview = $('#image-preview'),
          minY = imgPreview.data('minY'),
          y = e.pageY - imgPreview.data('h');
      imgPreview.css({
        'top': (y < minY ? minY : y) + 'px',
        'left': (e.pageX + xOffset) + 'px'
      });
    });
}(jQuery));
