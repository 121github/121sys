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
        left: 'prev,next today localevents',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
	eventDrop: function(event){ console.log(event); calendar.set_event_time(event.id,event.start,event.end) },
	  eventAfterRender:function( event, element, view ) { 
$(element).attr("event-id",event._id).find('.fc-content').prepend('<span class="'+event.icon+'"></span> ');
},
	 customButtons: {
        localevents: {
            text: 'Local Calendar',
            click: function() {
                window.location.href=helper.baseUrl+'booking';
            }
        }
    },
  events: helper.baseUrl+'booking/google_events'
    })
   },

}

$(document).ready(function() {
	calendar.init();
})
</script>