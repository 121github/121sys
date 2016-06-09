var simulation = "";

var campaign_functions = {
    init: function () {
		$('ul.nav .source-name').css('color','yellow');
        $('#top-campaign-select').hide();
		$('#sticky-panel .panel-heading').text('Job Notes');
		$('#sticky-notes').attr('placeholder','Enter any additional information about the job here. These notes will be added to the surveyors appointment and sent to their calendar');
    },
	add_google_event:function(appointment_id){
		 var description = '';
                $.ajax({
                    url:helper.baseUrl+'custom_scripts/lhs/lhs.php?calendar_description',
                    data: {
                        id: appointment_id
                    },
                    type: "POST",
                    dataType: "JSON"
                }).done(function(response){
                    if (response.success) {
                       description= response.description;
                    }
                   modals.add_google_event(appointment_id,description);
                });
	},
    record_setup_update: function () {
        $('.progress-outcome').find('option[value=""]').text("-- Client Status --");
        $('.progress-outcome').selectpicker('refresh');
		
    },
	add_record_setup:function(){
		$('#create-record .panel').prepend('<div class="form-group"><label>Address Type</label><br><select class="selectpicker" name="description"><option value="Survey Address">Survey Address</option><option value="Correspondence Address">Correspondence Address</option><option disabled data-subtext="Must add this later" value="Access Address">Access Address</option></select></div>');
	},
    contact_form_setup: function () {

        $modal.find('input[name="dob"]').closest('.form-group').hide();
        $modal.find('input[name="website"]').closest('.form-group').hide();
        $modal.find('input[name="facebook"]').closest('.form-group').hide();
        $modal.find('input[name="linkedin"]').closest('.form-group').hide();

       $modal.find('.position-label').html("Company");
        $modal.find('input[name="position"]').attr('placeholder', 'Company Name')

        $modal.find('select[name="tps"]').closest('.form-group').hide();
		$modal.find('#address').empty();
		
		$.ajax({ url:helper.baseUrl+'custom_scripts/lhs/lhs.php?address_form',
		type:"POST",
		data: { "contact_id":$modal.find('[name="contact_id"]').val(),"urn": record.urn }, 
		dataType:"HTML",
		beforeSend:function(){
		$modal.find('#address').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' /></li>");	
		}
		}).done(function(response){ 	
		$modal.find('#address').html(response);
		$modal.find('.panel-collapse').collapse('hide');
		 $modal.find('[data-toggle="collapse"]').click(function(e){
      e.preventDefault();
      var target_element= $(this).attr("href");
      $modal.find(target_element).collapse('toggle');
	  $modal.find('.panel-collapse').not(target_element).collapse('hide');
      return false;
});
		});
		
		
    },
    contact_panel_setup: function () {
        $('.Job-panel-label').html("Company");
        $('.tps-btn').hide();
		$('#contact-panel .panel-heading .btn').remove();
    },
    contact_tabs_setup: function () {
        $('.tps-contact-label').hide();
		if($modal.find('input[name="primary"]').val()=="0"){
			$('#correspondance-panel,#survey-panel,#contact-tabs .phone-tab').hide();
		}
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
 $modal.find('textarea[name="text"]').val($('#sticky-notes').val()).attr('readonly',true);
 $modal.find('[data-toggle="tooltip"]').tooltip();
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
		 $modal.find('textarea[name="text"]').val($('#sticky-notes').val()).attr('readonly',true);
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
                        data_id: appointment.job_id,
						appointment_id:appointment.appointment_id
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
            campaign_functions.set_appointment_title(appointment_id,"");
        });

    },
    load_custom_fields: function () {
    },
    edit_custom_fields: function () {
    },
    save_custom_panel: function ($form) {

        var appointment_id = false;
        var urn = $form.find("[name='urn']").val();
        var data_id = $form.find("[name='data_id']").val();
        if ($form.find('input[name="3"]').val() !== "") {
            appointment_id = $form.find('input[name="3"]').val();
        }
        //Get the Client email address on the record (contact on the appointment)
        if ($('[name="appointment_contact_email"]').length > 0) {
            var client_email = $('[name="appointment_contact_email"]').val();
        }
		 if ($form.find('input[name="1"]').val()==""&&($form.find("[name='6']").val() === "Paid"||$form.find("[name='6']").val() === "Invoiced")||$form.find("[name='6']").val() === "Appointment Confirmed") {
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

        //Report Status is Submitted -> set the submitted date
        if ($form.find("[name='16']").val() === "Written report submitted" && $form.find("[name='17']").val() === ''){
            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth()+1;
            var yyyy = today.getFullYear();
            if(dd<10){
                dd='0'+dd
            }
            if(mm<10){
                mm='0'+mm
            }
            submitted_date = dd+'/'+mm+'/'+yyyy;
        }
        else if ($form.find("[name='16']").val() !== "Written report submitted" && $form.find("[name='17']").val() !== '') {
            var submitted_date = '';
        }

        //Set the submitted field if it is necessary
        if (typeof submitted_date != 'undefined') {
            $.ajax({
                url: helper.baseUrl + 'records/update_custom_data_field',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn,
                    data_id: data_id,
                    field_id: 17,
                    value: submitted_date
                }
            });
        }
		
        //Job Status is Paid
        if ($form.find("[name='6']").val() === "Paid"){
			
			if($form.find("[name='15']").val()=="Yes") {
            //Send email Referral Scheme Email to Account Role group email
            lhs.send_template_email(urn, 2, "Role Group Account", 'bradf@121customerinsight.co.uk', "", "", "Referral scheme email", appointment_id);
			}
            //Send email Receipt of Payment Email to Client email address on the record
            lhs.send_template_email(urn, 6, "Client", 'bradf@121customerinsight.co.uk', "", "", "Receipt of payement email", appointment_id);
            //Hard Copy Required is Yes
            if ($form.find("[name='10']").val() === "Yes") {
                //Send email Hard Copy Email to the Account Role group email
                lhs.send_template_email(urn, 5, "Role Group Account", 'bradf@121customerinsight.co.uk', "", "", "Hard copy notification", appointment_id);

            }

        }
        //Job Status is Paid & Issued
        else if ($form.find("[name='16']").val() === "Report Issued") {
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
            campaign_functions.set_appointment_title(appointment_id,"");
        }).fail( function() {
            flashalert.danger("Error saving the appointment title")
        });
		}
        record.email_panel.load_panel();
    },
    custom_items_loaded: function () {
        if (helper.role == "16") {
			$('#email-panel').hide();
		    $('#custom-panel').find('tr:contains(Quote)').hide();
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

        //Changes on the form elements
        campaign_functions.change_custom_item_form();
    },
    edit_custom_item_setup: function (data) {
        //Set the job winner options
        campaign_functions.job_winner_setup(data);
		if($('#appointment-panel table tr').length==0){
            $modal.find('select[name="6"] option:contains("Appointment")').attr('disabled','disabled');
            $modal.find('select[name="6"] option:contains("Invoiced")').attr('disabled','disabled');
            $modal.find('select[name="6"] option:contains("Paid")').attr('disabled','disabled');
            $modal.find('select[name="6"] option:contains("Cancelled")').attr('disabled','disabled');
            $modal.find('select[name="6"]').selectpicker('refresh');
		}

        //Changes on the form elements
        campaign_functions.change_custom_item_form();
    },
    change_custom_item_form: function() {

        //Change type of survey input
        var type_of_survey_html = '<div class="input-group">' +
            '<div class="input-group-btn">' +
            '<button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown"' +
            'aria-haspopup="true" aria-expanded="false">Select <span class="caret"></span>' +
            '</button>' +
            '<ul class="dropdown-menu type-of-survey-list">' +
            '<li><a href="#">Building Survey</a></li>' +
            '<li><a href="#">Expert Witness</a></li>' +
            '<li><a href="#">General Structural Inspection</a></li>' +
            '<li><a href="#">Home Buyer Report</a></li>' +
            '<li><a href="#">Party Wall</a></li>' +
            '<li><a href="#">Specific Inspection</a></li>' +
            '<li><a href="#">Structural Calculations</a></li>' +
            '<li><a href="#">Valuation</a></li>' +
            '</ul>' +
            '</div>' +
            '</div>';
        var $input = $modal.find('.form-group').find('input[name="7"]');
        var $new = $(type_of_survey_html);
        var $parent = $modal.find('.form-group').find('input[name="7"]').parent()
        $new.append($input);
        $parent.html($new);
        $parent.prepend($('<label>Type of Survey</label>'));

        $modal.find(".type-of-survey-list li a").click(function () {
            $modal.find('.form-group').find('input[name="7"]').val($(this).text());
        });


        //Change additional services input
        var additional_services_html = '<div class="input-group">' +
            '<div class="input-group-btn">' +
            '<button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown"' +
            'aria-haspopup="true" aria-expanded="false">Select <span class="caret"></span>' +
            '</button>' +
            '<ul class="dropdown-menu additional-services-list">' +
            '<li><a href="#">Gas Inspection</a></li>' +
            '<li><a href="#">High Level Images</a></li>' +
            '<li><a href="#">Platinum Plus</a></li>' +
            '<li><a href="#">Thermal Images</a></li>' +
            '<li><a href="#">Valuation</a></li>' +
            '</ul>' +
            '</div>' +
            '</div>';
        var $input = $modal.find('.form-group').find('input[name="11"]');
        var $new = $(additional_services_html);
        var $parent = $modal.find('.form-group').find('input[name="11"]').parent()
        $new.append($input);
        $parent.html($new);
        $parent.prepend($('<label>Additional Services</label>'));

        $modal.find(".additional-services-list li a").click(function () {
            $modal.find('.form-group').find('input[name="11"]').val($(this).text());
        });
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
    set_appointment_title: function(appointment_id,urn) {
        $.ajax({
            url: helper.baseUrl+'custom_scripts/lhs/lhs.php?set_appointment_title',
            type: "POST",
            dataType: "JSON",
            data: {
                urn:urn,
				appointment_id:appointment_id
            }
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                if (typeof record != "undefined") {
                    record.appointment_panel.load_appointments();
                }
				 if (typeof calendar != "undefined") {
                     $('#calendar').fullCalendar('refetchEvents');
                }
				
				campaign_functions.add_google_event(appointment_id);
				
				
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