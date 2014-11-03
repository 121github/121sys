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
            url: helper.baseUrl + 'admin/get_hours_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            $tbody = $('.hours-body');
            $tbody.empty();
            $.each(response.data, function(date, hours) {
                if (hours.length) {
                    $.each(hours, function(i, val) {
                        var hours = Math.floor(val.duration / 3600);
                        var minutes = Math.floor((val.duration - (hours * 3600)) / 60);
                        var seconds = val.duration - (hours * 3600) - (minutes * 60);

                        if (hours < 10) {
                            hours = "0" + hours;
                        }
                        if (minutes < 10) {
                            minutes = "0" + minutes;
                        }
                        if (seconds < 10) {
                            seconds = "0" + seconds;
                        }

                        var duration = hours + ' h ' + minutes + ' m ';

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

                        var time_logged = hours + ' h ' + minutes + ' m ';

                        if (val.comment.length) {
                            comment = "<a href='#'><span class='glyphicon glyphicon-comment tt pointer' data-placement='left' data-toggle='tooltip' title='" + val.comment + "'></span></a>";
                        }
                        else {
                            comment = "<span class='glyphicon glyphicon-comment' style='opacity: 0.4; filter: alpha(opacity=40);'></span>";
                        }

                        $tbody.append("<tr>" +
                        "<td class='hours_id hidden'>" + val.hours_id +
                        "<td class='date'>" + date +
                        "</td><td class='user_name'>" + val.user_name +
                        "</td><td><span class='hidden duration'>" + val.duration + "</span>" + duration +
                        "</td><td>" + time_logged +
                        "<td class='campaign_name'><span class='hidden campaign_id'>" + val.campaign_id + "</span>" + val.campaign_name +
                        "</td><td class='updated_name'>" + val.updated_name +
                        "</td><td class='updated_date'>" + val.updated_date +
                        "</td><td class='comment hidden'>" + val.comment +
                        "</td><td>" + comment + "</td>" +
                        "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button></td>" +
                        "</tr>");
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
    	$('#edit_hours_form').find('input[name="hours_id"]').val(row.find('.hours_id').text());
        $('#edit_hours_form').find('input[name="duration"]').val(row.find('.duration').text());
        $('#edit_hours_form').find('textarea[name="comment"]').val(row.find('.comment').text());
		$('#edit_hours_form').find('select[name="campaign_id"]').selectpicker('val',row.find('.campaign_id').text());
		$('#edit_hours_form').find('input[name="date"]').val(row.find('.date').text());
		
        $('.ajax-table').fadeOut(1000, function() {
            $('#edit_hours_form').fadeIn();
        });
        
        $('.filter-form').fadeOut(1000, function() {
            
        });
    },
    //save a team
    save: function($btn) {
    	if (parseInt($('#edit_hours_form').find('input[name="duration"]').val()) - parseInt($('#edit_hours_form').find('input[name="exception"]').val()) <= 0) {
    		flashalert.danger("ERROR: hour NOT saved. The (duration - exception) can not be < 0");
    	}
    	else {
    		$.ajax({
                url: helper.baseUrl + 'admin/save_hour',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                hours.load_hours();
                hours.hide_edit_form();
                if (response) {
                	flashalert.success("hour saved");
                }
                else {
                	flashalert.success("ERROR: hour NOT saved");
                }
                
            });
    	}
    },
    //Hide edit form
    hide_edit_form: function() {
        $('#edit_hours_form').fadeOut(1000, function() {
            $('.ajax-table').fadeIn();
        });
        $('.filter-form').fadeIn(1000, function() {
            
        });
    }
}