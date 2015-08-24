platform = navigator.platform,
            mapLink = 'http://maps.google.com/';
        if (platform === 'iPad' || platform === 'iPhone' || platform === 'iPod') {
            mapLink = 'comgooglemaps://';
        }

var device_type;
$(window).ready(function() {
    setDevice($(window).width());
});

$(window).resize(function() {
    setDevice($(window).width());
});

function debounce(func, wait, immediate) {
	var timeout;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) func.apply(context, args);
	};
};

function CleanPastedHTML(input) {
  // 1. remove line breaks / Mso classes
  var stringStripper = /(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/g;
  var output = input.replace(stringStripper, ' ');
  // 2. strip Word generated HTML comments
  var commentSripper = new RegExp('<!--(.*?)-->','g');
  var output = output.replace(commentSripper, '');
  var tagStripper = new RegExp('<(/)*(meta|link|span|\\?xml:|st1:|o:|font)(.*?)>','gi');
  // 3. remove tags leave content if any
  output = output.replace(tagStripper, '');
  // 4. Remove everything in between and including tags '<style(.)style(.)>'
  var badTags = ['style', 'script','applet','embed','noframes','noscript'];

  for (var i=0; i< badTags.length; i++) {
    tagStripper = new RegExp('<'+badTags[i]+'.*?'+badTags[i]+'(.*?)>', 'gi');
    output = output.replace(tagStripper, '');
  }
  // 5. remove attributes ' style="..."'
  var badAttributes = ['style', 'start'];
  for (var i=0; i< badAttributes.length; i++) {
    var attributeStripper = new RegExp(' ' + badAttributes[i] + '="(.*?)"','gi');
    output = output.replace(attributeStripper, '');
  }
  return output;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
} 

function setDevice(width) {

    if (width <= 480){
        device_type = "mobile";
    }
    else if (width <= 767){
        device_type = "mobile2";
    }
    else if (width <= 980){
        device_type = "tablet";
    }
    else if (width <= 1200){
        device_type = "tablet2";
    }
    else {
        device_type = "default";
    }
	modals.set_size();
}


function addLeadingZero(num) {
    if (num < 10) {
        return "0" + num;
    } else {
        return "" + num;
    }
}

//this function stretches any panel in a strech element to the remaining height of the row
function stretch() {
    $.each($('.stretch-panel'), function () {
        rowheight = $(this).closest('.row').height();
        colheight = $(this).closest('[class^="col"]').height();
        if (rowheight - colheight < 100) {
            diff = rowheight - colheight;
        } else {
            diff = 100;
        }

        panheight = $(this).find('.panel').height();
        if ($(this).find('.panel').height() < diff + panheight) {
            $(this).find('.panel').css("min-height", diff + panheight + "px");
        }
        //remove the stretch class once complete so it doesnt do it again
        $(this).removeClass('stretch-panel');
    });
    $.each($('.stretch-element'), function () {
        rowheight = $(this).closest('.row').height();
        colheight = $(this).closest('[class^="col"]').height();
        diff = rowheight - colheight
        eleheight = $(this).height();
        panheight = $(this).closest('.panel').height();
        if ($(this).height() < diff + eleheight) {
            $(this).css("min-height", diff + eleheight + "px");
        }
        //remove the stretch class once complete so it doesnt do it again
        $(this).removeClass('stretch-element');
    });
}

//this function initializes all the javascript widgets - tooltips, datepickers etc when the page loads. It can be ran after an ajax request to apply widgets to new page elements - ensure you are using the correct class names for each element type!
function renew_js() {
    $('.tt').tooltip();
    $('.datetime').datetimepicker({
        format: 'DD/MM/YYYY HH:mm',
		sideBySide:true,
		
    });
    $('.date').datetimepicker({
        format: 'DD/MM/YYYY'
    });
    $('.date2').datetimepicker({
        format: 'YYYY-MM-DD',
        maxDate: moment(),
		enabledHours:false
    });
    $('.dob').datetimepicker({
        viewMode: 'years',
        format: 'DD/MM/YYYY',
		enabledHours:false
    });
    $(document).on('keypress', '.date,.datetime,.dob', function (e) {
        e.preventDefault()
    });
    $('.selectpicker').selectpicker();
    //$('.collapse').collapse();
    $('.tab').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
    $(document).on('click', '.clear-input', function (e) {
        $(this).closest('.input-group').find('input').val('');
    });
}
renew_js();

function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

Date.prototype.addHours = function (h) {
    this.setTime(this.getTime() + (h * 60 * 60 * 1000));
    return this;
}


var helper = {};

/* AJAX GLOBAL EVENT - This happens after ajax request. We check if the response is timeout then it redirects the user to the login page */
$(document).ajaxComplete(function (event, xhr, settings) {
    if (xhr.responseText === 'Logout') {
        window.location = helper.baseUrl + 'user/login'; //if the user is not logged in, simply refresh the page which will then redirect them to the login page
    }
    if (xhr.responseText === 'Denied') {
        flashalert.danger("You do not have permission to do this"); //if the user is does not have access to perform an ajax request flash a permission denied alert
    }
});

/* ==========================================================================
 ALERTS
 ========================================================================== */
var flashalert = {

    success: function (text) {
        $('.page-success .alert-text').html(text);
        $('.page-success').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
    },
    info: function (text) {
        $('.page-info .alert-text').html(text);
        $('.page-info').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
    },
    danger: function (text) {
        $('.page-danger .alert-text').html(text);
        $('.page-danger').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
    },
    warning: function (text) {
        $('.page-warning .alert-text').html(text);
        $('.page-warning').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
    }

}

function timestamp_to_uk(timestamp, time) {
    // multiplied by 1000 so that the argument is in milliseconds, not seconds
    var ukDateString;
    var d = new Date(timestamp);
    var year = d.getFullYear();
    var month = d.getMonth() + 1;
    var day = d.getDate();

    var hours = d.getHours();
    var minutes = d.getMinutes();
    var seconds = d.getSeconds();

// will display time in 10:30:23 format
    // var formattedTime = day + '/' + month + '/' + year;

    ukDateString = ('0' + timestamp.getDate()).slice(-2) + '/'
    + ('0' + (timestamp.getMonth() + 1)).slice(-2) + '/'
    + timestamp.getFullYear();

    if (time) {
        ukDateString += ' ' + hours + ':' + minutes
    }


    return ukDateString;

}

function toHHMMSS(sec_num) {
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = hours+':'+minutes+':'+seconds;

    return time;
}

function get_elapsed_time_string(total_seconds) {
    function pretty_time_string(num) {
        return ( num < 10 ? "0" : "" ) + num;
    }

    var hours = Math.floor(total_seconds / 3600);
    total_seconds = total_seconds % 3600;

    var minutes = Math.floor(total_seconds / 60);
    total_seconds = total_seconds % 60;

    var seconds = Math.floor(total_seconds);

    // Pad the minutes and seconds with leading zeros, if required
    hours = pretty_time_string(hours);
    minutes = pretty_time_string(minutes);
    seconds = pretty_time_string(seconds);

    // Compose the string for display
    var currentTimeString = hours + ":" + minutes + ":" + seconds;

    return currentTimeString;
}

/* ==========================================================================
 MENU
 ========================================================================== */
$(document).ready(function () {
    $(".dropdown-menu > li > a.trigger").on("click", function (e) {
        var current = $(this).next();
        var grandparent = $(this).parent().parent();
        if ($(this).hasClass('left-caret') || $(this).hasClass('right-caret'))
            $(this).toggleClass('right-caret left-caret');
        grandparent.find('.left-caret').not(this).toggleClass('right-caret left-caret');
        grandparent.find(".sub-menu:visible").not(current).hide();
        grandparent.find(".sub-menu-left:visible").not(current).hide();
        current.toggle();
        e.stopPropagation();
    });
    $(".dropdown-menu > li > a:not(.trigger)").on("click", function () {
        var root = $(this).closest('.dropdown');
        root.find('.left-caret').toggleClass('right-caret left-caret');
        root.find('.sub-menu:visible').hide();
        root.find('.sub-menu-left:visible').hide();
    });

    var barheight = $('.navbar').height();
    if (barheight > 50) {
        $('.navbar-brand').hide();
    }

});

/* ==========================================================================
 BROWSER
 ========================================================================== */
var browser = {
	
    init: function () {
        if ($.browser.msie) {			
  modal_header.text('Old browser detected');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
		modal_body.append("<p>IE version (" + $.browser.versionNumber + ") does not support many of the features within this system.</p><p>The browsers below are guaranteed to work with this system, we <b>strongly</b> recommend you switch.</p><p><a href='https://www.google.com/chrome/index.html' target='blank'><img src='"+helper.baseUrl+"assets/img/chrome-logo.jpg'/></a> <a href='https://www.mozilla.org/en-GB/firefox/new/' target='blank'><img src='"+helper.baseUrl+"assets/img/firefox-logo.jpg' /></a></p>");
        $(".confirm-modal").off('click').text('I don\'t care').show();
		$('.confirm-modal').on('click', function (e) {
            $('#modal').modal('toggle');
        });
        }
    }
}

