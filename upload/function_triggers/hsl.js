var simulation = "";

var campaign_functions = {
    init: function () {
    

        $(document).on('click', '[data-modal="delete-appointment"]', function () {
            $('[name="cancellation_reason"]').text("Appointment cancelled").hide();
        });
		//Find closest branches on load
        var interval = setInterval(function () {
            campaign_functions.get_branch_info();
            clearInterval(interval);
        }, 3000);
		
		 $(document).on("click", ".branch-filter", function (e) {
            e.preventDefault();
            $(this).closest('ul').prev('button').text($(this).find('.branch_name').text());
            $(this).closest('ul').find('a').css("color", "black");
            quick_planner.branch_id = $(this).attr('id');
            quick_planner.branch_name= $(this).attr('branch-name');
			quick_planner.driver_id = $(this).attr('data-bus-attendees');
			
			$(this).closest('tr').find('input[name="hub-choice"]').attr('data-branch', quick_planner.branch_id);
            $(this).closest('tr').find('input[name="hub-choice"]').attr('data-branch-name', quick_planner.branch_name);
            $(this).closest('tr').find('input[name="hub-choice"]').val(quick_planner.driver_id)
			
            if ($(this).closest('tr').find('input[name="hub-choice"]').prop('checked')) {
			   $("#quick-planner-panel .branch-name-text").text(quick_planner.branch_name);
               quick_planner.load_planner();
            }
            $('.branch-distance-' + $(this).attr('data-region')).text($(this).attr('data-distance'));
            $(this).css("color", "green");
            $('#closest-branch').prop('class', 'pointer btn btn-xs btn-default');
        });
 		$(document).on('change', 'input[name="hub-choice"]', function () {
            quick_planner.driver_id = $(this).val();
			quick_planner.branch_id = $(this).attr('data-branch');
			quick_planner.branch_name = $(this).attr('data-branch-name');
			quick_planner.region_id = $(this).attr('data-region');
			$("#quick-planner-panel .branch-name-text").text(quick_planner.branch_name);
            //$('a.filter[data-val="' + driver_id + '"]').trigger('click');
            quick_planner.load_planner();
        })

        //Find closest branches on new address
        $modal.on("click", ".save-contact-address", function (e) {
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
 		campaign_functions.hsl_coverletter_address();
        quick_planner.set_appointment_start(start);
        quick_planner.set_appointment_attendee(quick_planner.driver_id);
        campaign_functions.set_appointment_contact();
       
        $modal.find('.branches-selection').show();
        $modal.find('.attendees-selection').removeClass("col-xs-6").addClass("col-xs-4");
        $modal.find('.contacts-selection').removeClass("col-xs-6").addClass("col-xs-4");
        if (quick_planner.branch_id!==false) {
            $modal.find('.branchpicker').selectpicker('val', quick_planner.branch_id).selectpicker('refresh');
        }
		 $modal.find('.typepicker').trigger('change');
    },
    set_appointment_contact: function () {
        $.ajax({
            url: helper.baseUrl + 'webforms/get_webform_answers',
            data: {urn: record.urn, webform_id: "1"},
            dataType: "JSON",
            type: "POST"
        }).done(function (response) {
            if (response.success) {
                $modal.find('.contactpicker').selectpicker('val', [response.answers.a1]);
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
        $modal.find('.branches-selection').show();
        $modal.find('.attendees-selection').removeClass("col-xs-6").addClass("col-xs-4");
        $modal.find('.contacts-selection').removeClass("col-xs-6").addClass("col-xs-4");
    },
    hsl_coverletter_address: function () {
        $options = $('#addresspicker').html();
        $cover_letter_address = $("<div class='form-group'><p>Please select the recipient address for the appointment confirmation letter</p><select data-width='100%' id='cl_addresspicker'><option value=''>Same as the appointment</option>" + $options + "</select></div>");
        $cover_letter_address.find('option[value="Other"]').remove();

        $cover_letter_address.insertBefore($('#select-appointment-address'));
        $('#cl_addresspicker').selectpicker();

        $modal.on('change', '#cl_addresspicker', function () {
            $.ajax({
                url: helper.baseUrl + 'ajax/add_cover_letter_address',
                data: {coverletter_address: $(this).val()},
                dataType: "JSON",
                type: "POST"
            })
        });
    },
    get_branch_info: function () {
		var $panel =  $('#branch-info');
        $.ajax({
            url: helper.baseUrl + 'ajax/get_branch_info',
            type: "POST",
            dataType: "JSON",
            data: {postcode: quick_planner.contact_postcode, branch_id: quick_planner.branch_id}
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
               $panel.find('.panel-body').html(branch_info);
            } else {
                $panel.find('.panel-body').html("<p>Please enter a contact postcode to find the closest hub, or select a hub using the options above</p>");
            }
        }).fail(function () {
            $panel.find('.panel-body').html("<p>Please enter a contact postcode to find the closest hub, or select a hub using the options above</p>");
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
/*
var quick_planner = {
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
            if (typeof waypoint.datetime !== "undefined") {
                var time = waypoint.datetime;
            }
            if (typeof waypoint.datetime !== "undefined") {
                var time = waypoint.datetime;
            }
            var empty_tooltip = slots.apps == "0" ? " data-toggle='tooltip' data-html='true' data-placement='top' title='No appointments booked'" : "";
            var tooltip = slots.reason ? " data-toggle='tooltip' data-html='true' data-placement='top' title='Not available: " + slots.reason + "'" : empty_tooltip;
            var holiday = slots.reason ? "class='purple'" : "";
            color = slots.apps >= slots.max_apps && slots.max_apps > 0 ? "class='danger'" : holiday;
            table += "<tr " + color + "><td>" + waypoint[0].uk_date + "</td><td><div class='pointer show-apps' data-date='" + date + "' data-user='" + simulation.user_id + "' " + tooltip + " >" + slots.apps + "/" + slots.max_apps + "</div></td><td>" + stats[stats.length-1].distance.text + "</td><td>" + stats[stats.length-1].duration.text + "</td><td><button class='btn btn-default btn-xs simulate' data-date='" + date + "' data-time='" + time + "' data-uk-date='" + waypoint[0].uk_date + "' " + force + " >Simulate</button></td></tr>";

        });
        table += "</tbody></table></div>";
        $('#quick-planner').html(table);
        $('#quick-planner .show-apps[data-toggle="tooltip"]').tooltip();

    }
}
*/
//add function to add to planner when an appointment is added/updated


$(document).ready(function () {
    campaign_functions.init();
    //hsl requests
     $(record.record_panel).html($(record.record_panel).html().replace("Record Details", "Progress Summary"));

if(helper.role>2){
	 $(record.record_panel).find(".outcomepicker .dropdown-menu ul li:contains('Remove from records')").remove();
}

});