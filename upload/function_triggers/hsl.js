var simulation = "";

var campaign_functions = {
    init: function () {

        $(document).on('change', '.typepicker', function () {
            var title = $(this).find('option:selected').text();
            $('[name="title"]').val(title);
        });
$(document).on('click','.show-apps',function(e){
var user_id =$(this).attr('data-user');
var date =$(this).attr('data-date');
$.ajax({ url:helper.baseUrl+'appointments/get_apps',
type:"POST",
dataType:"JSON",
data:{ user_id:user_id,date:date }
}).done(function(response){
	if(response.data.length>0){
	var apps="";
	$.each(response.data,function(i,row){
		apps+= Number(i+1)+'. '+row.title+ ': '+row.start+' until '+row.end+'\n';
	});
	alert(apps);
	} 
		
});
});
        campaign_functions.get_branch_info();
        $(document).on('click', '#closest-branch', function (e) {
            e.preventDefault();
            if ($('input[name="contact_postcode"]').val() == "") {
                flashalert.danger("You must capture a valid customer postcode first");
            } else {
                campaign_functions.get_branch_info();
            }
        });
        $(document).on('click', 'a.region-select', function (e) {
            e.preventDefault();
            var id = $(this).attr('data-branch-id');
            campaign_functions.get_branch_info(id);
        });
        $(document).on('click', 'button.simulate', function (e) {
            var date = $(this).attr('data-date');
            var uk_date = $(this).attr('data-uk-date');
            var time = $(this).attr('data-time');
            quick_planner.popup_simulation(uk_date, date, time, simulation.waypoints[date], simulation.stats[date], simulation.slots[date]);
        });

        $(document).on('change', 'input[name="hub-choice"],input[name="slot"]', function () {
            var driver_id = $(this).val();
            $('a.filter[data-val="' + driver_id + '"]').trigger('click');
            quick_planner.load_planner();
        })

        $(document).on('click', '.continue-simulation', function (e) {
            e.preventDefault();
            var attendee = $('input[name="hub-choice"]:checked').val();
            var start = $(this).attr('data-date');
            modals.create_appointment(record.urn, start, attendee)

        });
    },
    contact_form_setup: function () {
        $('input[name="dob"]').closest('.form-group').hide();
        $('input[name="position"]').closest('.form-group').hide();
        $('input[name="website"]').closest('.form-group').hide();
        $('input[name="facebook"]').closest('.form-group').hide();
        $('input[name="linkedin"]').closest('.form-group').hide();
    },
    appointment_setup: function (start, attendee) {
        campaign_functions.set_appointment_start(start);
        campaign_functions.set_appointment_attendee(attendee);
        $('[name="title"]').val("Home Consultancy");

    },
    set_appointment_attendee: function (attendee) {
        $('.attendeepicker').selectpicker('val', [attendee]);
    },
    set_appointment_start: function (start) {
        var m = moment(start, "YYYY-MM-DD HH:mm");
        $('.startpicker').data("DateTimePicker").date(m);
        $('.endpicker').data("DateTimePicker").minDate(m);
        $('.endpicker').data("DateTimePicker").date(m.add('hours', 1).format('DD\MM\YYYY HH:mm'));
    },
    get_branch_info: function (id) {
        var contact_postcode = $('input[name="contact_postcode"]').val();

        $.ajax({
            url: helper.baseUrl + 'ajax/get_branch_info',
            type: "POST",
            dataType: "JSON",
            data: {postcode: contact_postcode, branch_id: id}
        }).done(function (response) {
            if (response) {
                var branch_info = "";
                branch_info += "<table class='table table-condensed table-striped'><thead><tr><th>Hub</th><th>Consultant</th><th>Branch</th><th>Distance</th></tr><thead><tbody>";
                $.each(response.branches, function (i, row) {
                    branch_info += "<tr><td>" + row.region_name + "</td><td>" + row.consultants[0].name + "</td><td>" + row.branch_name + "</td><td>" + row.distance + "</td><td><input type='radio' name='hub-choice' data-branch='" + row.branch_id + "' data-region='" + row.region_id + "' value='" + row.drivers[0].id + "'/></td></tr>";
                });
                branch_info += "</tbody></table>";
                $('#branch-info').html(branch_info);
            } else {
                $('#branch-info').html("<p>Please enter a contact postcode to find the closest hub, or select a hub using the options above</p>");
            }
        }).fail(function () {
            $('#branch-info').html("<p>Please enter a contact postcode to find the closest hub, or select a hub using the options above</p>");

        });


    },
    appointment_saved: function (appointment_id) {
        //Send appointment_confirmation + cover_letter to hsl
        var branch_id = null;
        $.ajax({
            url: helper.baseUrl + 'email/send_appointment_confirmation',
            data: {
                appointment_id: appointment_id,
                branch_id: branch_id,
                description: 'HSL - Appointment confirmation'
            },
            type: "POST",
            dataType: "JSON"
        }).done(function(response){

        });
    }
}

