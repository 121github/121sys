<div id='calendar'></div>
<style>
#calendar .tooltip-inner { text-align:left !important; max-width:400px; }
#calendar .tt-month { color:#fff;background:#FF0000;border-radius:5px; padding:2px 4px }
#calendar .tt-week { color:#FF0000;  }
.fc-toolbar .bootstrap-select { text-align:left;width:auto !important; max-width:200px}
</style>
<script type="text/javascript">

var fullcalendar;	
var calendar = {	
   init:function(){
	 	$('#calendar').on('change','#attendee-select',function(){
		  $('#calendar').fullCalendar('removeEventSource', calendar.event_source)
		 calendar.event_source = helper.baseUrl+'booking/events?attendee='+$('#attendee-select').val()
		$('#calendar').fullCalendar('addEventSource', calendar.event_source)	
	 });
	if (helper.permissions['slot availability'] > 0) {
	this.left_buttons = 'prev,next rulesButton googleButton';	
	} else {
	this.left_buttons = 'prev,next googleButton';	
	}
	this.event_source = helper.baseUrl+'booking/events';  
	this.selected_attendee = "";   
    this.fullCalendar = $('#calendar').fullCalendar({
	timeFormat: 'H:mm',
	selectable:true,
	minTime:'07:00',
	maxTime:'20:00',
     header: {
        left: calendar.left_buttons,
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
	eventDrop: function(event){ calendar.set_event_time(event.id,event.start,event.end) },
	eventResize: function(event){ calendar.set_event_time(event.id,event.start,event.end) },
	  eventAfterRender:function( event, element, view ) { 
$(element).attr("data-id",event._id).attr('data-modal','view-appointment').find('.fc-content').prepend('<span class="'+event.icon+'"></span> ');
},
	  viewRender:function( event, element, view ) { 
calendar.loadAppointmentRules();
	  },
	  eventAfterAllRender:function( event, element, view ) {		  
	  },
	editable: true,
	 customButtons: {
        googleButton: {
            text: 'Google Calendar',
            click: function() {
                window.location.href=helper.baseUrl+'booking/google';
            }
        },
  		rulesButton: {
            text: 'Rules',
            click: function() {
                window.location.href=helper.baseUrl+'admin/availability';
            }
        }
    },
  events: calendar.event_source
    })
	calendar.attendee_filter();

       $('#calendar .fc-toolbar .fc-right').append(
           '<div class="form-group">'+
               '<div class="input-group date" id="datetimepicker2">'+
               '<input name="cal_date" type="hidden" class="form-control" />'+
               '<span class="input-group-addon" style="border: none; background: transparent;">'+
               '   <span class="fa fa-calendar"></span>'+
               '</span>'+
               '</div>'+
           '</div>'
       );
       $('#datetimepicker2').datetimepicker({
           defaultDate: moment(),
           format: 'YYYY-MM-DD',
           enabledHours:false
       }).on('dp.change', function(ev) {
           $('#calendar').fullCalendar('gotoDate',new Date($('input[name="cal_date"]').val()));
           //$('#calendar').fullCalendar('changeView','agendaDay');
       });
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
   attendee_filter:function(){
	   $('#calendar .fc-toolbar .fc-left').append('<div><select title="Filter" id="attendee-select"><option value=""></option></select></div>');
	   $('#attendee-select').selectpicker();
	   calendar.load_attendees();
   },
   load_attendees:function(){
	  $.ajax({
            url: helper.baseUrl + 'calendar/get_calendar_users',
            type: "POST",
            dataType: "JSON",
            data: {campaigns: $('#campaign-cal-select').val()}
        }).done(function (response) {
            var $options = "<option value=''>Show all</options>";
            $.each(response.data, function (k, v) {
                $options += "<option value='" + v.id + "'>" + v.name + "</options>";
            });
            $('#calendar').find('#attendee-select').html($options).selectpicker('refresh');
        });  
   },
    loadAppointmentRules: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_rules/by_user',
            type: "POST",
            dataType: "JSON",
            data: {users: $('#user-select').val()}
        }).done(function (response) {
            if (response.success){
				
                $.each(response.data, function (key, value) {
                    var title = '';
                    $.each(value, function (i, rule) {
                        var reason = "";
                        if (rule.reason.length > 0) {
                            var reason = ": " + rule.reason;
                        }
                        title += rule.name + ": " + rule.reason + "</br>";
                    });
					var daynumber = $('.fc-body').find('.fc-day-number[data-date="' + key + '"]').text();
					var tt = '<span class="pointer" data-toggle="tooltip" title="'+title+'">'+daynumber+'</span>';
					if($('#calendar').find('.fc-month-view').length>0){
                    $('#calendar').find('.fc-day-number[data-date="' + key + '"]').html($(tt).addClass('tt-month'));
					 }
					 if($('#calendar').find('.fc-agendaWeek-view').length>0||$('#calendar').find('.fc-agendaDay-view').length>0){
						  $('#calendar').find('.fc-day-header[data-date="' + key + '"]').append($(tt).addClass('marl glyphicon glyphicon-info-sign tt-week'));
					 }
					
                });				
			 setInterval(function(){ $('[data-toggle="tooltip"]').tooltip({ container: 'body',placement:'bottom',html:true }); }, 1000);
			}
        });
    }

}

$(document).ready(function() {
	calendar.init();
})
</script>