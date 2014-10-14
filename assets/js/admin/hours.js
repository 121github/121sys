var hours = {
    //initalize the team specific buttons 
    init: function() {
    	$('.daterange').daterangepicker({
            opens: "left",
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            format: 'DD/MM/YYYY',
            minDate: "02/07/2014",
            maxDate: moment(),
            startDate: moment().subtract('days', 6),
            endDate: moment()
        },
        function(start, end, element) {
        	var $btn = this.element;
            $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
            $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
            $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
            hours.load_hours()
        });
	    $(document).on("click", '.daterange', function(e) {
	        e.preventDefault();
	    });
	
	    $(document).on("click", ".campaign-filter", function(e) {
	        e.preventDefault();
	        $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
	        $(this).closest('ul').find('a').css("color","black");
	        $(this).css("color","green");
	        hours.load_hours()
	    });
	
	    $(document).on("click", ".agent-filter", function(e) {
	        e.preventDefault();
	        $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
	        $(this).closest('ul').find('a').css("color","black");
	        $(this).css("color","green");
	        hours.load_hours()
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
        	$('form').find('input[name="date_from"]').val(moment().subtract('days', 6).format('YYYY-MM-DD'));
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
            $tbody = $('.hours-panel').find('tbody');
            $tbody.empty();
            $.each(response.data, function(i, val) {
                if (response.data.length) {
                	var global_duration = ((val.duration) - (val.exception*60));
                	if (global_duration   < 0) {
                		 var duration    = 'ERROR: duration < 0';
                	}
                	else {
                		var hours   = Math.floor(global_duration / 3600);
                        var minutes = Math.floor((global_duration - (hours * 3600)) / 60);
                        var seconds = global_duration - (hours * 3600) - (minutes * 60);

                        if (hours   < 10) {hours   = "0"+hours;}
                        if (minutes < 10) {minutes = "0"+minutes;}
                        if (seconds < 10) {seconds = "0"+seconds;}
                		
                        var duration    = hours+' h '+minutes+' m '+seconds+' s';
                	}
                	
                	if (val.comment.length) {
                		comment = "<a href='#'><span class='glyphicon glyphicon-comment tt pointer' data-placement='left' data-toggle='tooltip' title='"+val.comment+"'></span></a>"
                	}
                	else {
                		comment = "<span class='glyphicon glyphicon-comment' style='opacity: 0.4; filter: alpha(opacity=40);'></span>"
                	}
                    
                    $tbody.append("<tr>" +
                    			"<td class='hours_id hidden'>" + val.hours_id +
	                    		"<td class='date'>" + val.date + 
	                    		"</td><td class='user_name'>" + val.user_name + 
	                    		"<td class='global_duration'><span class='hidden duration'>"+Math.floor(val.duration/60)+"</span><span class='hidden exception'>"+Math.floor(val.exception)+"</span>" + duration +
	                    		"<td class='campaign_name'><span class='hidden campaign_id'>"+val.campaign_id+"</span>" + val.campaign_name +
	                    		"</td><td class='updated_name'>"+val.updated_name+
	                    		"</td><td class='updated_date'>"+val.updated_date+
	                    		"</td><td class='comment hidden'>" + val.comment +
	                    		"</td><td>"+comment+"</td>" +
	                    		"</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button></td>" +
                    		"</tr>");
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
        $('#edit_hours_form').find('input[name="exception"]').val(row.find('.exception').text());
        $('#edit_hours_form').find('textarea[name="comment"]').val(row.find('.comment').text());
		$('#edit_hours_form').find('select[name="campaign_id"]').selectpicker('val',row.find('.campaign_id').text());
		$('#edit_hours_form').find('input[name="date"]').data('DateTimePicker').setDate(row.find('.date').text());
		
        $('.ajax-table').fadeOut(1000, function() {
            $('#edit_hours_form').fadeIn();
        });
    },
    //save a team
    save: function($btn) {
    	var row = $btn.closest('tr');
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
    }
}