var simulation = "";

var campaign_functions = {
    init: function () {
        $('#top-campaign-select').hide();
        if (record.role == "16") {
            $('div.custom-panel,#custom-panel,#email-panel').hide();
        }
    },
    record_setup_update: function () {
        $('.progress-outcome').find('option[value=""]').text("-- Client Status --");
        $('.progress-outcome').selectpicker('refresh');
    },
    contact_form_setup: function () {
        $('input[name="dob"]').closest('.form-group').hide();
        $('input[name="website"]').closest('.form-group').hide();
        $('input[name="facebook"]').closest('.form-group').hide();
        $('input[name="linkedin"]').closest('.form-group').hide();

        $('.position-label').html("Company");
        $('input[name="position"]').attr('placeholder', 'Company Name')

        $('select[name="tps"]').closest('.form-group').hide();
    },
    contact_panel_setup: function () {
        $('.Job-panel-label').html("Company");
        $('.tps-btn').hide();
    },
    contact_tabs_setup: function () {
        $('.tps-contact-label').hide();
    },
    appointment_setup: function (start, attendee, urn) {
		console.log("LHS app setup");
        $.ajax({
            url: helper.baseUrl + 'appointments/get_unlinked_data_items',
            data: {urn: urn},
            dataType: "JSON",
            type: "POST"
        }).done(function (response) {
            $options = "";
            if (response.data.length > 0) {
                data_options = "";
                $.each(response.data, function (k, row) {
                    data_options += "<option value='" + row.data_id + "'>Job #" + row.data_id + ": Created on " + row.created_on + "</option>";
                });
                $data_items = $("<div class='form-group' style='display:none' ><p>Which job is this appointment related to?</p><select data-width='100%' id='data-items' name='data_id'>" + data_options + "</select></div>");

                $data_items.insertBefore($('#select-appointment-address'));
                //$('#data-items').selectpicker();
            }
        });


        $modal.find('.startpicker').data("DateTimePicker").destroy();
        $modal.find('.endpicker').data("DateTimePicker").destroy();
        $modal.find('.startpicker').datetimepicker({
            stepping: 30,
            format: 'DD/MM/YYYY HH:mm',
            sideBySide: true,
            enabledHours: [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
            daysOfWeekDisabled: [0, 6]
        });
        $modal.find('.endpicker').datetimepicker({
            stepping: 30,
            format: 'DD/MM/YYYY HH:mm',
            sideBySide: true,
            enabledHours: [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
            daysOfWeekDisabled: [0, 6]
        });

        if (start) {
            modals.set_appointment_start(start);
        }

             $modal.find('#typepicker').closest('.form-group').find('p').text('Appointment status');

        //Title no editable
        $modal.find('input[name="title"]').prop('readonly', true);

    },

    appointment_edit_setup: function () {
		console.log("LHS app edit setup");
        $modal.find('.attendees-selection p').html($modal.find('.attendees-selection p').html().replace('Choose the attendee(s) ', 'Choose the surveyor '));


        $modal.find('.startpicker').data("DateTimePicker").enabledHours([7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
        $modal.find('.startpicker').data("DateTimePicker").daysOfWeekDisabled([0, 6]);

        $modal.find('.endpicker').data("DateTimePicker").enabledHours([7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
        $modal.find('.endpicker').data("DateTimePicker").daysOfWeekDisabled([0, 6]);

        //When the type is changed
        modal_body.find('.typepicker').change(function () {
            var selectedId = $(this).val();

            //If we select Confirmed, confirmedButton -> on
            if (selectedId == 3) {
                $('#appointment-confirmed').bootstrapToggle('on');
                $modal.find('input[name="appointment_confirmed"]').val("1");
            }
            else {
                $('#appointment-confirmed').bootstrapToggle('off');
                $modal.find('input[name="appointment_confirmed"]').val("0");
            }
        });
        $modal.find('#typepicker').closest('.form-group').find('p').text('Appointment status');
        $modal.off('change', '.typepicker');
        $modal.on('change', '.typepicker', function () {
            //get_appointment_title();
        });

        //Title no editable
        $modal.find('input[name="title"]').prop('readonly', true);
        $modal.find('input[name="title"]')
            .tooltip('hide')
            .attr('data-original-title', $modal.find('input[name="title"]').val())
            .tooltip('fixTitle');
    },
    save_appointment: function (appointment) {
        //Get the additional info
        $.ajax({
            url: helper.baseUrl + 'ajax/load_custom_panel',
            type: "POST",
            dataType: "JSON",
            data: {id: "1", urn: appointment.urn}
        }).done(function (response) {
            var start_date = new Date(appointment.start.substr(0, 10));
            if (appointment.appointment_type_id == "3") {
                var express = false;
                if (typeof response.data[appointment.job_id]['Express report'] !== "undefined") {
                    if (response.data[appointment.job_id]['Express report']['value'] == "Yes") {
                        express = "Yes";
                    }
                }
                //If the 'Express Report' tick box is selected
                if (express === 'Yes') {
                    //Survey Delivery Date should be populated with a date that is 2 working days post the start date
                    start_date.setDate(start_date.getDate() + 2);
                }
                else {
                    //Survey Delivery Date should be populated with a date that is 5 working days post the start date
                    start_date.setDate(start_date.getDate() + 5);
                }
                var month = start_date.getMonth() + 1;
                var day = start_date.getDate();
                survey_date = (('' + day).length < 2 ? '0' : '') + day + '/' +
                    (('' + month).length < 2 ? '0' : '') + month + '/' +
                    start_date.getFullYear();
            }
            else {
                //Set the date to null if the appointment is not confirmed
                survey_date = null;
            }
            var update_status = false;
            if (response.data[appointment.job_id]['Job Status']['value'].length < 2 || response.data[appointment.job_id]['Job Status']['value'] == "Prospect" || response.data[appointment.job_id]['Job Status']['value'] == "Appointment Possible"
                || response.data[appointment.job_id]['Job Status']['value'] == "Appointment TBC"
                || response.data[appointment.job_id]['Job Status']['value'] == "Appointment Confirmed") {

                if (appointment.appointment_type_id == "1") {
                    update_status = "Appointment Possible";
                } else if (appointment.appointment_type_id == "2") {
                    update_status = "Appointment TBC";
                } else if (appointment.appointment_type_id == "3") {
                    update_status = "Appointment Confirmed";
                    //send the appointment confirmation email
                    //lhs.send_template_email(appointment.urn, 3, "Client", appointment.contact_email, "","","Appointment confirmation",appointmment.appointment_id);
                }
            }
            if (update_status) {
                $.ajax({
                    url: helper.baseUrl + 'records/update_custom_data_field',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        urn: appointment.urn,
                        data_id: appointment.job_id,
                        field_id: 6,
                        value: update_status
                    }
                })
            }
            //Save the appointment additional info.
            $.ajax({
                url: helper.baseUrl + 'records/update_custom_data_field',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: appointment.urn,
                    data_id: appointment.job_id,
                    field_id: 9,
                    value: survey_date
                }
            }).done(function (response) {
                if (typeof custom_panels != "undefined") {
                    custom_panels.load_all_panels()
                }
            });

			//create job number
			  if (appointment.appointment_type_id == "3") {
			 $.ajax({
                    url: helper.baseUrl + 'custom_scripts/lhs/lhs.php',
                    type: "POST",
                    dataType: "JSON",
                    data: {
						action:'create_job_number',
                        data_id: response.data[appointment.job_id],
                    }
                })
		 }
			

            //Add the attendee to the ownership record list
            $.ajax({
                url: helper.baseUrl + 'ajax/add_ownership',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: appointment.urn,
                    user_id: appointment.attendees[0]
                }
            });

            //Set the title
            var appointment_id = appointment.appointment_id;
            var appointment_type_id = appointment.appointment_type_id;
            var address = appointment.address;
            var access_address = appointment.access_address;
            var job_id = appointment.job_id;
            var job_status = response.data[appointment.job_id]['Job Status']['value'];
            var type_of_survey = response.data[appointment.job_id]['Type of survey']['value'];
            var additional_services = response.data[appointment.job_id]['Additional services']['value'];
            campaign_functions.set_appointment_title(appointment_id, appointment_type_id, address, access_address, job_id, job_status, type_of_survey, additional_services);
        });

    },
    load_custom_fields: function () {
    },
    edit_custom_fields: function () {
    },
    save_custom_panel: function ($form) {

        var appointment_id = false;
        var urn = $form.find("[name='urn']").val();
        if ($form.find('input[name="3"]').val() !== "") {
            appointment_id = $form.find('input[name="3"]').val();
        }
        //Get the Client email address on the record (contact on the appointment)
        if ($('[name="appointment_contact_email"]').length > 0) {
            var client_email = $('[name="appointment_contact_email"]').val();
        }
		 if ($form.find('input[name="1"]').val()==""&&($form.find("[name='6']").val() === "Paid"||$form.find("[name='6']").val() === "Invoiced"||$form.find("[name='6']").val() === "Paid & Issued"||$form.find("[name='6']").val() === "Invoiced & Report Ready")) {
			 $.ajax({
                    url: helper.baseUrl + 'custom_scripts/lhs/lhs.php',
                    type: "POST",
                    dataType: "JSON",
                    data: {
						action:'create_job_number',
                        data_id: $modal.find('[name="data_id"]').val(),
                    }
                })
		 }
		 //if they want to clear the job number if an status gets put back just uncomment this
		 /*
		 if ($form.find('input[name="1"]').val()!==""&&($form.find("[name='6']").val() !== "Paid"&&$form.find("[name='6']").val() !== "Invoiced"&&$form.find("[name='6']").val() !== "Paid & Issued"&&$form.find("[name='6']").val() !== "Invoiced & Report Ready")) {
			 $.ajax({
                    url: helper.baseUrl + 'upload/function_triggers/lhs.php',
                    type: "POST",
                    dataType: "JSON",
                    data: {
						action:'clear_job_number',
                        data_id: $modal.find('[name="data_id"]').val(),
                    }
                })
		 }
		*/
		
        //Job Status is Paid
        if ($form.find("[name='6']").val() === "Paid") {
            //Send email Referral Scheme Email to Account Role group email
            lhs.send_template_email(urn, 2, "Role Group Account", 'bradf@121customerinsight.co.uk', "", "", "Referral scheme email", appointment_id);

            //Send email Receipt of Payment Email to Client email address on the record
            lhs.send_template_email(urn, 6, "Client", 'bradf@121customerinsight.co.uk', "", "", "Receipt of payement email", appointment_id);
            //Hard Copy Required is Yes
            if ($form.find("[name='10']").val() === "Yes") {
                //Send email Hard Copy Email to the Account Role group email
                lhs.send_template_email(urn, 5, "Role Group Account", 'bradf@121customerinsight.co.uk', "", "", "Hard copy notification", appointment_id);

            }

        }
        //Job Status is Paid & Issued
        else if ($form.find("[name='6']").val() === "Paid & Issued") {
            //Send email Feedback Email to Client email address on the record
            lhs.send_template_email(urn, 8, "Client", 'bradf@121customerinsight.co.uk', "", "", "Feedback email", appointment_id);
        }
        //Job Status is Confirmed Appointment
        else if ($form.find("[name='6']").val() === "Appointment Confirmed") {
            //Send email Appointment Confirmation Email to Client email
            lhs.send_template_email(urn, 3, "Client", 'bradf@121customerinsight.co.uk', "", "", "Appointment confirmation", appointment_id);

        }
		if(appointment_id){
        //Get the appointment data
        $.ajax({
            url: helper.baseUrl + 'appointments/get_appointment',
            type: "POST",
            dataType: "JSON",
            data: {
                appointment_id: appointment_id
            }
        }).done( function (response){
            var appointment_type_id = response.data.appointment_type_id;
            var address = response.data.address;
            var access_address = response.data.access_address;
            var job_id = $form.find("[name='data_id']").val();
            var job_status = $form.find("[name='6']").val();
            var type_of_survey = $form.find("[name='7']").val();
            var additional_services = $form.find("[name='11']").val();
            campaign_functions.set_appointment_title(appointment_id, appointment_type_id, address, access_address, job_id, job_status, type_of_survey, additional_services);
        }).fail( function() {
            flashalert.danger("Error saving the appointment title")
        });
		}
        record.email_panel.load_panel();
    },
    custom_items_loaded: function () {
        $('.custom-panel').find('.id-title').closest('tr').hide();
        if (record.role == "16") {
            $('.edit-detail-btn').hide();
            $('#custom-panel').find('tr:contains(Quote)').hide();
            $('.custom-panel').find('tr:contains(Quote)').hide();
            $('.custom-panel').find('tr:contains(Invoice)').hide();
            $('div.custom-panel,#custom-panel').show();
        }
    },
    new_custom_item_setup: function () {
        //Set the job winner options
        campaign_functions.job_winner_setup();
    },
    edit_custom_item_setup: function (data) {
        //Set the job winner options
        campaign_functions.job_winner_setup(data);
    },
    job_winner_setup: function(data) {
        $.ajax({
            url: helper.baseUrl+'user/get_users',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                if (response.data.length > 0) {
                    var options = "<option value=''> --Please select-- </option>";
					var job_winner_val = "";
					if(typeof data[1][14] !== "undefined"){
                    job_winner_val= data[1][14];
					}
                    $.each(response.data, function (k, val) {
                        options += "<option value='" + val.name + "'>" + val.name + "</option>";
                    });
                    $modal.find('form').find("select[name='14']")
                        .html(options)
                        .selectpicker('val', job_winner_val)
                        .selectpicker('refresh');
                }
            }
            else {
                flashalert.danger(response.msg);
            }

        });
    },
    set_access_address: function () {
        if (typeof $('.accessaddresspicker option:selected').val() !== 'undefined') {
            if ($('.accessaddresspicker option:selected').val().length <= 0) {
                $.each($('#accessaddresspicker option'), function () {
                    if ($(this).attr('data-title') == "Access Detail Address") {
                        $('#access-add-check').bootstrapToggle('on');
                        $('#accessaddresspicker').selectpicker('val', $(this).val()).selectpicker('refresh');
                    }
                });
            }
        }
    },
    set_appointment_title: function(appointment_id, appointment_type_id, address, access_address, job_id, job_status, type_of_survey, additional_services) {
        var title = "";
        var type = "";
        var add_services = "";

        if (job_status != "Invoiced") {
            title += job_id;
        }

        switch (appointment_type_id) {
            case "1":
                title += " [poss]";
                break;
            case "2":
                title += " [tbc]";
                break;
            case "3":
                title += " [conf]";
                break;
        }

        switch (type_of_survey) {
            case "Building Survey":
                type = "BS";
                break;
            case "Home Buyer Report":
                type = "HBR";
                break;
            case "General Structural Inspection":
                type = "GSI";
                break;
            case "Specific Inspection":
                type = "SSI";
                break;
            case "Site Visit":
                type = "SV";
                break;
            case "Valuation":
                type = "VAL";
                break;
            case "Schedule Of Condition":
                type = "SOC";
                break;
            case "Structural Calculations":
                type = "SCALC";
                break;
            case "Party Wall":
                type = "PW";
                break;
        }

        switch (additional_services) {
            case "Valuation":
                add_services = "VAL";
                break;
            case "Express Write Up Service":
                add_services = "EXP";
                break;
            case "Platinum Plus":
                add_services = "PP";
                break;
            case "High Level Images":
                add_services = "HRP";
                break;
            case "Thermal Images":
                add_services = "TI";
                break;
        }

        if (access_address) {
            title += " KA "+type+" "+add_services+" "+address;
            title += " - KA "+access_address;
        }
        else {
            title += " STP "+type+" "+add_services+" "+address;
        }

        //Update appointment title
        $.ajax({
            url: helper.baseUrl+'custom_scripts/lhs/lhs.php',
            type: "POST",
            dataType: "JSON",
            data: {
                "action": "update_appointment_title",
                "appointment_id": appointment_id,
                "title": title
            }
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                if (typeof record != "undefined") {
                    record.appointment_panel.load_appointments();
                }
            }
            else {
                flashalert.danger(response.msg);
            }

        });
    }

}

var lhs = {
    send_template_email: function (urn, template_id, recipients_to_name, recipients_to, recipients_cc, recipients_bcc, email_name, appointment_id) {
        if (recipients_to != "") {
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