var quick_planner = {
    check_selections: function (driver, branch) {
        if (driver > 0 && branch > 0) {
            return true
        } else {
            flashalert.danger("Please select a hub/branch");
        }

    },
    popup_simulation: function (date, sqldate, time, waypoints, stats, slots) {
        var mheader = "Journey simulation for " + date;
        var mbody = "";
        var i = 0;
        $.each(waypoints, function (name, waypoint) {
            if (waypoint.postcode.length > 0) {
                mbody += '<div style="height:30px; padding:5px; margin:5px; background:#80D6FF;border-radius:10px;-moz-border-radius:10px;-webkit-border-radius:10px; text-align:center"><p><strong>' + waypoint.title + '</strong> [' + waypoint.postcode + ']';
                if (typeof waypoint.app_duration !== "undefined") {
                    mbody += waypoint.app_duration
                }
                if (typeof waypoint.time !== "undefined") {
                    mbody += " <span class='red travel-time' style='display:none'>Time: " + waypoint.time + "</span>";
                }
                mbody += '</p></div>'
                if (i < 5) {
                    mbody += '<div style="text-align:center"><span>' + stats[i].duration.text + '</span> <span class="glyphicon glyphicon-arrow-down"></span> <span>' + stats[i].distance.text + '</span></div>';
                } else {
                    mbody += '<div style="text-align:center">Total Duration: <strong>' + stats[i].duration.text + '</strong>  Total Distance: <strong>' + stats[i].distance.text + '</strong></div>';
                }
            }

            i++;
        });
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Cancel</button>';
        if (slots.apps < slots.max_apps) {
            mfooter += '<button type="submit" data-date="' + sqldate + ' ' + time + '" class="btn btn-primary pull-right continue-simulation">Continue</button>';
        }
        modals.load_modal(mheader, mbody, mfooter);
    },

    load_planner: function () {
        var contact_postcode = $('input[name="contact_postcode"]').val();
        var driver = $('input[name="hub-choice"]:checked').val();
        var branch = $('input[name="hub-choice"]:checked').attr('data-branch');
        var slot = $('input[name="slot"]:checked').val();
        if (quick_planner.check_selections(driver, branch)) {
            $.ajax({
                url: helper.baseUrl + 'planner/simulate_hsl_planner',
                type: "POST",
                dataType: "JSON",
                data: {postcode: contact_postcode, driver_id: driver, branch_id: branch, slot: slot},
                beforeSend: function () {
                    $('#quick-planner').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                }
            }).done(function (response) {
                //quick_planner.fill_width_planner(response);
                quick_planner.planner_summary(response);
            });
        }
    },

    planner_summary: function (data) {
        simulation = data;
        var table = "";
        table += "<div class='table-responsive' style='overflow:auto; max-height:215px'><table class='small table table-condensed'><thead><tr><th>Date</th><th>Slots</th><th>Total Distance</th><th>Total Duration</th><th>Route</th><tr></thead><tbody>";
        $.each(data.waypoints, function (date, waypoint) {
            var btn_text = "Simulate";
            var slots = data.slots[date];
            var stats = data.stats[date];
            if (slots.apps == slots.max_apps) {
                var btn_text = "Show";
            }
            if (typeof waypoint.slot1.datetime !== "undefined") {
                var time = waypoint.slot1.datetime;
            }
            if (typeof waypoint.slot2.datetime !== "undefined") {
                var time = waypoint.slot2.datetime;
            }
            table += "<tr><td>" + waypoint.start.uk_date + "</td><td class='pointer show-apps' data-date='" + date + "' data-user='"+simulation.user_id+"'>" + slots.apps + "/" + slots.max_apps + "</td><td>" + stats[5].distance.text + "</td><td>" + stats[5].duration.text + "</td><td><button class='btn btn-default btn-xs simulate' data-date='" + date + "' data-time='" + time + "' data-uk-date='" + waypoint.start.uk_date + "'>Simulate</button></td></tr>";
        });
        table += "</tbody></table></div>";
        $('#quick-planner').html(table);


    },
    fill_width_planner: function (data) {

        var table = "";
        table += "<div class='table-responsive' style='overflow:auto; max-height:250px'><table class='small table table-condensed'><thead><tr><th>Date</th><th>Start</th><th>-</th><th>Branch</th><th>-</th><th>Slot 1</th><th>-</th><th>Slot 2</th><th>-</th><th>Branch</th><th>-</th><th>End</th><th>Total Distance</th><th>Total Travel Time</th><tr></thead><tbody>";
        $.each(data.waypoints, function (date, waypoint) {
            var stats = data.stats[date];

            table += "<tr><td>" + date + "</td><td>" + waypoint.start.postcode + "</td><td>" + stats[0].distance.text + "</td><td>" + waypoint.branch_start.postcode + "</td><td>" + stats[1].distance.text + "</td><td>" + waypoint.slot1.postcode + "</td><td>" + stats[2].distance.text + "</td><td>" + waypoint.slot2.postcode + "</td><td>" + stats[3].distance.text + "</td><td>" + waypoint.branch_end.postcode + "</td><td>" + stats[4].distance.text + "</td><td>" + waypoint.destination.postcode + "</td><td>" + stats[5].distance.text + "</td><td>" + stats[5].duration.text + "</td></tr>";
        });

        table += "</tbody></table></div>";
        $('#quick-planner').html(table);

    }

}
//add function to add to planner when an appointment is added/updated


$(document).ready(function () {
    campaign_functions.init();
    //hsl requests
    $(".record-panel .panel-heading").html($(".record-panel .panel-heading").html().replace("Record Details", "Progress Summary"));

});