var simulation = "";

var campaign_functions = {
    init: function () {

    },
    appointment_setup: function (start) {
        $modal.find('.startpicker').data("DateTimePicker").destroy();
        $modal.find('.endpicker').data("DateTimePicker").destroy();
        $modal.find('.startpicker').datetimepicker({
            stepping: 30,
            format: 'DD/MM/YYYY HH:mm',
            sideBySide: true,
            enabledHours: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18]
        });
        $modal.find('.endpicker').datetimepicker({
            stepping: 30,
            format: 'DD/MM/YYYY HH:mm',
            sideBySide: true,
            enabledHours: [9, 10, 11, 12, 13, 14, 15, 16, 17, 18]
        });
    },
    set_date_survey_delivery: function(appointment) {
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
                if (appointment.appointment_id == val.c1) {
                    record_details = val;
                }
                else if (!val.c1 || val.c1 == '' || val.c1 === null){
                    val.c1 = appointment.appointment_id;
                    record_details = val;
                }
            });
            //Create a new job reference (job status)
            if (!record_details) {
                record_details = {
                    'c1': appointment.appointment_id,
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
                    detail_id: record_details.detail_id
                }
            }).done(function (response) {
                flashalert.success("Survey Delivery Date Updated");
                record.additional_info.load_panel();
            });
        });
    }

}