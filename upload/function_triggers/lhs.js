var simulation = "";

var campaign_functions = {
    init: function () {
		$('#top-campaign-select').hide();
		$('div.custom-panel,#custom-panel').hide();
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
			if(response.data[appointment.job_id]['Job Status']['value'].length<2||response.data[appointment.job_id]['Job Status']['value']=="Appointment Possible"
			||response.data[appointment.job_id]['Job Status']['value']=="Appointment TBC"
			||response.data[appointment.job_id]['Job Status']['value']=="Appointment Confirmed"){
			 
			 if(appointment.appointment_type_id=="1"){
				 update_status = "Appointment Possible";
			 } else if(appointment.appointment_type_id=="2"){
				  update_status = "Appointment TBC";
			 } else if(appointment.appointment_type_id=="3"){
				  update_status = "Appointment Confirmed";
				  //send the appointment confirmation email
			lhs.send_template_email(record.urn, 3, "Client", appointment.contact_email, "","","Appointment confirmation",appointmment.appointment_id);				  
			 }
			 
			}
			if(update_status){
		$.ajax({
                url: helper.baseUrl + 'records/update_custom_data_field',
                type: "POST",
                dataType: "JSON",
                data: {
					urn: record.urn,
					data_id: appointment.job_id,
					field_id:6,
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
    },
    edit_custom_fields: function() {
    },
    save_custom_panel: function($form) {
		var appointment_id = false;
		if($form.find('input[name="3"]').val()!==""){
		appointment_id = $form.find('input[name="3"]').val();
		}
        //Get the Client email address on the record (contact on the appointment)
		if($('[name="appointment_contact_email"]').val().length>0){
		var client_email  = $('[name="appointment_contact_email"]').val();	
		} 

            //Job Status is Paid
            if ($form.find("[name='6']").val() === "Paid") {
                //Send email Referral Scheme Email to Account Role group email
                lhs.send_template_email(record.urn, 2, "Role Group Account", 'bradf@121customerinsight.co.uk', "","","Referral scheme email",appointment_id);

                //Send email Receipt of Payment Email to Client email address on the record
                lhs.send_template_email(record.urn, 6, "Client", 'bradf@121customerinsight.co.uk', "","","Receipt of payement email",appointment_id);
                //Hard Copy Required is Yes
                if ($form.find("[name='10']").val() === "Yes") {
                    //Send email Hard Copy Email to the Account Role group email
                    lhs.send_template_email(record.urn, 5, "Role Group Account", 'bradf@121customerinsight.co.uk', "","","Hard copy notification",appointment_id);

                }

            }
            //Job Status is Paid & Issued
            else if ($form.find("[name='6']").val() === "Paid & Issued") {
                //Send email Feedback Email to Client email address on the record
                lhs.send_template_email(record.urn, 8, "Client", 'bradf@121customerinsight.co.uk', "","","Feedback email",appointment_id);
            }
            //Job Status is Confirmed Appointment
            else if ($form.find("[name='6']").val() === "Appointment Confirmed") {
                //Send email Appointment Confirmation Email to Client email
                lhs.send_template_email(record.urn, 3, "Client", 'bradf@121customerinsight.co.uk', "","","Appointment confirmation",appointment_id);

            }

            record.email_panel.load_panel();
    },
		custom_items_loaded:function(){
			    $('.custom-panel').find('.id-title').text("Job Number");
				if(record.role=="16"){
				$('.edit-detail-btn').hide();
				$('#custom-panel').find('tr:contains(Quote)').hide();
				$('.custom-panel').find('tr:contains(Quote)').hide();
				$('.custom-panel').find('tr:contains(Invoice)').hide();
				$('div.custom-panel,#custom-panel').show();
				}
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
    send_template_email: function(urn, template_id, recipients_to_name, recipients_to, recipients_cc, recipients_bcc, email_name, appointment_id) {
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
					appointment_id:appointment_id
                }
            }).done(function(response) {
                if (response.success===true) {
                    flashalert.success(response.msg);
                }
                else if(response.success==false){
                    flashalert.danger(response.msg);
                }
            }).fail(function(){
				 flashalert.danger(msg);
			});
        }
        else {
            flashalert.danger("ERROR: No email address on: "+msg);
        }
    }
}