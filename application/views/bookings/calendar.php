<div id='calendar'></div>
<script type="text/javascript">

	
var calendar = {	
   init:function(){
    this.cal = $('#calendar').fullCalendar({
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
$(element).attr("event-id",event._id).find('.fc-content').prepend('<span class="'+event.icon+'"></span> ');
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
   }

}

$(document).ready(function() {
	calendar.init();
})
</script>