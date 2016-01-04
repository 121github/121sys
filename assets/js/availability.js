var quick_planner = {
	init:function(){
		//set variables
		this.driver_id = false;
		this.driver_name = false;
		this.branch_id = false;
		this.branch_name = false;
		this.region_id = false;
		this.region_name = false;
		this.slot_id = false;
		this.contact_postcode = $('input[name="contact_postcode"]').length?$('input[name="contact_postcode"]').val():false;
		this.company_postcode = $('input[name="company_postcode"]').length?$('input[name="company_postcode"]').val():false;
		
//add listners
        $(document).on('change', '.typepicker', function () {
            var type = $('.typepicker').val()!==""?$(this).find('option:selected').text():"Appointment";
			var title = $('#contact-select option:selected').length>0?type+' with '+$('#contact-select option:selected').text():type;
            $('[name="title"]').val(title);
        }); 
        $(document).on('mouseover', '#quick-planner tbody tr', function (e) {
            var target = $(this).find('.show-apps');
            if (target.length>0) {
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
                        target.attr('data-toggle', 'tooltip').attr('data-placement', 'top').attr('data-original-title', apps).attr('data-html', 'true');
						target.removeClass('show-apps');
                    }
                });
            }
        });

		 $(document).on('change', 'input[name="slot"]', function () {
            quick_planner.driver_id = $(this).val();
			quick_planner.branch_id = $(this).attr('data-branch');
			quick_planner.branch_name = $(this).attr('data-branch-name');
			quick_planner.region_id = $(this).attr('data-region');
            //$('a.filter[data-val="' + driver_id + '"]').trigger('click');
            //quick_planner.load_planner();
        })
		        $(document).on('click', '#closest-branch', function (e) {
            e.preventDefault();
            if (quick_planner.contact_postcode == "") {
                flashalert.danger("You must capture a valid customer postcode first");
            } else {
                //Reset the quick planner
                $('#quick-planner').html("Please choose the planner you want to use ");
                $('.panel-heading').find(".branch-name").text("");
                //Get the branches
                campaign_functions.get_branch_info();
            }
        });

        $(document).on('click', 'a.region-select', function (e) {
            e.preventDefault();
            quick_planner.branch_id = $(this).attr('data-branch-id');
            campaign_functions.get_branch_info(quick_planner.branch_id);
        });
        $(document).on('click', 'button.simulate', function (e) {
            var date = $(this).attr('data-date');
            var uk_date = $(this).attr('data-uk-date');
            var time = $(this).attr('data-time');
            quick_planner.popup_simulation(uk_date, date, time, simulation.waypoints[date], simulation.stats[date], simulation.slots[date]);
        });
        $(document).on('click', '.continue-simulation', function (e) {
            e.preventDefault();
            var start = $(this).attr('data-date');
            modals.create_appointment(record.urn, start)
        });
	},
	appointment_setup: function (start) {
		if(typeof start == "undefined"){
			start = $('#slots-panel').find('input:checked').attr('data-date') +' '+ $('#slots-panel').find('input:checked').attr('data-time');	;	
		}
        quick_planner.set_appointment_start(start);
        quick_planner.set_appointment_attendee(quick_planner.driver_id);
	},
	set_appointment_attendee: function () {
        $('.attendeepicker').selectpicker('val', [quick_planner.driver_id]);
    },
    set_appointment_start: function (start) {
        var m = moment(start, "YYYY-MM-DD HH:mm");
        $('.startpicker').data("DateTimePicker").date(m);
        $('.endpicker').data("DateTimePicker").date(m.add('hours', 1).format('DD\MM\YYYY HH:mm'));
    },
	check_selections: function () {
		if(typeof quick_planner !== "undefined"){
		if(!quick_planner.contact_postcode.length>0&&!quick_planner.company_postcode.length>0){
		return false;	
		}
        if (quick_planner.driver_id > 0) {
            return true
        } else {
            flashalert.danger("Please select a hub/branch");
        }
		} else {
		 return true;	
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
        if (quick_planner.check_selections()) {
            $.ajax({
                url: helper.baseUrl + 'planner/simulate_121_planner',
                type: "POST",
                dataType: "JSON",
                data: {postcode: quick_planner.company_postcode.length>0?quick_planner.company_postcode:quick_planner.contact_postcode, 
				driver_id: quick_planner.driver_id, 
				branch_id: quick_planner.branch_id, 
				slot: quick_planner.slot},
                beforeSend: function () {
                    $('#quick-planner').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                }
            }).done(function (response) {
				if(response.success){
                quick_planner.planner_summary(response);
				} else {
				 $('#quick-planner').html("<p>"+response.msg+"</p>");	
				}
            });
        } else {
			 $('#quick-planner').html("<p>You must add an address to the record before you can plan the journey</p>");
		}
    },
 planner_summary: function (data) {
        simulation = data;
        var table = "";
		
        table += "<div class='table-responsive' style='overflow:auto; max-height:215px'><table class='small table table-condensed'><thead><tr><th>Date</th><th>Slots</th><th>Total Distance</th><th>Total Duration</th><th>Route</th><tr></thead><tbody>";
        $.each(data.waypoints, function (date, waypoint) {
			var rule_tooltip="";
            var btn_text = "Simulate";
            var slots = data.slots[date];
            var stats = data.stats[date];
            var force = "";
			var holiday = "";
			var show_apps = "";
			var tt = "data-toggle='tooltip' data-html='true' data-placement='top' title='No appointments booked'";
			var time = "00:00:00";
            if (slots.apps == slots.max_apps) {
                var btn_text = "Show";
            }
			$.each(waypoint,function(i,v){
			if (typeof v.datetime !== "undefined") {
                time = v.datetime;
            }
			});
if(slots.reason.length>0){
rule_tooltip = "<span class='fa fa-info-circle tt' data-toggle='tooltip' data-html='true' data-placement='top' title='"+slots.reason+"'></span>";
holiday="class='purple'";
}
if(slots.apps>0){
show_apps = "show-apps";
}
            color = slots.apps >= slots.max_apps && slots.max_apps > 0 ? "class='danger'" : holiday;
            table += "<tr " + color + "><td>" + waypoint[0].uk_date + "</td><td><div "+tt+" class='pointer "+show_apps+"' data-date='" + date + "' data-user='" + simulation.user_id + "'>" + slots.apps + "/" + slots.max_apps + "</div> "+rule_tooltip+"</td><td>" + stats[stats.length-1].added_distance.text + "</td><td>" + stats[stats.length-1].added_duration.text + "</td><td><button class='btn btn-default btn-xs simulate' data-date='" + date + "' data-time='" + time + "' data-uk-date='" + waypoint[0].uk_date + "' " + force + " >Simulate</button></td></tr>";

        });
        table += "</tbody></table></div>";
        $('#quick-planner').html(table);
        $('#quick-planner [data-toggle="tooltip"]').tooltip().tooltip('hide');

    }
	
}