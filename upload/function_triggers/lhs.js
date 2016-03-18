var simulation = "";

var campaign_functions = {
    init: function () {

    },
    record_setup_update: function() {
        $('.progress-outcome').find('option[value=""]').text("-- Client Status --");
        $('.progress-outcome').selectpicker('refresh');
    },
    contact_form_setup: function() {
        $('input[name="dob"]').closest('.form-group').hide();
        $('input[name="website"]').closest('.form-group').hide();
        $('input[name="facebook"]').closest('.form-group').hide();
        $('input[name="linkedin"]').closest('.form-group').hide();

        $('.position-label').html("Company");
        $('input[name="position"]').attr('placeholder', 'Company Name')

        $('select[name="tps"]').closest('.form-group').hide();
    },
    contact_panel_setup: function() {
        $('.Job-panel-label').html("Company");
        $('.tps-btn').hide();
    },
    contact_tabs_setup: function() {
        $('.tps-contact-label').hide();
    },
    appointment_setup: function (start,attendee,urn) {
			$.ajax({ url: helper.baseUrl+'appointments/get_unlinked_data_items',
				data:{urn:urn},
				dataType:"JSON",
				type:"POST"
				}).done(function(response){
				$options = "";
				if(response.data.length>0){
					data_options = "";
					$.each(response.data,function(k,row){
						data_options += "<option value='"+row.data_id+"'>Delivery #"+row.data_id+": Created on "+row.created_on+"</option>";
					});
            $data_items = $("<div class='form-group'><p>Which job is this appointment related to?</p><select data-width='100%' id='data-items' name='data_id'>" + data_options + "</select></div>");

            $data_items.insertBefore($('#select-appointment-address'));
            $('#data-items').selectpicker();
				}
			});
		
		
        $modal.find('.startpicker').data("DateTimePicker").destroy();
        $modal.find('.endpicker').data("DateTimePicker").destroy();
        $modal.find('.startpicker').datetimepicker({
            stepping: 30,
            format: 'DD/MM/YYYY HH:mm',
            sideBySide: true,
            enabledHours: [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
            daysOfWeekDisabled: [0,6]
        });
        $modal.find('.endpicker').datetimepicker({
            stepping: 30,
            format: 'DD/MM/YYYY HH:mm',
            sideBySide: true,
            enabledHours: [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19],
            daysOfWeekDisabled: [0,6]
        });

        if (start) {
            modals.set_appointment_start(start);
        }

		$modal.find('#typepicker').closest('.form-group').find('p').text('Please choose the appointment status');
		
		
    },

    appointment_edit_setup: function () {
		$modal.find('.attendees-selection').html($modal.find('.attendees-selection').html().replace('Please choose the attendee(s) ','Please choose the surveyor '));
		
		
        $modal.find('.startpicker').data("DateTimePicker").enabledHours([7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
        $modal.find('.startpicker').data("DateTimePicker").daysOfWeekDisabled([0,6]);

        $modal.find('.endpicker').data("DateTimePicker").enabledHours([7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]);
        $modal.find('.endpicker').data("DateTimePicker").daysOfWeekDisabled([0,6]);

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
		$('#typepicker').closest('.form-group').find('p').text('Please choose the appointment status');
    },

    set_appointment_confirmation: function() {
       /* var app = $('.startpicker').val()
        var start_date = moment(app, 'DD/MM/YYYY HH:mm');
        var m = moment();
        var duration = moment.duration(start_date.diff(m)).days();
        if(duration<3&&duration>=0){
            $modal.find('#appointment-confirmed').bootstrapToggle('enable');
            $modal.find('#appointment-confirmed').off('click');
            modal_body.find('.typepicker').find('option[value="3"]').attr('disabled',false);
        } else {
            $modal.find('#appointment-confirmed').bootstrapToggle('disable');
            modal_body.find('.typepicker').find('option[value="3"]').attr('disabled',true);
        }
        modal_body.find('.typepicker').selectpicker('refresh');

        $('#appointment-confirmed').on('change',function(e){
            if($(this).prop("checked")){
                modal_body.find('.typepicker').selectpicker('val',3).selectpicker('refresh');
            } else {
                if (modal_body.find('.typepicker').selectpicker('val') == 3) {
                    modal_body.find('.typepicker').selectpicker('val',2).selectpicker('refresh');
                }
            }
        });
		*/
    },
    save_appointment: function(appointment) {			
        //Get the additional info
        $.ajax({
            url: helper.baseUrl + 'ajax/load_custom_panel',
            type: "POST",
            dataType: "JSON",
            data: {id:"1",urn: appointment.urn}
        }).done(function (response) {
			console.log(response.data);
			console.log(appointment);
            var start_date  = new Date(appointment.start.substr(0, 10));
            if (appointment.appointment_type_id == "3") {
				var express = false;
				if(typeof response.data[appointment.job_id]['Express report'] !== "undefined"){
				if(response.data[appointment.job_id]['Express report']['value']=="Yes"){
				express = "Yes";	
				}
				}
				console.log(express);
                //If the ‘Express Report’ tick box is selected
                if (express === 'Yes') {
                    //Survey Delivery Date should be populated with a date that is 2 working days post the start date
                    start_date.setDate(start_date.getDate() + 2);
                }
                else {
                    //Survey Delivery Date should be populated with a date that is 5 working days post the start date
                    start_date.setDate(start_date.getDate() + 5);
                }
                var month = start_date.getMonth()+1;
                var day = start_date.getDate();
                survey_date = ((''+day).length<2 ? '0' : '') + day + '/' +
                    ((''+month).length<2 ? '0' : '') + month + '/' +
                    start_date.getFullYear();
            }
            else {
                //Set the date to null if the appointment is not confirmed
                survey_date = null;
            }
			 var update_status = false;	
			if(typeof response.data[appointment.job_id]['Job Status']=="undefined"){
			 var update_status = true;	
			}
			if(response.data[appointment.job_id]['Job Status']['value']==""){
			 var update_status = true;	
			}
			console.log(update_status);
			if(update_status){
		$.ajax({
                url: helper.baseUrl + 'records/update_custom_data_field',
                type: "POST",
                dataType: "JSON",
                data: {
					urn: record.urn,
					data_id: appointment.job_id,
					field_id:6,
                    value: 2
                }
            })	
			}
            //Save the appointment additional info.
            $.ajax({
                url: helper.baseUrl + 'records/update_custom_data_field',
                type: "POST",
                dataType: "JSON",
                data: {
					urn: record.urn,
					data_id: appointment.job_id,
					field_id:9,
                    value: survey_date
                }
            }).done(function (response) {
					custom_panels.load_all_panels()
            });

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
        });

    },
    load_custom_fields: function() {
        $.each($('#custom-panel').find('.c1'), function () {
            if ($(this).html() != '' && $(this).html() != '-') {
                $(this).prepend('#');
            }
        });
    },
    edit_custom_fields: function() {
        //Enable Job Status Dropdown since the job reference is set
        var record_details_panel = $('#custom-panel');
        if (record_details_panel.find('input[name="c1"]') &&
            record_details_panel.find('input[name="c1"]').val() !== null &&
            record_details_panel.find('input[name="c1"]').val().length>0)
        {
            record_details_panel.find('input[name="c1"]').val("#"+record_details_panel.find('input[name="c1"]').val());
            $('#custom-panel').find('select[name="c2"]').attr('disabled',false).selectpicker('refresh');
        }
    },
    save_custom_fields: function(data) {



        //Get the Client email address on the record (contact on the appointment)
        var client_email = "";
        //If it has an appointment associated get the contact appointment email
        if (data.c1 && data.c1 != '') {
            $.ajax({
                url: helper.baseUrl + 'appointments/get_contact_appointment',
                type: "POST",
                dataType: "JSON",
                data: {
                    appointment_id: data.c1
                }
            }).done(function (response) {
                client_email = response.email;
            });
        }
        //If it doesn't have an appointment associated, get the contact email(s)
        else {
            $.ajax({
                url: helper.baseUrl + 'ajax/get_contacts',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: data.urn
                }
            }).done(function (response) {
                var client_email_ar = [];
                $.each(response.data, function (key, val) {
                    client_email_ar.push(val.visible['Email address']);
                });
                client_email.split(",");
            });
        }

        //Get the Account role group emails
        var account_role_email = "";
        var account_role_email_ar = [];
        $.ajax({
            url: helper.baseUrl + 'ajax/get_users_by_role',
            type: "POST",
            dataType: "JSON",
            data: {
                role_id: 15
            }
        }).done(function (response) {
            $.each(response.data, function (key, val) {
                account_role_email_ar.push(val.user_email);
            });
        });

        //TODO Fix this to avoid the timeset and call the functions asynchrony
        //Send email templates if it is needed
        setTimeout(function () {
            account_role_email = account_role_email_ar.join(",");

            //Job Status is Paid
            if (data.c2 === "Paid") {
                //Send email Referral Scheme Email to Account Role group email
                lhs.send_template_email(data.urn, 2, "Role Group Account", account_role_email, "","","It was sent a Referral Scheme Email to the Account Role group");

                //Send email Receipt of Payment Email to Client email address on the record
                lhs.send_template_email(data.urn, 6, "Client", client_email, "","","It was sent a Receipt of Payment Email to the client");

                //Hard Copy Required is Yes
                if (data.c5 === "Yes") {
                    //Send email Hard Copy Email to the Account Role group email
                    lhs.send_template_email(data.urn, 5, "Role Group Account", client_email, "","","It was sent a Hard Copy Email to the Account Role group");

                }

            }
            //Job Status is Paid & Issued
            else if (data.c2 === "Paid & Issued") {
                //Send email Feedback Email to Client email address on the record
                lhs.send_template_email(data.urn, 8, "Client", client_email, "","","It was sent a Feedback Email to the client");

            }
            //Job Status is Confirmed Appointment
            else if (data.c2 === "Confirmed Appointment") {
                //Send email Appointment Confirmation Email to Client email
                lhs.send_template_email(data.urn, 3, "Client", client_email, "","","It was sent an Appointment Confirmation Email to the client");

            }

            record.email_panel.load_panel();

        }, 2000);
    },
		custom_items_loaded:function(){
			    $('.custom-panel').find('.id-title').text("Job Number");
		},
		new_custom_item_setup:function(){

		},
    set_access_address: function() {
        if (typeof $('.accessaddresspicker option:selected').val() !== 'undefined') {
            if ($('.accessaddresspicker option:selected').val().length <= 0) {
                $.each($('#accessaddresspicker option'), function () {
                    if ($(this).attr('data-title') == "Access Detail Address") {
                        $('#access-add-check').bootstrapToggle('on');
                        $('#accessaddresspicker').selectpicker('val',$(this).val()).selectpicker('refresh');
                    }
                });
            }
        }
    }

}

var lhs = {
    send_template_email: function(urn, template_id, recipients_to_name, recipients_to, recipients_cc, recipients_bcc, msg) {
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
                    msg: msg
                }
            }).done(function(response) {
                if (response.success) {
                    flashalert.success(response.msg);
                }
                else {
                    flashalert.danger(response.msg);
                }
            });
        }
        else {
            flashalert.danger("ERROR: No email address on: "+msg);
        }
    }
}