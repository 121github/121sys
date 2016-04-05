<div id='calendar'></div>
<style>
.tooltip-inner { text-align:left !important; max-width:400px; }
#calendar .tt-month { color:#fff;background:#FF0000;border-radius:5px; padding:2px 4px }
#calendar .tt-week { color:#FF0000;  }
.fc-toolbar .bootstrap-select { text-align:left;width:auto !important; max-width:200px}
.context-menu-icon-updated { padding-left:8px !important }
</style>
<script type="text/javascript">

var fullcalendar;	
var calendar = {	
   init:function(){
	   $modal.on('click','[data-toggle="tab"]',function(e){
		   console.log($(this).attr('href'));
		   if($(this).attr('href')=="#apprules"){
			   $modal.find('#save-rule-btn').hide();
		   } else {
			   $modal.find('#save-rule-btn').show(); 
		   }
	   });
	   
	   
	     $modal.on('change', '.attendee-select', function () {
            calendar.get_slots_in_group($(this).val());
        });
	    $modal.on('click', '#save-rule-btn', function () {
            calendar.save_rules();
        });
		$modal.on('click', '.del-rule-btn', function () {
        calendar.delete_rule($(this).attr('item-id'), $(this).attr('item-date'));
    });
	   
	 	$('#calendar').on('change','#attendee-select',function(){
			calendar.load_rules();
		  $('#calendar').fullCalendar('removeEventSource', calendar.event_source)
		 calendar.event_source = helper.baseUrl+'booking/events?attendee='+$('#attendee-select').val()
		$('#calendar').fullCalendar('addEventSource', calendar.event_source)	
	 });
	this.left_buttons = 'prev,next ';
	if (helper.permissions['slot availability'] > 0) {
	this.left_buttons += ' rulesButton'; }
	if (helper.permissions['google calendar'] > 0) {
	this.left_buttons += ' googleButton';	
	}
	this.event_source = helper.baseUrl+'booking/events';    
    this.fullCalendar = $('#calendar').fullCalendar({
	timeFormat: 'H:mm',
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
calendar.load_rules();
	  },
	  eventAfterAllRender:function( event, element, view ) {
		  $('#calendar .fc-row td').addClass('context-menu-one');		  
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
				calendar.rule_modal();
                calendar.show_rules_in_day();
            }
        }
    },
  events: calendar.event_source
    })
	calendar.attendee_filter();
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
	   var elem = $('#calendar').find('#attendee-select');
	   elem.selectpicker();
	   calendar.load_attendees(elem);
   },
   load_attendees:function(elem,attendee){
	  $.ajax({
            url: helper.baseUrl + 'calendar/get_calendar_users',
            type: "POST",
            dataType: "JSON",
            data: {campaigns: $('#campaign-cal-select').val()}
        }).done(function (response) {
            var $options = "<option value=''>Show all</options>";
            $.each(response.data, function (k, v) {
                $options += "<option "+(v.id==attendee?"selected":"")+" value='" + v.id + "'>" + v.name + "</options>";
            });
            elem.html($options).selectpicker('refresh');
        });  
   },
    load_rules: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_rules/by_user',
            type: "POST",
            dataType: "JSON",
            data: {users: $('#attendee-select').val()}
        }).done(function (response) {
            if (response.success){
				calendar.clear_calendar_rules();
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
    },
	clear_calendar_rules:function(){
		$.each($('#calendar').find('.tt-month'), function(i,k){
			$(this).tooltip('destroy');
			$(this).removeClass('tt-month');
		});
		$.each($('#calendar').find('.tt-week'), function(i,k){
			$(this).tooltip('destroy');
			$(this).remove();
		});
		
	},
	rule_modal:function(date,create){
		var mheader = "Appointment Rules";
		            var mbody = '<ul class="nav nav-tabs" role="tablist">' +
            '<li role="presentation" '+(!create?'class="active"':'')+'><a href="#apprules" aria-controls="apprules" role="tab" data-toggle="tab">Rules</a></li>' +
            '<li role="presentation"  '+(create?'class="active"':'')+'><a href="#addrule" aria-controls="addrule" role="tab" data-toggle="tab">Add rule</a></li>' +
            '</ul>' +

            '<div class="tab-content">' +
            '<div role="tabpanel" class="tab-pane '+(!create?'active':'')+'" id="apprules">' +
            '<div class="rules-per-day"></div>' +
            '</div>' +
            '<div role="tabpanel" class="tab-pane '+(create?'active':'')+'" id="addrule">' +
            '<form class="appointment-rule-form form-horizontal">' +
            '<p><label>Create new rule<span class="block-day-error" style="color: red; display: none">Select the date(s) to block</span></label>' +
            '<div class="row">' +
            '<div class="col-md-6">' +
            '<label>Start Date</label>' +
            '<input name="date_from" value="" placeholder="Add the start block day..." class="form-control block-day"/>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<label>End Date</label>' +
            '<input name="date_to" value="" placeholder="Add the end block day..." class="form-control block-day-end"/>' +
            '</div>' +
            '</div>' +
            '</p>' +
            '<p>' +
            '<label>Attendee<span class="attendee-error" style="color: red; display: none"> Select a user</span></label>' +
            '<select name="user_id" class="attendee-select" title="Select attendee" data-width="100%" required>' +
            '</select>' +
            '</p>' +
            '<p><label>Time Slot(s)</label>' +
            '<select name="slot_id[]" data-actions-box="true" multiple disabled class="appointment-slot-select" id="appointment-slot-select" title="Select the slot to restrict" data-width="100%" required>' +
            '</select>' +
            '</p>' +
			 '<p class="max_apps"><label>Max Slots</label>' +
            '<input name="max_apps" placeholder="Max appointments in the selected slot(s)" value="" class="form-control numeric"/>' +
            '</p>' +
            '<p class="notes"><label>Reason</label>' +
            '<input name="notes" placeholder="Enter comments..." value="" class="form-control"/>' +
            '</p>' +
            '</form>' +
            '</div>' +
            '</div>';
		 var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="save-rule-btn" '+(!create?'style="display:none"':'')+'>Save</button> <button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
		modals.load_modal(mheader,mbody,mfooter);
        modal_body.css('overflow', 'visible');
        modal_body.css('padding', '0');
		var attendee= $('#calendar').find('#attendee-select').val();
		calendar.load_attendees($modal.find('.attendee-select'),attendee);
		if(attendee>0){
		calendar.get_slots_in_group(attendee);
		}
		$('#appointment-slot-select').selectpicker();
		$modal.find('.block-day').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.hide", function (e) {
            $modal.find('.block-day-end').data("DateTimePicker").minDate(e.date);
        });
        $modal.find('.block-day-end').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.hide", function (e) {
            $modal.find('.block-day').data("DateTimePicker").maxDate(e.date);
        });
        if (date) {
            $modal.find('input[name="date_from"]').val(timestamp_to_uk(new Date(date)));
            $modal.find('input[name="date_to"]').val(timestamp_to_uk(new Date(date)));
        }
	},
	show_rules_in_day: function (date) {
        $.ajax({
            url: helper.baseUrl + 'booking/get_appointment_rules_by_date',
            type: "POST",
            dataType: "JSON",
            data: {date: date, user_id: $('#attendee-select').val() }
        }).done(function (response) {
            if (response.data.length>0) {
                var rules = "", date_col = "";
                scroller_class = "";
                    date_col += '<th>Date</th>';
                rules += '<div id="scroller-div"><table class="table ajax-table small"><thead><tr><th>Date</th><th>Attendee</th><th>Slot</th><th>Availability</th><th>Reason</th><th>Remove</th></tr></thead><tbody>';
                $.each(response.data, function (key, value) {                   
                    rules +=
                        '<tr>' +
                          '<td>' + value.uk_date + '</td>' +
                        '<td>' + value.name + '</td>' +
                         '<td>' + value.slot_name + '</td>' +
						 '<td>' + value.max_slots + '</td>' +
						 '<td>' + value.notes + '</td>' +
                        '<td><button class="btn btn-default btn-xs del-rule-btn pointer" item-id="' + value.slot_override_id + '" item-date="' + value.date + '">Remove</button></td>' +
                        '</tr>';
                });
                rules += '</tbody></table></div>';
                $modal.find('.rules-per-day').html(rules);
                $('#scroller-div').css('overflow', 'auto').css('max-height', '350px');
            }
            else {
                $modal.find('.rules-per-day').html("No calendar rules have been created yet.");
                $modal.find('.nav-tabs a[href="#addrule"]').tab('show');
            }
        });
    },
 get_slots_in_group: function (id) {
        var slot_select = $('#appointment-slot-select');
        slot_select.prop('disabled', true).selectpicker('refresh');
        $.ajax({
            url: helper.baseUrl + 'calendar/get_slots_for_attendee',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            }
        }).done(function (response) {
            slot_select.html('')
            if (response.length > 0) {
                $.each(response, function (i, row) {
                    slot_select.append('<option data-subtext="' + row.slot_description + '" value="' + row.appointment_slot_id + '">' + row.slot_name + '</option>');
                });
                slot_select.prop('disabled', false).selectpicker('refresh');
            } else {
                slot_select.append('<option value="">All day</option>');
                slot_select.prop('disabled', false).selectpicker('refresh').selectpicker('selectAll');
            }

        });
    },
	save_rules:function(){
		$.ajax({url: helper.baseUrl+'admin/save_date_slots',
			type:"POST",
			dataType:"JSON",
			data: $modal.find('form').serialize()
		}).done(function(response){
			if(response.success){
			calendar.load_rules();
			flashalert.success("Rule was saved");	
			$modal.modal('toggle');	
			} else {
			flashalert.danger(response.error);	
			}
		});
	},
	 delete_rule: function (id,date) {
        $.ajax({
            url: helper.baseUrl + 'admin/delete_date_slots',
            type: "POST",
            dataType: "JSON",
            data: {id: id}
        }).done(function (response) {
            if (response.success) {
                flashalert.success("Rule was deleted");
				calendar.show_rules_in_day(date);
				calendar.load_rules();
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },
}

$(document).ready(function() {
	calendar.init();
	$.contextMenu({
            selector: '.context-menu-one', 
            callback: function(key, options) {
				var elem = options.$trigger;
				var cell = elem.closest('td');
				var cellIndex = cell[0].cellIndex
				if(elem.closest('div').hasClass('fc-content-skeleton')){
					var date = elem.closest('.fc-row').find('.fc-bg td:eq('+cellIndex+')').attr('data-date');	
				} else {
					var date = elem.attr('data-date');	
				}
			   if(key=="view"){
				$('#calendar').fullCalendar( 'changeView', 'agendaDay' );
				$('#calendar').fullCalendar( 'gotoDate', moment(date,'YYYY-MM-DD'));
			   }
			   if(key=="create"){
				   modals.search_records("create-appointment",date);
			   }
			   if(key=="rule"){
				calendar.rule_modal(date,'create');
				calendar.show_rules_in_day(date);
			   }
            },
            items: {
            "view": {name: "View Day", icon: function(opt, $itemElement, itemKey, item){
            $itemElement.html('<span class="fa fa-calendar"></span> View Day');
            return 'context-menu-icon-updated';
        	}
				},
			"create": {name: "Create Event", icon: function(opt, $itemElement, itemKey, item){
            $itemElement.html('<span class="fa fa-plus"></span> Create Event');
            return 'context-menu-icon-updated';
        	}
				},
           	"rule": {name: "Create Rule", icon: function(opt, $itemElement, itemKey, item){
            $itemElement.html('<span class="fa fa-edit"></span> Create Rule');
            return 'context-menu-icon-updated';
        	}
				},
               /* "sep1": "---------",
                "quit": {name: "Cancel", icon: function(){
                    return 'context-menu-icon context-menu-icon-quit';
                }} */
            } 
        });	
	
})
</script>