
 //this function initializes all the javascript widgets - tooltips, datepickers etc when the page loads. It can be ran after an ajax request to apply widgets to new page elements - ensure you are using the correct class names for each element type!
function renew_js(){
$('.tt').tooltip();	
$('.datetime').datetimepicker({
            format: 'DD/MM/YYYY HH:mm'
  });
$('.date').datetimepicker({
            format: 'DD/MM/YYYY',
			pickTime: false,
        });
$('.date2').datetimepicker({
    format: 'YYYY-MM-DD',
	pickTime: false,
	maxDate: moment(),
});
$('.dob').datetimepicker({
			pickTime: false,
            viewMode:'years',
            format: 'DD/MM/YYYY'
});
			$(document).on('keypress','.date,.datetime,.dob',function(e){
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
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

Date.prototype.addHours = function(h) {    
   this.setTime(this.getTime() + (h*60*60*1000)); 
   return this;   
}



var helper = {};

/* AJAX GLOBAL EVENT - This happens after ajax request. We check if the response is timeout then it redirects the user to the login page */
$( document ).ajaxComplete(function(event, xhr, settings) {
  if (xhr.responseText === 'Logout') {
    window.location=helper.baseUrl+'user/login'; //if the user is not logged in, simply refresh the page which will then redirect them to the login page
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
    },

}

 function  timestamp_to_uk(timestamp,time) {
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
             + ('0' + (timestamp.getMonth()+1)).slice(-2) + '/'
             + timestamp.getFullYear();
			 
		if(time){
		ukDateString +=  ' ' + hours + ':' + minutes
	}
	
			 
    return ukDateString;

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

