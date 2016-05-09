var newOptions = [];

$(document).on('change','#calendar #attendee-select',function(){
	console.log($(this).val());
	if($(this).val()==259){
	newOptions.hiddenDays = [ 0,7 ];

 newOptions.minTime = "10:00:00";
 newOptions.maxTime = "19:00:00";
 console.log(minTime);
	}
	if($(this).val()==258){		
 newOptions.hiddenDays = [  0,7 ];
 newOptions.minTime = "08:00:00";
 newOptions.maxTime = "19:00:00";
	}
	$('#calendar').fullCalendar('destroy');
	$('#calendar').fullCalendar($.extend(calendar.calendarOptions,newOptions));
	calendar.build_options()
});


var simulation = "";

var campaign_functions = {
    init: function () {
		//this is ran when the html finished loading
    },
    record_setup_update: function() {
		//this is ran when the update panel finished loading
    },
    appointment_setup: function (start) {
		//this is ran when the "add appointment" model pops up
    },

    set_appointment_confirmation: function() {
		//this is ran when the "confirm appointment" toggle is changed
    },
    save_appointment: function(appointment) {
		//this is ran when an appointment is saved
    },
    load_custom_fields: function() {
		//this is ran when the custom fields panel is loaded
    },
    edit_custom_fields: function() {
		//this is ran when the custom fields panel is edited
    },
    save_custom_fields: function(data) {
		//this is ran when a custom fields panel is saved
    },
	custom_items_loaded:function(){

	},
	new_custom_item_setup:function(){

	},
    set_access_address: function() {
    	 //this sets the access address in the appointment dropdown if one has been added to the appointment
    }

}