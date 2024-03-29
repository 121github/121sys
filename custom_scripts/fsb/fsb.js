var newOptions = [];
$(document).on('change','#calendar #attendee-select',function(){
temp_attendee = $(this).val();
	if($(this).val()==259){
	newOptions.hiddenDays = [ 0,7 ];

 newOptions.minTime = "10:00:00";
 newOptions.maxTime = "19:00:00";
	}
	if($(this).val()==258){		
 newOptions.hiddenDays = [  0,7 ];
 newOptions.minTime = "08:00:00";
 newOptions.maxTime = "19:00:00";
	}
	
	$('#calendar').fullCalendar('destroy');
	$('#calendar').fullCalendar($.extend(calendar.calendarOptions,newOptions));
	calendar.build_options($(this).val())
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
		//contact_confirmation_email(appointment.appointment_id);
		client_confirmation_email(appointment);
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


function contact_confirmation_email(appointment){
	//this is sent manually by the agents via webmail
	//var recipient = $('#contact-email-address').text();
//custom_email.send_template_email(record.urn, 92, false, recipient, "", "bradf@121customerinsight.co.uk", "Contact appointment Confirmation email",appointment);
}

function client_confirmation_email(appointment){
	var recipient = 'attendee';
custom_email.send_template_email(appointment.urn, 89, false, recipient, "", "rachaeln@121customerinsight.co.uk", "FSB appointment notification email",appointment.appointment_id);
}

function client_info_email(){
//this is sent manually by the agents via webmail
//var recipient = $('#contact-email-address').text();
//custom_email.send_template_email(appointment.urn, 91, false, recipient, "", "", "FSB info email",false);
}

var custom_email = {
    send_template_email: function (urn, template_id, recipients_to_name, recipients_to, recipients_cc, recipients_bcc, email_name, appointment_id) {
        if (validate_email(recipients_to)||'attendee') {
            $.ajax({
                url: helper.baseUrl + 'email/send_template_email',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn,
                    template_id: template_id,
                    recipients_to_name: recipients_to_name,
                    recipients_to: recipients_to,
                    recipients_cc: recipients_cc,
                    recipients_bcc: recipients_bcc,
                    email_name: email_name,
                    appointment_id: appointment_id
                }
            }).done(function (response) {
                if (response.success === true) {
                    flashalert.success(response.msg);
					record.email_panel.load_panel()
                }
                else if (response.success == false) {
                    flashalert.danger(response.msg);
                }
            }).fail(function () {
                flashalert.danger(msg);
            });
        }
        else {
            flashalert.danger("Not a valid email:"+recipients_to);
        }
    }
}