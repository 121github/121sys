var simulation = "";

var campaign_functions = {
    init: function () {

    },
    record_setup_update: function() {
        $('.progress-outcome').find('option[value=""]').text("-- Client Status --");
        $('.progress-outcome').selectpicker('refresh');
    },
    appointment_setup: function (start) {
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

        if (typeof start != "undefined") {
            quick_planner.set_appointment_start(start);
        }

        //When the type is changed
        modal_body.find('.typepicker').change(function () {
            var selectedId = $(this).val();

            //If we select Confirmed, confirmedButton -> on
            if (selectedId == 3) {
                $('#appointment-confirmed').bootstrapToggle('on');
            }
            else {
                $('#appointment-confirmed').bootstrapToggle('off');
            }
        });
		if(typeof quick_planner.driver_id !== "undefined"){
			$modal.find('.attendeepicker').selectpicker('val',quick_planner.driver_id);
		}
		$('#typepicker').closest('.form-group').find('p').text('Please choose the appointment status');
    },

    appointment_edit_setup: function () {
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
        var app = $('.startpicker').val()
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
    },
    save_appointment: function(appointment) {
        //Get the additional info
        $.ajax({
            url: helper.baseUrl + 'ajax/get_record_details',
            type: "POST",
            dataType: "JSON",
            data: {urn: appointment.urn}
        }).done(function (response) {
            var record_details = null;
            $.each(response.record_details, function (key, val) {
                //If the job reference already exists or exists the job status with a null reference number
                //We will use this record_detail
                if (appointment.appointment_id == val.c9) {
                    record_details = val;
                }
                else if (!val.c9 || val.c9 == '' || val.c9 === null){
                    val.c9 = appointment.appointment_id;
                    record_details = val;
                }
            });
            //Create a new job reference (job status)
            if (!record_details) {
                record_details = {
                    'c2': 'Confirmed Appointment',
                    'c9': appointment.appointment_id,
                };
            }

            var start_date  = new Date(appointment.start.substr(0, 10));
            if (appointment.appointment_confirmed == "1") {
                //If the ‘Express Report’ tick box is selected
                if (record_details.c7 === 'Yes') {
                    //Survey Delivery Date should be populated with a date that is 2 working days post the start date
                    start_date.setDate(start_date.getDate() + 2);
                }
                else {
                    //Survey Delivery Date should be populated with a date that is 5 working days post the start date
                    start_date.setDate(start_date.getDate() + 5);
                }
                var month = start_date.getMonth()+1;
                var day = start_date.getDate();
                record_details.d1 = ((''+day).length<2 ? '0' : '') + day + '/' +
                    ((''+month).length<2 ? '0' : '') + month + '/' +
                    start_date.getFullYear();

                record_details.c1 = appointment.appointment_id;
            }
            else {
                //Set the date to null if the appointment is not confirmed
                record_details.d1 = null;
            }
            //Save the appointment additional info.
            $.ajax({
                url: helper.baseUrl + 'ajax/save_additional_info',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: appointment.urn,
                    d1: record_details.d1,
                    c1: record_details.c1,
                    c2: record_details.c2,
                    c9: record_details.c9,
                    detail_id: record_details.detail_id
                }
            }).done(function (response) {
                flashalert.success("Survey Delivery Date Updated");
                record.additional_info.load_panel();

                campaign_functions.save_custom_fields(record_details);
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