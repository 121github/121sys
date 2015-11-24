var simulation = "";

var campaign_functions = {
    init: function () {
		
        $(document).on('change', '.typepicker', function () {
            var title = $(this).find('option:selected').text();
            $('[name="title"]').val(title);
        });
		
        $(document).on('mouseover', '#quick-planner tbody tr', function (e) {
            var target = $(this).find('.show-apps');
            if (target.attr('data-toggle') !== "tooltip") {
                var user_id = target.attr('data-user');
                var date = target.attr('data-date');
                $.ajax({
                    url: helper.baseUrl + 'appointments/get_apps',
                    type: "POST",
                    dataType: "JSON",
                    data: {user_id: user_id, date: date}
                }).done(function (response) {
                    if (response.data.length > 0) {
                        var apps = "";
                        $.each(response.data, function (i, row) {
                            apps += '<small>'+Number(i + 1) + '. ' + row.title + ': '+ row.postcode +'<br>' + row.start + ' until ' + row.end + '</small><br>';
                        });
                        target.attr('data-toggle', 'tooltip').attr('data-placement', 'top').attr('title', apps).attr('data-html', 'true').tooltip();
                    }
                });
            }
        });

        
        $('#closest-branch').prop('class', 'pointer btn btn-xs btn-success');

        $(document).on('click', '#closest-branch', function (e) {
            e.preventDefault();
            if ($('input[name="contact_postcode"]').val() == "") {
                flashalert.danger("You must capture a valid customer postcode first");
            } else {
                //Reset the quick planner
                $('#quick-planner').html("Please choose the planner you want to use ");
                $('.panel-heading').find(".branch-name").text("");
                //Get the branches
                campaign_functions.get_branch_info();
                $('#closest-branch').prop('class', 'pointer btn btn-xs btn-success');
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
            var start = $(this).attr('data-date');
            modals.create_appointment(record.urn, start)
        });
        $(document).on('click', '[data-modal="delete-appointment"]', function () {
            $('[name="cancellation_reason"]').text("Appointment cancelled").hide();
        });

        $(document).on("click", ".branch-filter", function (e) {
            e.preventDefault();
            $(this).closest('ul').prev('button').text($(this).find('.branch_name').text());
            $(this).closest('ul').find('a').css("color", "black");
            $(this).closest('tr').find('input[name="hub-choice"]').attr('data-branch', $(this).attr('id'));
            $(this).closest('tr').find('input[name="hub-choice"]').attr('data-branch-name', $(this).attr('branch-name'));
            $(this).closest('tr').find('input[name="hub-choice"]').val($(this).attr('data-bus-attendees'));
            if ($(this).closest('tr').find('input[name="hub-choice"]').prop('checked')) {
                quick_planner.load_planner();
            }
            $('.branch-distance-' + $(this).attr('data-region')).text($(this).attr('data-distance'));
            $(this).css("color", "green");
            $('#closest-branch').prop('class', 'pointer btn btn-xs btn-default');
        });

        //Find closest branches on load
        var interval = setInterval(function () {
            campaign_functions.get_branch_info();
            clearInterval(interval);
        }, 3000);

        //Find closest branches on new address
        $(document).on("click", ".save-contact-address", function (e) {
            var interval = setInterval(function () {
                campaign_functions.get_branch_info();
                clearInterval(interval);
            }, 1000);
        });
		
    },
    contact_form_setup: function () {
        $('input[name="dob"]').closest('.form-group').hide();
        $('input[name="position"]').closest('.form-group').hide();
        $('input[name="website"]').closest('.form-group').hide();
        $('input[name="facebook"]').closest('.form-group').hide();
        $('input[name="linkedin"]').closest('.form-group').hide();
    },
    appointment_setup: function (start) {
        var attendee = $('input[name="hub-choice"]:checked').val();
        var branch = $('input[name="hub-choice"]:checked').attr('data-branch');
        campaign_functions.hsl_coverletter_address();
        campaign_functions.set_appointment_start(start);
        campaign_functions.set_appointment_attendee(attendee);
        campaign_functions.set_appointment_contact();
        $('[name="title"]').val("Home Consultancy");
        $('.branches-selection').show();
        $('.attendees-selection').removeClass("col-xs-6").addClass("col-xs-4");
        $('.contacts-selection').removeClass("col-xs-6").addClass("col-xs-4");
        if (typeof(branch) != "undefined") {
            $('.branchpicker').selectpicker('val', branch).selectpicker('refresh');
        }
    },
    set_appointment_contact: function () {
        $.ajax({
            url: helper.baseUrl + 'webforms/get_webform_answers',
            data: {urn: record.urn, webform_id: "1"},
            dataType: "JSON",
            type: "POST"
        }).done(function (response) {
            if (response.success) {
                $('.contactpicker').selectpicker('val', [response.answers.a1]);
            } else {
                alert("You have not completed the webform yet!");
            }
        }).fail(function () {
            flashalert.danger("There was an error finding the default contact");
        });
        ;
    },
    appointment_edit_setup: function () {
        campaign_functions.hsl_coverletter_address();
        $('.branches-selection').show();
        $('.attendees-selection').removeClass("col-xs-6").addClass("col-xs-4");
        $('.contacts-selection').removeClass("col-xs-6").addClass("col-xs-4");
    },
    hsl_coverletter_address: function () {
        $options = $('#addresspicker').html();
        $cover_letter_address = $("<div class='form-group'><p>Please select the recipient address for the appointment confirmation letter</p><select data-width='100%' id='cl_addresspicker'><option value=''>Same as the appointment</option>" + $options + "</select></div>");
        $cover_letter_address.find('option[value="Other"]').remove();

        $cover_letter_address.insertBefore($('#select-appointment-address'));
        $('#cl_addresspicker').selectpicker();

        $(document).on('change', '#cl_addresspicker', function () {
            $.ajax({
                url: helper.baseUrl + 'ajax/add_cover_letter_address',
                data: {coverletter_address: $(this).val()},
                dataType: "JSON",
                type: "POST"
            })
        });
    },
    set_appointment_attendee: function (attendee) {
        $('.attendeepicker').selectpicker('val', [attendee]);
    },
    set_appointment_start: function (start) {
        var m = moment(start, "YYYY-MM-DD HH:mm");
        $('.startpicker').data("DateTimePicker").date(m);
        $('.endpicker').data("DateTimePicker").date(m.add('hours', 1).format('DD\MM\YYYY HH:mm'));
    },
    get_branch_info: function (id) {
        //Get the branches
        var contact_postcode = $('input[name="contact_postcode"]').val();

        $.ajax({
            url: helper.baseUrl + 'ajax/get_branch_info',
            type: "POST",
            dataType: "JSON",
            data: {postcode: contact_postcode, branch_id: id}
        }).done(function (response) {
            if (response) {
                var branch_info = "";
                branch_info += "<table class='table small table-condensed table-striped'>" +
                    "<thead><tr><th>Hub</th><th>Branch</th><th>Distance</th></tr><thead>" +
                    "<tbody>";
	
                $.each(response.branches, function (i, region) {
                    var options = "";
                    var default_branch_id = region.branches[0].id;
                    var default_branch_name = region.branches[0].name;
                    var default_distance = region.branches[0].distance;
					var first_attendee = region.brus_attendees;
                    if (first_attendee.indexOf(',') >= 0) {
                        first_attendee = first_attendee.substr(0, first_attendee.indexOf(','));
                    }
                    $.each(region.branches, function (i, branch) {
                        var option_color = 'black';
                        if (branch.id == region.default_branch_id) {
                            default_branch_id = branch.id;
                            default_branch_name = branch.name;
                            default_distance = branch.distance;
                            option_color = 'green';
                        }
                        options += "<li><a href='#' class='branch-filter' id='" + branch.id + "' style='color: " + option_color + "' branch-name='" + branch.name + "' data-bus-attendees='" + first_attendee + "' data-region='" + region.id + "' data-distance='" + branch.distance + "'>" +
                            "<span class='branch_name'>" + branch.name + "</span>" +
                            "<span style='font-size: 10px;'>" + " - " + branch.distance + "</span>" +
                            "</a></li>";
                    });

                    branch_info += "<tr>" +
                        "<td>" + region.name + "</td>" +
                        "<td><div class='btn-group' style='width: 100%;'>" +
                        "<button class='btn btn-default btn-xs dropdown-toggle' style='width: 100%; text-align: left' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>" +
                        default_branch_name +
                        "</button>" +
                        "<ul class='dropdown-menu'>" + options + "</ul>" +
                        "</td>" +
                        "<td class='branch-distance-" + region.id + "'>" + default_distance + "</td>" +
                        "<td><input type='radio' name='hub-choice' data-branch='" + default_branch_id + "' data-branch-name='" + default_branch_name + "' data-region='" + region.id + "' value='" + first_attendee + "'/></td></tr>";
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
    appointment_saved: function (appointment_id, state) {
        //Send appointment_confirmation + cover_letter to hsl
        var branch_id = null;
        $.ajax({
            url: helper.baseUrl + 'email/send_appointment_confirmation',
            data: {
                appointment_id: appointment_id,
                branch_id: branch_id,
                state: state,
                send_to: 'HCletters@hslchairs.com'
            },
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {

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
                if (waypoints.length-1>i) {
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
        var driver_name = $('input[name="hub-choice"]:checked').attr('data-driver-name');
        var branch = $('input[name="hub-choice"]:checked').attr('data-branch');
        var branch_name = $('input[name="hub-choice"]:checked').attr('data-branch-name');
        var slot = $('input[name="slot"]:checked').val();
        $('.panel-heading').find(".branch-name").text(branch_name);
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
                quick_planner.planner_summary_hsl(response);
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
            var force = "";
			var time = "00:00:00";
            if (slots.apps == slots.max_apps) {
                var btn_text = "Show";
            }
			$.each(waypoint,function(i,v){
			if (typeof v.datetime !== "undefined") {
                time = v.datetime;
            }
			});

            var empty_tooltip = slots.apps == "0" ? " data-toggle='tooltip' data-html='true' data-placement='top' title='No appointments booked'" : "";
            var tooltip = slots.reason ? " data-toggle='tooltip' data-html='true' data-placement='top' title='Not available: " + slots.reason + "'" : empty_tooltip;
            var holiday = slots.reason ? "class='purple'" : "";
            color = slots.apps >= slots.max_apps && slots.max_apps > 0 ? "class='danger'" : holiday;
            table += "<tr " + color + "><td>" + waypoint[0].uk_date + "</td><td><div class='pointer show-apps' data-date='" + date + "' data-user='" + simulation.user_id + "' " + tooltip + " >" + slots.apps + "/" + slots.max_apps + "</div></td><td>" + stats[stats.length-1].added_distance.text + "</td><td>" + stats[stats.length-1].added_duration.text + "</td><td><button class='btn btn-default btn-xs simulate' data-date='" + date + "' data-time='" + time + "' data-uk-date='" + waypoint[0].uk_date + "' " + force + " >Simulate</button></td></tr>";

        });
        table += "</tbody></table></div>";
        $('#quick-planner').html(table);
        $('#quick-planner .show-apps[data-toggle="tooltip"]').tooltip().tooltip('hide');

    },
	
    planner_summary_hsl: function (data) {
        simulation = data;
        var table = "";
        table += "<div class='table-responsive' style='overflow:auto; max-height:215px'><table class='small table table-condensed'><thead><tr><th>Date</th><th>Slots</th><th>Total Distance</th><th>Total Duration</th><th>Route</th><tr></thead><tbody>";
        $.each(data.waypoints, function (date, waypoint) {
            var btn_text = "Simulate";
            var slots = data.slots[date];
            var stats = data.stats[date];
            var force = "";
            if (slots.apps == slots.max_apps) {
                var btn_text = "Show";
            }
            if (typeof waypoint.slot1.datetime !== "undefined") {
                var time = waypoint.slot1.datetime;
            }
            if (typeof waypoint.slot2.datetime !== "undefined") {
                var time = waypoint.slot2.datetime;
            }
            var empty_tooltip = slots.apps == "0" ? " data-toggle='tooltip' data-html='true' data-placement='top' title='No appointments booked'" : "";
            var tooltip = slots.reason ? " data-toggle='tooltip' data-html='true' data-placement='top' title='Not available: " + slots.reason + "'" : empty_tooltip;
            var holiday = slots.reason ? "class='purple'" : "";
            color = slots.apps >= slots.max_apps && slots.max_apps > 0 ? "class='danger'" : holiday;
            table += "<tr " + color + "><td>" + waypoint.start.uk_date + "</td><td><div class='pointer show-apps' data-date='" + date + "' data-user='" + simulation.user_id + "' " + tooltip + " >" + slots.apps + "/" + slots.max_apps + "</div></td><td>" + stats[5].distance.text + "</td><td>" + stats[5].duration.text + "</td><td><button class='btn btn-default btn-xs simulate' data-date='" + date + "' data-time='" + time + "' data-uk-date='" + waypoint.start.uk_date + "' " + force + " >Simulate</button></td></tr>";

        });
        table += "</tbody></table></div>";
        $('#quick-planner').html(table);
        $('#quick-planner .show-apps[data-toggle="tooltip"]').tooltip();

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

if(helper.role>2){
	$(".outcomepicker .dropdown-menu ul li:contains('Remove from records')").remove();
}

});