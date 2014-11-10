var hours = {
    //initalize the team specific buttons 
    init: function() {
    	$('.daterange').daterangepicker({
            opens: "left",
            ranges: {
            	'Next 7 Days': [moment(), moment().add('days', 6)],
            	'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
            	'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()]
            },
            format: 'DD/MM/YYYY',
            minDate: "02/07/2014",
            //maxDate: moment(),
            startDate: moment(),
            endDate: moment()
        },
        function(start, end, element) {
        	var $btn = this.element;
            $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
            $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
            $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
            hours.load_hours();
        });
	    $(document).on("click", '.daterange', function(e) {
	        e.preventDefault();
	    });
	
	    $(document).on("click", ".campaign-filter", function(e) {
	        e.preventDefault();
	        $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
	        $(this).closest('ul').find('a').css("color","black");
	        $(this).css("color","green");
	        hours.load_hours();
	    });
		    $(document).on("click", ".team-filter", function(e) {
	        e.preventDefault();
	        $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
			 $(this).closest('form').find('input[name="agent"]').val('');
	        $(this).closest('ul').find('a').css("color","black");
	        $(this).css("color","green");
	        hours.load_hours();
	    });
	    $(document).on("click", ".agent-filter", function(e) {
	        e.preventDefault();
	        $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="team"]').val('');
	        $(this).closest('ul').find('a').css("color","black");
	        $(this).css("color","green");
	        hours.load_hours();
	    });
	    
        $(document).on('click', '.save-btn', function(e) {
            e.preventDefault();
            hours.save($(this));
        });
        $(document).on('click', '.set-default-hour-btn', function(e) {
            e.preventDefault();
            $(this).hide();
            hours.set_default_hour($(this));
        });
        $(document).on('click', '.edit-btn', function() {
            hours.edit($(this));
        });
        $(document).on('click', '.close-btn', function(e) {
        	e.preventDefault();
        	hours.cancel();
        });
        
        //start the function to load the hours into the table
        if ($('form').find('input[name="date_from"]').val() == '') {
        	$('form').find('input[name="date_from"]').val(moment().format('YYYY-MM-DD'));
        }
        if ($('form').find('input[name="date_to"]').val() == '') {
        	$('form').find('input[name="date_to"]').val(moment().format('YYYY-MM-DD'));
        }
        hours.load_hours();
    },
    //this function reloads the hours into the table body
    load_hours: function() {
        $.ajax({
            url: helper.baseUrl + 'hour/get_hours_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            $tbody = $('.hours-body');
            $tbody.empty();
            $.each(response.data, function(date, hours) {
                if (hours.length) {
                    $.each(hours, function(i, val) {

                        //Default Hours
                        var default_hours = (val.default_hours)?val.default_hours:'';

                        //Duration if exists
                    	var duration = (val.duration)?val.duration:(default_hours)?default_hours:'';

                        var hours = Math.floor(duration / 3600);
                        var minutes = Math.floor((duration - (hours * 3600)) / 60);
                        var seconds = duration - (hours * 3600) - (minutes * 60);

                        if (hours < 10) {
                            hours = "0" + hours;
                        }
                        if (minutes < 10) {
                            minutes = "0" + minutes;
                        }
                        if (seconds < 10) {
                            seconds = "0" + seconds;
                        }

                        var duration_time = hours + 'h ' + minutes + 'm ';

                        //Time logged if exists
                        var hours = Math.floor(val.time_logged / 3600);
                        var minutes = Math.floor((val.time_logged - (hours * 3600)) / 60);
                        var seconds = val.time_logged - (hours * 3600) - (minutes * 60);

                        if (hours < 10) {
                            hours = "0" + hours;
                        }
                        if (minutes < 10) {
                            minutes = "0" + minutes;
                        }
                        if (seconds < 10) {
                            seconds = "0" + seconds;
                        }

                        //Comment icon and content
                        if (val.comment.length) {
                            comment = "<span class='glyphicon glyphicon-comment tt pointer' data-placement='left' data-toggle='tooltip' title='Comment: " + val.comment + "'></span>";
                        }
                        else {
                            comment = "<span class='glyphicon glyphicon-comment' style='opacity: 0.4; filter: alpha(opacity=40);'></span>";
                        }

                        //Duration and default hours icon and content
                        if (!val.hours_id && default_hours) {
                            time = "<a href='#' class='set-default-hour-btn'><span class='glyphicon glyphicon-time tt pointer red' data-placement='right' data-toggle='tooltip' title='Click to set the default hour " + duration_time + "'></span></a>";
                            input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
                        }
                        else if (duration) {
                        	time = "<span class='glyphicon glyphicon-time tt pointer green' data-placement='right' data-toggle='tooltip' title='Duration " + duration_time + "'></span>";
                            input_style = "";
	                    }
	                    else {
	                    	time = "<span class='glyphicon glyphicon-time' style='opacity: 0.4; filter: alpha(opacity=40);'></span>";
                            input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
	                    }

                        var edit_button = (val.hours_id)?"<button type='button' class='btn btn-default btn-xs edit-btn'>Edit</button>":"";

                        $tbody.append("<tr>" +
                        "<td class='hours_id hidden'>" + val.hours_id +
                        "<td class='date'>" + date +
                        "<td class='user_id hidden'>" + val.user_id +
                        "</td><td class='user_name'>" + val.user_name +
                        "</td><td>" +
	                        "<input type='text' style='"+input_style+"' size='4px' id='"+date.replace(/\//g, '-')+"_"+val.user_id+"_"+val.campaign_id+"' value='"+((duration)?Math.floor(duration/60):'')+"' onblur='hours.set_duration("+val.hours_id+", \""+date.replace(/\//g, '-')+"\", "+val.user_id+", "+val.campaign_id+", "+Math.floor(val.duration/60)+")' onfocus='hours.hide_default_btn($(this))' />" +
	                        "<span class='hidden duration'>" + duration + "</span>" + time +
                        "<td class='campaign_name'>" + val.campaign_name + "<span class='hidden campaign_id'>" + val.campaign_id + "</span>" +
                        "</td><td class='updated_name'>" + val.updated_name +
                        "</td><td class='updated_date'>" + val.updated_date +
                        "</td><td class='comment hidden'>" + val.comment +
                        "</td><td>" + comment + "</td>" +
                        "</td><td>"+edit_button+"</td>" +
                        "</tr>");
                        
                        $("#"+date.replace(/\//g, '-')+"_"+val.user_id+"_"+val.campaign_id).numeric();
                    });
                }
                $('.tt').tooltip();
            });
        });
    },
    //cancel the edit view
    cancel: function() {
    	//Hide edit form
        hours.hide_edit_form();
        //Load script table
        hours.load_hours();
    },
    //edit an hour
    edit: function($btn) {
    	var row = $btn.closest('tr');
    	var duration = row.find('.duration').text();
    	$('#edit_hours_form').find('input[name="hours_id"]').val(row.find('.hours_id').text());
        $('#edit_hours_form').find('input[name="user_id"]').val(row.find('.user_id').text());
        $('#edit_hours_form').find('input[name="duration"]').val((duration>0)?Math.floor(duration/60):0);
        $('#edit_hours_form').find('textarea[name="comment"]').val(row.find('.comment').text());
		$('#edit_hours_form').find('select[name="campaign_id"]').selectpicker('val',row.find('.campaign_id').text());
		$('#edit_hours_form').find('input[name="date"]').val(row.find('.date').text().replace(/\//g, '-'));

        $('#campaign_name').text(row.find('.campaign_name').text());
        $('#date').text(row.find('.date').text());
		
		$('#edit_hours_form').find('input[name="duration"]').numeric();

		$('.ajax-table').fadeOut(1000, function() {
            $('#edit_hours_form').fadeIn();
        });
        
        $('.filter-form').fadeOut(1000, function() {
            
        });
    },
    //save an hour
    save: function($btn) {
		$.ajax({
            url: helper.baseUrl + 'hour/save_hour',
            type: "POST",
            dataType: "JSON",
            data: $btn.closest('form').serialize()
        }).done(function(response) {
            if (response.success) {
                hours.load_hours();
                hours.hide_edit_form();
            	flashalert.success(response.message);
            }
            else {
            	flashalert.danger(response.message);
            }
            
        });
    },
    set_duration: function(hours_id, date, user_id, campaign_id, old_duration) {
        var duration = $("#"+date+"_"+user_id+"_"+campaign_id).val();

        if (duration && (duration != old_duration)) {
            if (duration != 0) {
                hours.save_duration(hours_id, date, user_id, campaign_id, duration, old_duration);
            }
            else {
                //Remove default hour
                hours.remove_duration(hours_id);
            }
        }
        else if(!duration.length && old_duration > 0) {
            //Remove default hour
            hours.remove_duration(hours_id);
        }
        else {
            hours.load_hours();
            flashalert.danger("You can not remove this hour because it is unset");
        }
    },
    set_default_hour: function($btn) {
        var row = $btn.closest('tr');
        var duration = row.find('.duration').text()/60;
        var date = row.find('.date').text().replace(/\//g, '-');
        var campaign_id = row.find('.campaign_id').text();
        var user_id = row.find('.user_id').text();

        hours.save_duration(null, date, user_id, campaign_id, duration);

    },
    save_duration: function(hours_id, date, user_id, campaign_id, duration) {
        var data = {'hours_id':hours_id, 'date':date, 'user_id':user_id, 'campaign_id':campaign_id, 'duration': duration};
        //Add or update default hour
        $.ajax({
            url: helper.baseUrl + 'hour/save_hour',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                hours.load_hours();
                hours.hide_edit_form();
                flashalert.success(response.message);
            }
            else {
                hours.load_hours();
                flashalert.danger(response.message);
            }

        });
    },
    remove_duration: function(hours_id) {
        var data = {'hours_id':hours_id};
        //Remove default hour
        $.ajax({
            url: helper.baseUrl + 'hour/remove_hour',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                hours.load_hours();
                flashalert.success(response.message);
            }
            else {
                flashalert.danger(response.message);
            }

        });
    },
    //Hide edit form
    hide_edit_form: function() {
        $('#edit_hours_form').fadeOut(1000, function() {
            $('.ajax-table').fadeIn();
        });
        $('.filter-form').fadeIn(1000, function() {
            
        });
    },
    //Hide default button
    hide_default_btn: function($btn) {
        var row = $btn.closest('tr');
        var default_btn = row.find('.set-default-hour-btn');
        $(default_btn).hide();
    }
}

var hours_settings = {
    //initalize the team specific buttons
    init: function() {
        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            hours_settings.load_default_hours();
        });
        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="agent"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            hours_settings.load_default_hours();
        });
        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="team"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            hours_settings.load_default_hours();
        });

        hours_settings.load_default_hours();
    },
    //this function reloads the hours_settings into the table body
    load_default_hours: function() {
        $.ajax({
            url: helper.baseUrl + 'hour/get_default_hours_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            $tbody = $('.default-hours-body');
            $tbody.empty();
            if (response.data.length) {
                $.each(response.data, function(i, val) {

                    var duration = (val.duration)?val.duration:'';

                    var hours = Math.floor(duration / 3600);
                    var minutes = Math.floor((duration - (hours * 3600)) / 60);
                    var seconds = duration - (hours * 3600) - (minutes * 60);

                    if (hours < 10) {
                        hours = "0" + hours;
                    }
                    if (minutes < 10) {
                        minutes = "0" + minutes;
                    }
                    if (seconds < 10) {
                        seconds = "0" + seconds;
                    }

                    var duration_time = (duration)?(hours + 'h ' + minutes + 'm '):'';

                    $tbody.append("<tr>" +
                    "<td class='hours_id hidden'>" + val.default_hours_id +
                    "<td class='user_id hidden'>" + val.user_id +
                    "</td><td class='user_name'>" + val.user_name +
                    "<td class='campaign_name'>" + val.campaign_name + "<span class='hidden campaign_id'>" + val.campaign_id + "</span>" +
                    "</td><td><input type='text' size='4px' id='"+val.user_id+"_"+val.campaign_id+"' value='"+((duration)?Math.floor(duration/60):'')+"' onblur='hours_settings.set_duration("+val.default_hours_id+", "+val.user_id+", "+val.campaign_id+", "+Math.floor(val.duration/60)+")' />" +
                    "</td><td><span class='hidden duration'>" + duration + "</span>" + duration_time +
                    "</td>" +
                    "</tr>");

                    $("#"+val.user_id+"_"+val.campaign_id).numeric();
                });
            }
            $('.tt').tooltip();
        });
    },
    set_duration: function(default_hours_id, user_id, campaign_id, old_duration) {
        var duration = $("#"+user_id+"_"+campaign_id).val();

        if (duration && (duration != old_duration)) {
            if (duration != 0) {
                hours_settings.save_duration(default_hours_id, user_id, campaign_id, duration, old_duration);
            }
            else {
                //Remove default hour
                hours_settings.remove_duration(default_hours_id);
            }
        }
        else if(!duration.length && old_duration > 0) {
            //Remove default hour
            hours_settings.remove_duration(default_hours_id);
        }
    },
    save_duration: function(default_hours_id, user_id, campaign_id, duration) {
        var data = {'default_hours_id':default_hours_id, 'user_id':user_id, 'campaign_id':campaign_id, 'duration': duration};
        //Add or update default hour
        $.ajax({
            url: helper.baseUrl + 'hour/save_default_hour',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                flashalert.success(response.message);
            }
            else {
                flashalert.danger(response.message);
            }
            hours_settings.load_default_hours();

        });
    },
    remove_duration: function(default_hours_id) {
        var data = {'default_hours_id':default_hours_id};
        //Remove default hour
        $.ajax({
            url: helper.baseUrl + 'hour/remove_default_hour',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                hours_settings.load_default_hours();
                flashalert.success(response.message);
            }
            else {
                flashalert.danger(response.message);
            }

        });
    }
}