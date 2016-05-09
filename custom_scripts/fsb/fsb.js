if(helper.user_id==259){
var hiddenDays = [ 1, 5, 6, 0 ];
var minTime = "08:00:00";
var maxTime = "19:00:00";
}
if(helper.user_id==258){
var hiddenDays = [ 1, 3, 5 ];
var minTime = "08:00:00";
var maxTime = "19:00:00";
}
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