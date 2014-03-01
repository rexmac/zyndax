/**
 * jQuery showLoading plugin - revised v1.0
 *
 * Copyright (c) 2012 Rex McConnell <rex@rexmac.com>
 *
 * Copyright (c) 2009 Jim Keller
 * Context - http://www.contextllc.com
 *
 * Dual licensed under the MIT and GPL licenses.
 *
 */
jQuery.fn.showLoading = function(b) {
  var c,
      options = {
        addClass: "",
        beforeShow: "",
        afterShow: "",
        hPos: "center",
        vPos: "center",
        indicatorZIndex: 5001,
        overlayZIndex: 5E3,
        parent: "",
        marginTop: 0,
        marginLeft: 0,
        overlayWidth: null,
        overlayHeight: null,
        overlayBorder: false
      };

  jQuery.extend(options, b);
  var indicator = jQuery("<div></div>"),
      overlay = jQuery("<div></div>"),
      e, f, g, h,
      self = jQuery(this);
  c = options.indicatorID ? options.indicatorID : self.attr("id");
  indicator.attr("id", "loading-indicator-" + c);
  indicator.addClass("loading-indicator");
  options.addClass && indicator.addClass(options.addClass);
  overlay.css("display", "none");

  jQuery(document.body).append(overlay);
  overlay.attr("id", "loading-indicator-" + c + "-overlay");
  overlay.addClass("loading-indicator-overlay");
  options.addClass && overlay.addClass(options.addClass + "-overlay");

  e = self.css("border-top-width");
  c = self.css("border-left-width");
  e = isNaN(parseInt(e, 10)) ? 0 : e;
  c = isNaN(parseInt(c, 10)) ? 0 : c;
  c = self.offset().left + parseInt(c, 10);
  g = self.offset().top + parseInt(e, 10);
  e = null !== options.overlayWidth ? options.overlayWidth : parseInt(self.width(), 10) + parseInt(self.css("padding-right"), 10) + parseInt(self.css("padding-left"), 10);
  f = null !== options.overlayHeight ? options.overlayHeight : parseInt(self.height(), 10) + parseInt(self.css("padding-top"), 10) + parseInt(self.css("padding-bottom"), 10);
  h = self.css('transform') || self.css('-ms-transform') || self.css('-o-transform') || self.css('-moz-transform') || self.css('-webkit-transform');
  if(h !== null) {
    h = h.match(/matrix\(([0-9.]+), [0-9.]+, [0-9.]+, ([0-9.]+), /);
    if(h !== null && h[1] > 0 && h[1] === h[2]) {
      e *= parseFloat(h[1], 10);
      f *= parseFloat(h[2], 10);
    }
  }
  overlay.css("width", e.toString() + "px");
  overlay.css("height", f.toString() + "px");
  e = 0;
  f = 0;
  if(options.overlayBorder === true) {
    e = parseInt(self.css('border-left-width'), 10);
    f = parseInt(self.css('border-top-width'), 10);
  }
  overlay.css("left", (c - e).toString() + "px");
  overlay.css("top", (g - f).toString() + "px");
  overlay.css("position", "absolute");
  overlay.css("z-index", options.overlayZIndex);
  options.overlayCSS && overlay.css(options.overlayCSS);
  indicator.css("display", "none");
  jQuery(document.body).append(indicator);
  indicator.css("position", "absolute");
  indicator.css("z-index", options.indicatorZIndex);
  e = g;
  options.marginTop && (e += parseInt(options.marginTop, 10));
  options.marginLeft && (c += parseInt(options.marginTop, 10));
  "center" == options.hPos.toString().toLowerCase() ? indicator.css("left", (c + (overlay.width() - parseInt(indicator.width(), 10)) / 2).toString() + "px") :
    "left" == options.hPos.toString().toLowerCase() ? indicator.css("left", (c + parseInt(overlay.css("margin-left"), 10)).toString() + "px") :
    "right" == options.hPos.toString().toLowerCase() ? indicator.css("left", (c + (overlay.width() - parseInt(indicator.width(), 10))).toString() + "px") :
    indicator.css("left", (c + parseInt(options.hPos, 10)).toString() + "px");
  "center" == options.vPos.toString().toLowerCase() ? indicator.css("top", (e + (overlay.height() - parseInt(indicator.height(), 10)) / 2).toString() + "px") :
    "top" == options.vPos.toString().toLowerCase() ? indicator.css("top", e.toString() + "px") :
    "bottom" == options.vPos.toString().toLowerCase() ? indicator.css("top", (e + (overlay.height() - parseInt(indicator.height(), 10))).toString() + "px") :
    indicator.css("top", (e + parseInt(options.vPos, 10)).toString() + "px");
  options.css && indicator.css(options.css);
  c = {
    overlay: overlay,
    indicator: indicator,
    element: this
  };
  "function" == typeof options.beforeShow && options.beforeShow(c);
  overlay.show();
  indicator.show();
  "function" == typeof options.afterShow && options.afterShow(c);
  return this;
};
jQuery.fn.hideLoading = function(b) {
  var c = {};
  jQuery.extend(c,b);
  indicatorID = c.indicatorID ? c.indicatorID : jQuery(this).attr("id");
  jQuery(document.body).find("#loading-indicator-" + indicatorID).remove();
  jQuery(document.body).find("#loading-indicator-" + indicatorID + "-overlay").remove();
  return this;
};
