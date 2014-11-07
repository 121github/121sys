var time = {
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
            time.load_time();
        });
	    $(document).on("click", '.daterange', function(e) {
	        e.preventDefault();
	    });
	

        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
             $(this).closest('form').find('input[name="agent"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            time.load_time();
         });
         $(document).on("click", ".agent-filter", function(e) {
                e.preventDefault();
                $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
                $(this).closest('form').find('input[name="team"]').val('');
                $(this).closest('ul').find('a').css("color","black");
                $(this).css("color","green");
                time.load_time();
	    });
	    
        $(document).on('click', '.save-btn', function(e) {
            e.preventDefault();
            time.save($(this));
        });
        $(document).on('click', '.set-default-time-btn', function(e) {
            e.preventDefault();
            $(this).hide();
            time.set_default_time($(this));
        });
        $(document).on('click', '.edit-btn', function() {
            time.edit($(this));
        });
        $(document).on('click', '.close-btn', function(e) {
        	e.preventDefault();
        	time.cancel();
        });
        
        $(document).on('click', '.add-exception-btn', function(e) {
        	e.preventDefault();
        	time.add_exception($(this));
        });
        $(document).on('click', '.remove-exception-btn', function(e) {
        	e.preventDefault();
        	time.remove_exception($(this));
        });
        
        //start the function to load the time into the table
        if ($('form').find('input[name="date_from"]').val() == '') {
        	$('form').find('input[name="date_from"]').val(moment().format('YYYY-MM-DD'));
        }
        if ($('form').find('input[name="date_to"]').val() == '') {
        	$('form').find('input[name="date_to"]').val(moment().format('YYYY-MM-DD'));
        }
        time.load_time();
    },
    //this function reloads the time into the table body
    load_time: function() {
        $.ajax({
            url: helper.baseUrl + 'time/get_time_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            $tbody = $('.time-body');
            $tbody.empty();
            $.each(response.data, function(date, time) {
                if (time.length) {
                    $.each(time, function(i, val) {

                        //Default Start Time
                        var default_start_time = (val.default_start_time)?val.default_start_time:'';

                        //Start Time if exists
                    	var start_time = (val.start_time)?val.start_time:(default_start_time)?default_start_time:'';

                        //Duration and default time icon and content
                        if (!val.time_id && default_start_time) {
                            start_time_btn = "<span class='input-group-addon tt' data-placement='right' data-toggle='tooltip' title='Click to set the default hour'><span class='glyphicon glyphicon-time pointer red' onclick='time.set_datetimepicker($(this))'></span></span>";
                            start_tipe_input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
                        }
                        else if (start_time) {
                            start_time_btn = "<span class='input-group-addon'><span class='glyphicon glyphicon-time' onclick='time.set_datetimepicker($(this))'></span></span>";
                            start_tipe_input_style = "";
                        }
                        else {
                            start_time_btn = "<span class='input-group-addon'><span class='glyphicon glyphicon-time' style='opacity: 0.4; filter: alpha(opacity=40);' onclick='time.set_datetimepicker($(this))'></span></span>";
                            start_tipe_input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
                        }

                        //Default End Time
                        var default_end_time = (val.default_end_time)?val.default_end_time:'';

                        //End Time if exists
                        var end_time = (val.end_time)?val.end_time:(default_end_time)?default_end_time:'';

                        //Duration and default time icon and content
                        if (!val.time_id && default_end_time) {
                            end_time_btn = "<span class='input-group-addon tt' data-placement='right' data-toggle='tooltip' title='Click to set the default hour'><span class='glyphicon glyphicon-time pointer red' onclick='time.set_datetimepicker($(this))'></span></span>";
                            end_tipe_input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
                        }
                        else if (end_time) {
                            end_time_btn = "<span class='input-group-addon'><span class='glyphicon glyphicon-time' onclick='time.set_datetimepicker($(this))'></span></span>";
                            end_tipe_input_style = "";
                        }
                        else {
                            end_time_btn = "<span class='input-group-addon'><span class='glyphicon glyphicon-time' style='opacity: 0.4; filter: alpha(opacity=40);' onclick='time.set_datetimepicker($(this))'></span></span>";
                            end_tipe_input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
                        }

                        //Exception time if exists
                        var time = Math.floor(val.exceptions / 60);
                        var minutes = Math.floor((val.exceptions - (time * 60)));
                        var seconds = val.exceptions - (time * 60) - (minutes);

                        if (time < 10) {
                            time = "0" + time;
                        }
                        if (minutes < 10) {
                            minutes = "0" + minutes;
                        }
                        if (seconds < 10) {
                            seconds = "0" + seconds;
                        }

                        var exception_time = time + 'h ' + minutes + 'm ';

                        //Comment icon and content
                        if (val.comment.length) {
                            comment = "<span class='glyphicon glyphicon-comment tt pointer' data-placement='left' data-toggle='tooltip' title='Comment: " + val.comment + "'></span>";
                        }
                        else {
                            comment = "<span class='glyphicon glyphicon-comment' style='opacity: 0.4; filter: alpha(opacity=40);'></span>";
                        }

                        //Exception time icon and content
                        if(val.exceptions) {
                            exceptions_time = "<span class='glyphicon glyphicon-eye-close tt pointer' data-placement='left' data-toggle='tooltip' title='Exceptions time: " + exception_time + "'></span>";
                        }
                        else {
                            exceptions_time = "<span class='glyphicon glyphicon-eye-close' style='opacity: 0.4; filter: alpha(opacity=40);'></span>";
                        }

                        var edit_button = (val.time_id)?"<button type='button' class='btn btn-default btn-xs edit-btn'>Edit</button>":"";

                        $tbody.append("<tr>" +
                        "<td class='time_id hidden'>" + val.time_id +
                        "<td class='date'>" + date +
                        "<td class='user_id hidden'>" + val.user_id +
                        "</td><td class='user_name'>" + val.user_name +
                        "</td><td class='input-group date datetimepicker' style='width: 9em;'>" +
                            "<input style='"+start_tipe_input_style+"' name='start_time' id='start_time_"+date.replace(/\//g, '-')+"_"+val.user_id+"' data-date-format='HH:mm' type='text' class='form-control' value='"+start_time.substr(0,5)+"' />" +
                            start_time_btn +
                        "</td><td>" +
                        "</td><td class='input-group date datetimepicker' style='width: 9em;'>" +
                            "<input style='"+end_tipe_input_style+"' name='end_time' id='end_time_"+date.replace(/\//g, '-')+"_"+val.user_id+"' data-date-format='HH:mm' type='text' class='form-control' value='"+end_time.substr(0,5)+"' />" +
                            end_time_btn +
                        "</td><td class='updated_name'>" + val.updated_name +
                        "</td><td class='updated_date'>" + val.updated_date +
                        "</td><td class='comment hidden'>" + val.comment +
                        "</td><td>" + exceptions_time + "</td>" +
                        "</td><td>" + comment + "</td>" +
                        "</td><td>"+edit_button+"</td>" +
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
        time.hide_edit_form();
        //Load script table
        time.load_time();
    },
    //edit an time
    edit: function($btn) {
    	var row = $btn.closest('tr');
    	var duration = row.find('.duration').text();
    	$('#edit_time_form').find('input[name="time_id"]').val(row.find('.time_id').text());
        $('#edit_time_form').find('input[name="user_id"]').val(row.find('.user_id').text());
        $('#edit_time_form').find('input[name="start_time"]').val(row.find('input[name="start_time"]').val());
        $('#edit_time_form').find('input[name="end_time"]').val(row.find('input[name="end_time"]').val());
        $('#edit_time_form').find('textarea[name="comment"]').val(row.find('.comment').text());
		$('#edit_time_form').find('input[name="date"]').val(row.find('.date').text().replace(/\//g, '-'));

        $('#date').text(row.find('.date').text());
		
		$('#edit_time_form').find('input[name="duration"]').numeric();
		$('#edit_time_form').find('input[name="exception-duration"]').numeric();

		time.load_exceptions();
		
        $('.ajax-table').fadeOut(1000, function() {
            $('#edit_time_form').fadeIn();
        });
        
        $('.filter-form').fadeOut(1000, function() {
            
        });
    },
    //add exception
    add_exception: function($btn) {
    	var exception_type_id = $("#exception-select option:selected").val();
    	var exception_name = $("#exception-select option:selected").text();
    	var exception_duration = $('#edit_time_form').find('input[name="exception-duration"]').val();
    	var time_id = $('#edit_time_form').find('input[name="time_id"]').val();
    	
    	if (time_id > 0) {
	    	if ((exception_type_id != 0) && (exception_duration > 0)) {
	    		$.ajax({
	                url: helper.baseUrl + 'time/add_time_exception',
	                type: "POST",
	                dataType: "JSON",
	                data: {'exception_type_id':exception_type_id, 'time_id' : time_id, 'duration' : exception_duration}
	            }).done(function(response) {
	                if (response.success) {
	                	var exception = {'exception_id':response.exception_id, 'exception_type_id':exception_type_id, 'exception_name' : exception_name, 'duration' : exception_duration}
	                	time.append_exception(exception);
	                	$('#edit_time_form').find('input[name="exception-duration"]').val("");
	        			$('#edit_time_form').find('select[name="exception_id"]').selectpicker('val',0);
	        			
	                	flashalert.success("Exception added");
	                }
	                else {
	                	flashalert.danger("ERROR: Exception NOT added");
	                }
	                
	            });
	    	}
    	}
    	else {
    		flashalert.danger("You need to set the duration before");
    	}
    },
    //append an exception
    append_exception: function(exception){
    	var $tbody = "<tr>" +
        "<td><span class='hidden exception_id'>" +exception.exception_id + "</span>" + exception.exception_name +
        "<td>" + exception.duration +
        "</td><td><button class='btn btn-danger btn-xs remove-exception-btn'>Remove</button></td>" +
        "</tr>";

		$('.exceptions-body').append($tbody);
    },
    //remove exception from the list
    remove_exception: function($btn) {
    	var row = $btn.closest('tr');
    	var exception_id = row.find('.exception_id').text();
    	
    	if (exception_id != 0) {
    		$.ajax({
                url: helper.baseUrl + 'time/remove_time_exception',
                type: "POST",
                dataType: "JSON",
                data: {'exception_id':exception_id, 'time_id' : $('#edit_time_form').find('input[name="time_id"]').val()}
            }).done(function(response) {
                if (response.success) {
                	row.remove();
                	flashalert.success("Exception removed");
                }
                else {
                	flashalert.danger("ERROR: Exception NOT removed");
                }
                
            });
    	}
    },
    //load exceptions for this time
    load_exceptions: function() {
    	var $tbody = $('.exceptions-body');
    	$tbody.empty();
    	
    	$.ajax({
            url: helper.baseUrl + 'time/get_time_exception',
            type: "POST",
            dataType: "JSON",
            data: {'time_id' : $('#edit_time_form').find('input[name="time_id"]').val()}
        }).done(function(response) {
            if (response) {
            	$.each(response.data, function(key,val) {
            		time.append_exception(val);
            	});
            }
            else {
            	flashalert.success("ERROR: Error loading the exceptions for this time");
            }
        });
    },
    //save an time
    save: function($btn) {
		$.ajax({
            url: helper.baseUrl + 'time/save_time',
            type: "POST",
            dataType: "JSON",
            data: $btn.closest('form').serialize()
        }).done(function(response) {
            if (response.success) {
                time.load_time();
                time.hide_edit_form();
            	flashalert.success(response.message);
            }
            else {
            	flashalert.danger(response.message);
            }
            
        });
    },
    save_time: function(time_id, date, user_id, start_time, end_time) {
        var data = {'time_id':time_id, 'date':date, 'user_id':user_id, 'start_time': start_time, 'end_time': end_time};
        //Add or update default time
        $.ajax({
            url: helper.baseUrl + 'time/save_time',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                time.load_time();
                time.hide_edit_form();
                flashalert.success(response.message);
            }
            else {
                flashalert.danger(response.message);
            }

        });
    },
    remove_duration: function(time_id) {
        var data = {'time_id':time_id};
        //Remove default time
        $.ajax({
            url: helper.baseUrl + 'time/remove_time',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                time.load_time();
                flashalert.success(response.message);
            }
            else {
                flashalert.danger(response.message);
            }

        });
    },
    //Hide edit form
    hide_edit_form: function() {
        $('#edit_time_form').fadeOut(1000, function() {
            $('.ajax-table').fadeIn();
        });
        $('.filter-form').fadeIn(1000, function() {
            
        });
    },
    //Hide default button
    hide_default_btn: function($btn) {
        var row = $btn.closest('tr');
        var default_btn = row.find('.set-default-time-btn');
        $(default_btn).hide();
    },

    //Set datetimepicker
    set_datetimepicker: function($btn) {
        var row = $btn.closest('tr');
        var datetimepicker = row.find('.datetimepicker');

        datetimepicker.datetimepicker({
            pickDate: false
        });

        datetimepicker.on("dp.hide",function (e) {
            start_time = (row.find('input[name="start_time"]').val())?row.find('input[name="start_time"]').val()+':00':'';
            end_time = (row.find('input[name="end_time"]').val())?row.find('input[name="end_time"]').val()+':00':'';
            user_id = row.find('.user_id').text();
            time_id = (row.find('.time_id').text() != 'null')?row.find('.time_id').text():'';
            date = row.find('.date').text().replace(/\//g, '-');

            time.save_time(time_id, date, user_id, start_time, end_time);

        });
    }
}

var default_time = {
    //initalize the team specific buttons
    init: function() {
        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            default_time.load_default_time();
        });
        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="agent"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            default_time.load_default_time();
        });
        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="team"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            default_time.load_default_time();
        });

        default_time.load_default_time();
    },
    //this function reloads the default_time into the table body
    load_default_time: function() {
        $.ajax({
            url: helper.baseUrl + 'time/get_default_time_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            $tbody = $('.default-time-body');
            $tbody.empty();
            if (response.data.length) {
                $.each(response.data, function(i, val) {

                    //Start Time if exists
                    var start_time = (val.start_time)?val.start_time:'';

                    //Duration and default start time icon and content
                    if (start_time) {
                        start_time_btn = "<span class='input-group-addon'><span id='time_btn' class='glyphicon glyphicon-time' onclick='default_time.set_datetimepicker($(this))'></span></span>";
                        start_time_input_style = "";
                    }
                    else {
                        start_time_btn = "<span class='input-group-addon'><span id='time_btn' class='glyphicon glyphicon-time' style='opacity: 0.4; filter: alpha(opacity=40);' onclick='default_time.set_datetimepicker($(this))'></span></span>";
                        start_time_input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
                    }

                    //End Time if exists
                    var end_time = (val.end_time)?val.end_time:'';

                    //Duration and default end time icon and content
                    if (end_time) {
                        end_time_btn = "<span class='input-group-addon'><span id='time_btn' class='glyphicon glyphicon-time' onclick='default_time.set_datetimepicker($(this))'></span></span>";
                        end_time_input_style = "";
                    }
                    else {
                        end_time_btn = "<span class='input-group-addon'><span id='time_btn' class='glyphicon glyphicon-time' style='opacity: 0.4; filter: alpha(opacity=40);' onclick='default_time.set_datetimepicker($(this))'></span></span>";
                        end_time_input_style = "opacity: 0.6;filter: alpha(opacity=60); background: rgb(197, 191, 191);";
                    }

                    remove_btn = (val.default_time_id)?"<span class='glyphicon glyphicon-remove red' onclick='default_time.remove_default_time($(this))'></span>":"";

                    $tbody.append("<tr>" +
                    "<td class='default_time_id hidden'>" + val.default_time_id +
                    "<td class='user_id hidden'>" + val.user_id +
                    "</td><td class='user_name'>" + val.user_name +
                    "</td><td class='input-group date datetimepicker' style='width: 9em;'>" +
                        "<input style='"+start_time_input_style+"' name='start_time' data-date-format='HH:mm' type='text' class='form-control' value='"+start_time.substr(0,5)+"' />" +
                        start_time_btn +
                    "</td><td>" +
                    "</td><td class='input-group date datetimepicker' style='width: 9em;'>" +
                        "<input style='"+end_time_input_style+"' name='end_time' data-date-format='HH:mm' type='text' class='form-control' value='"+end_time.substr(0,5)+"' />" +
                        end_time_btn +
                    "</td><td>" +
                        remove_btn +
                    "</td>" +
                    "</tr>");

                    $("#"+val.user_id+"_").numeric();
                });
            }
            $('.tt').tooltip();
        });
    },

    save_default_time: function(default_time_id, user_id, start_time, end_time) {
        var data = {'default_time_id':default_time_id, 'user_id':user_id, 'start_time': start_time, 'end_time': end_time};

        //Add or update default time
        $.ajax({
            url: helper.baseUrl + 'time/save_default_time',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                default_time.load_default_time();
                flashalert.success(response.message);
            }
            else {
                flashalert.danger(response.message);
            }

        });
    },
    remove_default_time: function($btn) {
        var row = $btn.closest('tr');
        var default_time_id = row.find('.default_time_id');

        var data = {'default_time_id':default_time_id};
        //Remove default time
        $.ajax({
            url: helper.baseUrl + 'time/remove_default_time',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (response.success) {
                default_time.load_default_time();
                flashalert.success(response.message);
            }
            else {
                flashalert.danger(response.message);
            }

        });
    },
    //Set datetimepicker
    set_datetimepicker: function($btn) {
        var row = $btn.closest('tr');
        var datetimepicker = row.find('.datetimepicker');

        datetimepicker.datetimepicker({
            pickDate: false
        });

        datetimepicker.on("dp.hide",function (e) {
            start_time = (row.find('input[name="start_time"]').val())?row.find('input[name="start_time"]').val()+':00':'';
            end_time = (row.find('input[name="end_time"]').val())?row.find('input[name="end_time"]').val()+':00':'';
            user_id = row.find('.user_id').text();
            default_time_id = (row.find('.default_time_id').text() != 'null')?row.find('.default_time_id').text():'';

            default_time.save_default_time(default_time_id, user_id, start_time, end_time);

        });


    }
}