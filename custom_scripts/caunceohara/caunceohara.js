// JavaScript Document

// JavaScript Document

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

function client_info_email(){
	var recipient = $('#contact-email-address').text();
custom_email.send_template_email(record.urn, 90, false, recipient, "", "", "Quote link email",false);
}

var custom_email = {
    send_template_email: function (urn, template_id, recipients_to_name, recipients_to, recipients_cc, recipients_bcc, email_name, appointment_id) {
         if (validate_email(recipients_to)) {
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
				record.email_panel.load_panel();
                if (response.success === true) {
                    flashalert.success(response.msg);
                }
                else if (response.success == false) {
                    flashalert.danger(response.msg);
                }
            }).fail(function () {
                flashalert.danger(msg);
            });
        }
        else {
            flashalert.danger("ERROR: No email address on: " + msg);
        }
    }
}