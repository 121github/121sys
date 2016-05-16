platform = navigator.platform,
            mapLink = 'http://maps.google.com/';
        if (platform === 'iPad' || platform === 'iPhone' || platform === 'iPod') {
            mapLink = 'comgooglemaps://';
        }
		if(typeof quick_planner == "undefined"){
var quick_planner = [];
		}
if(typeof calendar == "undefined"){
var calendar = [];
		}
function validate_postcode(postcode,callback){
	var valid;
$.ajax({
            url: helper.baseUrl + 'ajax/validate_postcode',
            data: {
                postcode: postcode
            },
            dataType: 'JSON',
            type: 'POST'
        }).done(function(response){
			if(response.success){
			callback(response.postcode);	
			} else {
			callback(false);
			}
		});
}

function validate_email(email) 
{
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
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
	if(device_type=="default"||device_type=="tablet2"||device_type=="tablet"){
		$('#top-campaign-container').css('display','inline-block');
		$('#side-campaign-select').hide();
	} else {
		$('#top-campaign-container').hide();
		$('#side-campaign-select').show();
	}
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
        format: 'DD/MM/YYYY',
		enabledHours:false
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

var menu_api = false;
var messages = (localStorage.getItem("messages")?JSON.parse(localStorage.getItem("messages")):[]);

/* AJAX GLOBAL EVENT - This happens after ajax request. We check if the response is timeout then it redirects the user to the login page */
$(document).ajaxError(function (event, xhr, settings) {
    if (xhr.status != 200) {
        var date = new Date();
        var msg = [false, 'Access Error', settings.url+' ['+xhr.status+" - "+xhr.statusText+']', date];
        messages.unshift(msg);
        localStorage.setItem("messages", JSON.stringify(messages));
    }
});
$(document).ajaxComplete(function (event, xhr, settings) {
    if (typeof (xhr.responseJSON) != "undefined" && typeof (xhr.responseJSON.msg) != "undefined") {
        var date = new Date();
        var title = (typeof xhr.responseJSON.msg_title != "undefined"?xhr.responseJSON.msg_title:"-");
        var info = (typeof xhr.responseJSON.msg != "undefined"?xhr.responseJSON.msg:"-");
        var msg = [xhr.responseJSON.success, title, info, date];
        messages.unshift(msg);
        localStorage.setItem("messages", JSON.stringify(messages));
    }

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
        $('#page-success .alert-text').html(text);
        $('#page-success').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
    },
    info: function (text) {
        $('#page-info .alert-text').html(text);
        $('#page-info').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
    },
    danger: function (text) {
        $('#page-danger .alert-text').html(text);
        $('#page-danger').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
    },
    warning: function (text) {
        $('#page-warning .alert-text').html(text);
        $('#page-warning').removeClass('hidden').fadeIn(1000).delay(2000).fadeOut(1000);
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

function deserializeForm(dataForm) {
    var objs = [], temp;
    var temps = dataForm.split('&');

    for(var i = 0; i < temps.length; i++){
        temp = temps[i].split('=');
        //objs.push(temp[0]);
        var key = decodeURIComponent(temp[0]);
        var value = (temp.length > 1) ? decodeURIComponent(temp[1]).replaceAll("+"," ") : undefined;
        objs[key] = value;
    }

    return objs;
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
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
    if (barheight > 53) {
        $('.navbar-brand').hide();
    }

	$(document).on("click","#startsearch",function(e){
		e.preventDefault();
		$.ajax({ url:helper.baseUrl+'search/count_records',
		type:"POST",
		dataType:"JSON",
		data:$('#quicksearchform').serialize()	
	}).done(function(response){
		if(response.success){
			if(response.data>0){
		$('#quicksearchresult').addClass('text-success').html('<a href="#" id="showquicksearchresults">'+response.data+' record(s) found</a>');
			} else {
		$('#quicksearchresult').addClass('text-warning').text('0 record(s) found');		
			}
		} else {
		$('#quicksearchresult').addClass('text-danger').text(response.msg);
		}
	});
});

$('#global-filter-form').on("click",".apply-filter",function(e){
	e.preventDefault();
	var postcode =  $('#global-filter-form').find('input[name="postcode"]').val(); 
	var distance = $('#global-filter-form').find('[name="distance"]').val(); 
	if(distance!==""&&postcode==""){
	flashalert.danger("Distance filter requires a postcode");
	return false;	
	}
	if(postcode!==""){
	var valid_postcode = validate_postcode(postcode,postcode_filter_callback);
	return false;
	}
	apply_global_filter();
});

	$('#quicksearchform').on("click","#showquicksearchresults",function(e){
	apply_quick_search();
	});



$('#global-filter-form').on('click', '.clear-filter', function(e) {
		e.preventDefault();
            modals.clear_filters();
        });

function postcode_filter_callback(valid){
 if(valid){
	 $('#submenu-filters input[name="postcode"]').val(valid);
	 $('#global-filter-form').find('input[name="postcode"]').val(valid); 	
     apply_filter();
 } else {
	 $('#submenu-filters input[name="postcode"]').val('');	
	 $('#global-filter-form').find('input[name="postcode"]').val(''); 
	 flashalert.danger("Postcode is not valid"); 
}
}

function apply_global_filter(){
 $.ajax({
            url: helper.baseUrl + 'search/apply_filter',
            type: "POST",
            dataType: "JSON",
            data:  $('#global-filter-form').serialize()
        }).done(function(response) {
			if(response.filter){
				$('#submenu-filter-btn').removeClass('btn-default').addClass('btn-success');
				$('nav#global-filter .clear-filter').prop('disabled',false);
			} else {
				$('#submenu-filter-btn').removeClass('btn-success').addClass('btn-default');
					$('nav#global-filter .clear-filter').prop('disabled',true);
			}
			if(typeof view !== "undefined"){
			view.reload_table();
			} else {
			location.href = helper.baseUrl+'records/detail/0';	
			}
        });	
}


function apply_quick_search(){
 $.ajax({
            url: helper.baseUrl + 'search/apply_filter',
            type: "POST",
            dataType: "JSON",
            data:  $('#quicksearchform').serialize()
        }).done(function(response) {
			if(typeof view !== "undefined"){
			view.reload_table();
			} else {
			window.location.href = helper.baseUrl+'records/view';
			}
        });	
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


/* ==========================================================================
 FILTERS
 ========================================================================== */
var filters = {
    init: function() {
		$('.dropdown-menu ul').addClass('mm-nolistview');
        $('nav#filter-right').mmenu({
            navbar: {
                title: "Filters <span class='text-primary current-campaign'></span>"
            },
            extensions: ["pageshadow", "effect-menu-slide", "effect-listitems-slide", "pagedim-black"],
            offCanvas: {
                position: "right",
                zposition: "front"
            }
        });

        $('nav#filter-view').mmenu({
            navbar: {
                title: "Current Filters <span class='text-primary current-campaign'></span>"
            },
            extensions: ["pageshadow", "effect-menu-slide", "effect-listitems-slide", "pagedim-black"],
            offCanvas: {
                position: "right",
                zposition: "front"
            }
        });

        $(document).on("click", ".clear-filters", function (e) {
            e.preventDefault();
            location.reload();
        });

        $(document).on("click", '.plots-tab', function(e) {
            e.preventDefault();
            $('.graph-color').show();
        });

        $(document).on("click", '.filters-tab,.searches-tab', function(e) {
            e.preventDefault();
            $('.graph-color').hide();
        });

        $(document).on("click", ".show-charts", function (e) {
            e.preventDefault();
            var charts = (typeof $(this).attr('charts') != "undefined"?$(this).attr('charts').split(","):null);
            var data = (typeof $(this).attr('data') != "undefined"?$(this).attr('data').split(","):null);
            if ($(this).attr('data-item') == 0 && charts) {
                $.each(charts, function (i, val) {
                    $('.nav-tabs a[href="#'+val+'"]').tab('show');
                });
                $('.graph-color').show();
                $('.show-charts').removeClass('btn-default').addClass('btn-success');
                $(this).attr('data-item',1);
            }
            else if ($(this).attr('data-item') == 1 && data) {
                $.each(data, function (i, val) {
                    $('.nav-tabs a[href="#'+val+'"]').tab('show');
                });
                $('.graph-color').hide();
                $('.show-charts').removeClass('btn-success').addClass('btn-default');
                $(this).attr('data-item',0);
            }
        });

        $(document).on("click", '.view-filters', function (e) {
            e.preventDefault();
            var data = (typeof $('.show-charts').attr('data') != "undefined"?$('.show-charts').attr('data').split(","):null);
            $.each(data, function (i, val) {
                $('.nav-tabs a[href="#'+val+'"]').tab('show');
                if ($('.show-charts').attr('data-item') == 1) {
                    $('.show-charts').removeClass('btn-success').addClass('btn-default');
                    $('.show-charts').attr('data-item',0);
                }
                $('.graph-color').hide();
            });
        });
    }
}