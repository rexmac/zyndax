/**
 * jQuery Drop-down Menu Plugin v1.00 (29-JUL-2012)
 *
 * Copyright (c) 2012 Rex McConnell <rex@rexmac.com>
 *
 * Licensed under the MIT license:
 *   http://rexmac.github.com/license/mit.txt
 */
/*global jQuery */
;(function($) {
  $("ul.dropdown li").hover(function() {
    $(this).addClass("hover");
    $('ul:first', this).css('display', 'block');
  }, function() {
    $(this).removeClass("hover");
    $('ul:first', this).css('display', 'none');
  });
  //$("ul.dropdown li ul li:has(ul)").find("a:first").append(" &raquo; ");
}(jQuery));
