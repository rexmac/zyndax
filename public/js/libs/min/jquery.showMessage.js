(function(a,c){var f;a.fn.showMessage=function(c){var b=a.extend({thisMessage:[""],className:"notification",position:"top",opacity:90,useEsc:!1,displayNavigation:!0,autoClose:!1,delayTime:5E3},c);this.each(function(){var d=a("<div></div>").css({display:"none",position:"relative"}).addClass("showMessage").addClass("messages").addClass(b.className);if(b.displayNavigation){var e=a('<div class="messageNav"></div>').css({position:"absolute",top:"2px",right:"5px","font-weight":"bold","font-size":"small",
height:"16px","line-height":"14px"});b.useEsc&&a(e).html("Esc Key or ");var c=a("<a></a>").attr({href:"#",title:"close"}).click(function(){a(d).fadeOut();clearTimeout(f);return!1}).text("x");a(e).append(c);a(d).append(e)}e=a("<div></div>").css({});if("string"===typeof b.thisMessage)a(e).append(b.thisMessage);else if(a.isArray(b.thisMessage)){for(var c=a("<ul></ul>").css({}),g=0;g<b.thisMessage.length;g++){var h=a("<li></li>").html(b.thisMessage[g]).css({"list-style-image":"none","list-style-position":"outside",
"list-style-type":"none"});a(c).append(h)}a(e).append(c)}a(d).append(e);"top"==b.position?a(this).prepend(d):a(this).append(d);a(d).fadeIn();b.autoClose&&("undefined"!=typeof f&&clearTimeout(f),f=setTimeout(function(){a(d).fadeOut()},b.delayTime))})};a.fn.showMessage.closeMessage=function(){a(".showMessage",c.parent.document).length&&(clearTimeout(f),a(".showMessage",c.parent.document).fadeOut())}})(jQuery,window,document);