// JavaScript Document

// JavaScript Document

var simulation = "";

var campaign_functions = {
    init: function () {
		//this is ran when the html finished loading
    },
    record_setup_update: function() {
		//this is ran when the update panel finished loading
		$('#contact-panel').on('click','.startcall.btn',function(e){
			setTimeout(function(){
				$('#script-panel').find('.view-script').trigger('click');
			},1000);
		});
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
    set_access_address: function() {
    	 //this sets the access address in the appointment dropdown if one has been added to the appointment
    }

}