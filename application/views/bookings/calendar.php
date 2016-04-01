<div id='calendar'></div>
<style>
.tooltip-inner { text-align:left !important; z-index:999999 }
</style>
<script type="text/javascript">

var fullcalendar;	
var calendar = {	
   init:function(){
    fullcalendar = $('#calendar').fullCalendar({
	timeFormat: 'H:mm',
	selectable:true,
	minTime:'07:00',
	maxTime:'20:00',
     header: {
        left: 'prev,next today googleButton',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
	eventDrop: function(event){ console.log(event); calendar.set_event_time(event.id,event.start,event.end) },
	  eventAfterRender:function( event, element, view ) { 
$(element).attr("data-id",event._id).attr('data-modal','edit-appointment').find('.fc-content').prepend('<span class="'+event.icon+'"></span> ');
},
	  eventAfterAllRender:function( event, element, view ) { 
calendar.loadAppointmentRules();
	  },
	editable: true,
	 customButtons: {
        googleButton: {
            text: 'Google Calendar',
            click: function() {
                window.location.href=helper.baseUrl+'booking/google';
            }
        }
    },
  events: helper.baseUrl+'booking/events'
    })
   },
   set_event_time:function(id,start,end){
	   $.ajax({ url: helper.baseUrl+'booking/set_event_time',
	   type:"POST",
	   dataType:"JSON",
	   data: { id:id,start:start.format("YYYY-MM-DD HH:mm"),end:end.format("YYYY-MM-DD HH:mm") }
	   }).done(function(){
		   flashalert.success("Appointment was updated");
	   })
   },
    loadAppointmentRules: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_rules/by_user',
            type: "POST",
            dataType: "JSON",
            data: {users: $('#user-select').val()}
        }).done(function (response) {
            if (response.success) {
                $.each(response.data, function (key, value) {
                    var title = '<ul';
                    $.each(value, function (i, rule) {
                        var reason = "";
                        if (rule.reason.length > 0) {
                            var reason = ": " + rule.reason;
                        }
                        title += "<li>" + rule.name + ":" + rule.reason + "</li>";
                    });
                    title += '</ul>';
					var daynumber = $('.fc-body').find('.fc-day-number[data-date="' + key + '"]').text();
					var tt = '<span class="pointer" style="color:#fff;background:#C51014;border-radius:5px; padding:2px 4px" data-toggle="tooltip" title="'+title+'" data-html="true">'+daynumber+'</span>';
                    $('.fc-body').find('.fc-day-number[data-date="' + key + '"]').html(tt);
                });
				 setInterval(function(){ $('[data-toggle="tooltip"]').tooltip(); }, 1000);
				
            }
        });
    }

}

$(document).ready(function() {
	calendar.init();
})
</script>