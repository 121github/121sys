// JavaScript Document
var dashboard = {
    init: function () {	
		$(".filter").click(function(e) {
            e.preventDefault();
			var input = $(this).attr('data-ref');
			var func = $(this).closest('form').attr('data-func');
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="'+input+'"]').val($(this).attr('id'));
			//run the panel function specified in the data-func
			eval("dashboard."+func+"()");
        });
    },
	/* the function for the history panel on the main dashboard */
    history_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_history',
            type: "POST",
            dataType: "JSON",
            data: $('.history-filter').serialize(),
        }).done(function (response) {
            $tbody = $('.call-history').find('tbody');
            $tbody.empty();
            $.each(response.data, function (i, val) {
                if (response.data.length) {
                    $tbody.append("<tr><td>" + val.campaign_name + "</td><td>" + val.date + "</td><td>" + val.time + "</td><td>" + val.name + "</td><td>" + val.outcome + "</td><td><a href='" + helper.baseUrl + 'records/detail/' + val.urn + "'><span class='glyphicon glyphicon-play'></span></a></td></tr>");
                }
            });
        });
    },
	    emails_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_email_stats',
            type: "POST",
            dataType: "JSON",
            data: $('.emails-filter').serialize(),
        }).done(function (response) {
            $('.email-stats').html("<ul><li><a href='"+response.data.new_url+"'>"+response.data.new+"</a> new emails read</li><li><a href='"+response.data.read_url+"'>"+response.data.read+"</a> emails read today "+"</li><li><a href='"+response.data.all_url+"'>"+response.data.all+"</a> emails sent today</li><li><a href='"+response.data.unsent_url+"'>"+response.data.unsent+"</a> failed emails today</li><li><a href='"+response.data.sentall_url+"'>"+response.data.sentall+"</a> emails sent anytime</li><li><a href='"+response.data.readall_url+"'>"+response.data.readall+"</a> emails read anytime</li></ul>");
        });
    },
	/* the function for the outcomes panel on the main dashboard */
    outcomes_panel: function (campaign_id) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_outcomes',
            type: "POST",
            dataType: "JSON",
           data: $('.outcomes-filter').serialize(),
        }).done(function (response) {
            $('.outcome-stats').empty();
            var $outcomes = "";
			if(response.data.length>0){
            $.each(response.data, function (i, val) {
                $outcomes += '<a href="'+helper.baseUrl+'search/custom/history/outcome/'+val.outcome+'/contact-from/'+response.date+'" class="list-group-item"><i class="fa fa-comment fa-fw"></i>' + val.outcome + '<span class="pull-right text-muted small"><em>' + val.count + '</em></span></a>';
            });
            $('.outcome-stats').append('<div class="list-group">' + $outcomes + '</div>');
			} else {
			$('.outcome-stats').append('<div class="list-group">No calls made today</div>');	
			}
        });
    },
		/* the function for the stats panel on the main dashboard */
    system_stats: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/system_stats',
            type: "POST",
            dataType: "JSON",
           data: $('.stats-filter').serialize(),
        }).done(function (response) {
            $('.timeline').empty();
            $timeline = '<li class="timeline-inverted"><div class="timeline-panel"><div class="timeline-heading"><h4 class="timeline-title">Campaign Stats</h4></div><div class="timeline-body"><p><a href="'+response.data.virgin_url+'">' + response.data.virgin + '</a> records have yet to be called.<br><a href="'+response.data.active_url+'">' + response.data.active + '</a> records are in progress<br><a href="'+response.data.parked_url+'">' + response.data.parked + '</a> records have been parked<br><a href="'+response.data.dead_url+'">' + response.data.dead + '</a> records are dead</p></div></div></li>';
			
			$timeline += '<li class="timeline-inverted"><div class="timeline-panel"><div class="timeline-heading"><h4 class="timeline-title">Follow up Stats</h4></div><div class="timeline-body"><p><a href="'+response.data.pending_url+'">' + response.data.pending + '</a> records are pending.<br><a href="'+response.data.in_progress_url+'">' + response.data.in_progress + '</a> records are in progress<br><a href="'+response.data.completed_url+'">' + response.data.completed + '</a> records have been completed</div></div></li>';
			if(response.data.surveys>0){
			$timeline += '<li class="timeline-inverted"><div class="timeline-panel"><div class="timeline-heading"><h4 class="timeline-title">Survey Stats</h4></div><div class="timeline-body"><p>' + response.data.surveys + ' surveys have been compeleted<br>' + response.data.failures + ' surveys scored less than 6 on the NPS question<br>' + response.data.average + ' is the average NPS score</p></div></div></li>';
			}
            $('.timeline').append($timeline);
        });
    },
	/* the function for the comments panel on the main dashboard */
    comments_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_comments',
            type: "POST",
            dataType: "JSON",
           data: $('.comments-filter').serialize(),
        }).done(function (response) {
            $('.chat').empty();
            $.each(response.data, function (i, val) {
                $('.chat').append('<li class="left clearfix"><div class="chat-body clearfix"><div class="header"><strong class="primary-font"><a href="'+helper.baseUrl+'records/detail/' + val.urn + '">' + val.name + '</a></strong><small class="pull-right text-muted"><i class="fa fa-clock-o fa-fw"></i>' + val.date + '</small></div><p>' + val.comment + '</p></div></li>');
            });
        });
    },
	/* the function for the urgent panel on the client dashboard */
    urgent_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_urgent',
            type: "POST",
            dataType: "JSON",
            data: $('.urgent-filter').serialize(),
        }).done(function (response) {
            $('.urgent-panel').empty();
            var $urgents = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $urgents += '<li><a class="red tt pointer" data-toggle="tooltip" data-placement="left" title="'+val.last_comment+'"" href="'+helper.baseUrl+'records/detail/' + val.urn + '">' + val.fullname + '</a><br><span class="small">Last Updated on ' + val.date_updated + '</span></li>';
                });
                $('.urgent-panel').append('<ul>' + $urgents + '</ul>');
				$('.tt').tooltip();
            } else {
                $('.urgent-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
	
		/* the function for the urgent panel on the client dashboard */
    pending_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_pending',
            type: "POST",
            dataType: "JSON",
           data: $('.pending-filter').serialize(),
        }).done(function (response) {
            $('.pending-panel').empty();
            var $pending = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $pending += '<li><a class="tt pointer" data-toggle="tooltip" data-placement="left" title="'+val.last_comment+'"" href="'+helper.baseUrl+'records/detail/' + val.urn + '">' + val.fullname + '</a><br><span class="small">Last Updated on ' + val.date_updated + '</span></li>';
                });
                $('.pending-panel').append('<ul>' + $pending + '</ul>');
				$('.tt').tooltip();
            } else {
                $('.pending-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
		/* the function for the urgent panel on the client dashboard */
    appointments_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_appointments',
            type: "POST",
            dataType: "JSON",
            data: $('.appointments-filter').serialize(),
        }).done(function (response) {
            $('.appointments-panel').empty();
            var $appointments = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $appointments += '<li><a class="tt pointer" data-toggle="tooltip" data-placement="left" title="'+val.last_comment+'"" href="'+helper.baseUrl+'records/detail/' + val.urn + '">' + val.fullname + '</a><br><span class="small">Start time: ' + val.date_updated + '</span></li>';
                });
                $('.appointments-panel').append('<ul>' + $appointments + '</ul>');
				$('.tt').tooltip();
            } else {
                $('.appointments-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
	/* the function for the outcomes panel on the agent/client dashboard */
    favorites_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_favorites',
            type: "POST",
            dataType: "JSON",
            data: $('.favorites-filter').serialize(),
        }).done(function (response) {
            $('.favorites-panel').empty();
			var $table="";
			    var $tbody = "";
            if (response.data.length > 0) {
				$table = "<table class='table'><thead><tr><th>Campaign</th><th>Name</th><th>Last Update</th><th>Nextcall</th><th>Outcome</th><th>View</th></tr></tr></thead><tbody>";
                $.each(response.data, function (i, val) {
					        
                    $tbody += '<tr><td>'+val.campaign_name+'</td><td>'+val.fullname+'</td><td>'+val.date_updated+'</td><td>'+val.nextcall+'</td><td>'+val.outcome+'</td><td><span class="glyphicon glyphicon-comment tt pointer" title="" data-toggle="tooltip" data-placement="left" data-original-title="'+val.comments+'"></span></td><td><a href="'+helper.baseUrl+'records/detail/' + val.urn + '"><span class="glyphicon glyphicon-play"></span></a></td></tr>';
					
                });
				$table += $tbody;
               $table += "</tbody></table>";
			      $('.favorites-panel').append($table);
            } else {
                $('.favorites-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
	/* the function for the missed call backs panel on the agent dashboard */
    callbacks_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/all_callbacks',
            type: "POST",
            dataType: "JSON",
            data: $('.callbacks-filter').serialize(),
			beforeSend: function(){
			            $('.timely-callbacks').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
            $('.timely-callbacks').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    if (val.last_comments.length) {
                        comments = '<span class="glyphicon glyphicon-comment tt pointer" data-placement="left" data-toggle="tooltip" title="' + val.last_comments + '"></span>';
                    }
                    else {
                        comments = '<span class="glyphicon glyphicon-comment" style="opacity: 0.4; filter: alpha(opacity=40);"></span>';
                    }
                    $row += '<tr><td>' + val.contact + '</td><td>' + val.name + '</td><td>' + val.campaign + '</td><td>' + val.date + '</td><td>' + val.time + '</td><td><a href="'+helper.baseUrl+'records/detail/' + val.urn + '"><span class="glyphicon glyphicon-play"></span></a></td><td>'+comments+'</td></tf></tr>';
                });
                $('.callbacks').append('<table class="table table-striped table-responsive"><thead><th>Contact</th><th>User</th><th>Campaign</th><th>Date</th><th>Time</th><th>View</th><th></th></thead><tbody>' + $row + '</tbody></table>');
                $('.tt').tooltip();
            } else {
                $('.callbacks').append('<p>' + response.msg + '</p>');
            }
        });
    },
	/* the function for the progress panel on the client dashboard */
    progress_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/client_progress',
            type: "POST",
            dataType: "JSON",
			data: $('.progress-filter').serialize(),
			beforeSend: function(){
			            $('.progress-panel').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
            $('.progress-panel').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    var $urgent = "";
                    if (val.urgent == "1") {
                        $urgent = "class='red'";
                    }
                    $row += '<tr><td>' + val.name + '</td><td>' + val.campaign + '</td><td>' + val.date + '</td><td>' + val.time + '</td><td ' + $urgent + '>' + val.status + '</td><td><span class="glyphicon glyphicon-comment tt pointer" data-toggle="tooltip" data-placement="top" title="'+val.comments+'"></span> <a href="'+helper.baseUrl+'records/detail/' + val.urn + '"><span class="glyphicon glyphicon-play"></span></a></td></tr>';
                });
                $('.progress-panel').append('<table class="table table-striped table-responsive" style="max-height:600px"><thead><th>Name</th><th>Campaign</th><th>Date</th><th>Time</th><th>Status</th><th>View</th></thead><tbody>' + $row + '</tbody></table>');
				$('.tt').tooltip();
            } else {
                $('.progress-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
	/* the function for the progress panel on the client dashboard */
    interest_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/nbf_progress',
            type: "POST",
            dataType: "JSON",
            data: $('.interest-filter').serialize(),
			beforeSend: function(){
			            $('.interest-panel').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
            $('.interest-panel').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    var $urgent = "";
                    if (val.urgent == "1") {
                        $urgent = "class='red'";
                    }
                    $row += '<tr><td>' + val.name + '</td><td>' + val.date + '</td><td>' + val.time + '</td><td ' + $urgent + '>' + val.status + '</td><td><span class="glyphicon glyphicon-comment tt pointer" data-toggle="tooltip" data-placement="top" title="'+val.comments+'"></span> <a href="'+helper.baseUrl+'records/detail/' + val.urn + '"><span class="glyphicon glyphicon-play"></span></a></td></tr>';
                });
                $('.interest-panel').append('<table class="table table-striped table-responsive" style="max-height:600px"><thead><th>Name</th><th>Date</th><th>Time</th><th>Status</th><th>View</th></thead><tbody>' + $row + '</tbody></table>');
				$('.tt').tooltip();
            } else {
                $('.interest-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
/* the function for the latest activity panel on the management dashboard */
    agent_activity: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_activity',
            type: "POST",
            dataType: "JSON",
            data: $('.activity-filter').serialize(),
			beforeSend: function(){
			            $('.agent-activity').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
            $('.agent-activity').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $row += '<tr><td>' + val.name + '</td><td>' + val.campaign + '</td><td>' + val.outcome + '</td><td>' + val.when + '</td><td>' + val.outcome_date + '</td><td><a href="'+helper.baseUrl+'records/detail/' + val.urn + '"><span class="glyphicon glyphicon-play"></span></a></td></tr>';
                });
                $('.agent-activity').append('<table class="table table-striped table-responsive"><thead><th>Name</th><th>Campaign</th><th>Last Outcome</th><th>Last Update</th><th>Last Transfer</th><th>View</th></thead><tbody>' + $row + '</tbody></table>');
            } else {
                $('.agent-activity').append('<p>' + response.msg + '</p>');
            }
        });
    },
	/* the function for the agent success on the management dashboard */
    agent_success: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_success',
            type: "POST",
            dataType: "JSON",
            data: $('.success-filter').serialize(),
			beforeSend: function(){
			            $('.agent-success').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
			 $('.agent-success').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $row += '<tr><td>' + val.name + '</td>';
					if(campaign){
					 $row += "<td>" + val.campaign + "</td>	";
					} else {  $row += "<td>All</td>	"; }
					$row += '<td>' + val.dials + '</td><td>' + val.transfers + '</td><td>' + val.rate + '</td></tr>';
                });
                $('.agent-success').append('<table class="table table-striped table-responsive"><thead><th>Name</th><th>Campaign</th><th>Dials</th><th>Transfers</th><th>Success Rate</th></thead><tbody>' + $row + '</tbody></table>');
            } else {
                $('.agent-success').append('<p>' + response.msg + '</p>');
            }
        });
    },
		/* the function for the agent success on the management dashboard */
    agent_data: function(filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_data',
            type: "POST",
            dataType: "JSON",
            data: $('.data-filter').serialize(),
			beforeSend: function(){
			            $('.agent-data').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
			 $('.agent-data').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $row += '<tr><td>' + val.name + '</td>';
					if(campaign){
					 $row += "<td>" + val.campaign + "</td>	";
					} else {  $row += "<td>All</td>	"; }
					$row += '<td>' + val.total + '</td><td>' + val.virgin + '</td><td>' + val.in_progress + '</td><td>' + val.completed + '</td><td>' + val.pc_virgin + '</td><td>' + val.pc_in_progress + '</td><td>' + val.pc_completed + '</td></tr>';
                });
                $('.agent-data').append('<table class="table table-striped table-responsive"><thead><th>Name</th><th>Campaign</th><th>Total</th><th>New</th><th>In Progress</th><th>Completed</th><th>% New</th><th>% In Progress</th><th>% Completed</th></thead><tbody>' + $row + '</tbody></table>');
            } else {
                $('.agent-data').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the agent current hours on the management dashboard */
    agent_current_hours: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_current_hours',
            type: "POST",
            dataType: "JSON",
            data: $('.hours-filter').serialize(),
			beforeSend: function(){
			            $('.agent-current-hours').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
			 $('.agent-current-hours').empty();
            var $row = "";
            if (response.success) {
            	var start = new Date;
	            $.each(response.data, function (agent, campaign) {
	            	$row += '<tr id="duration"><td>' + agent + '</td>';
	            	//$.each(campaigns, function (campaign, duration) {
	            		elapsed_seconds = ((new Date - start)/1000)+Number(campaign['duration']);
	            		$row += "<td>" + campaign['campaign'] + "</td>	";
	            		$row += '<td style="font-weight:bold;" id="time_box_date_'+agent+'">' + get_elapsed_time_string(elapsed_seconds) + '</td><td style="display:none;" id=time_box_seconds_'+agent+'">'+campaign['duration']+'</td>';
	            		$row += "<td>" + campaign['worked'] + "</td>";
	            		$row += '<td id="transfers_box_'+agent+'">' + campaign['transfers'] + '</td>';
	            		$row += '<td id="rate_box_'+agent+'">' + campaign['rate']+ ' per hour' + '</td>';
	            		$row += "</tr>";
	            	//});
	            });
	            $('.agent-current-hours').append('<table class="table table-striped table-responsive"><thead><th>Agent</th><th>Campaign</th><th>Time on this campaign</th><th>Records worked</th><th>Transfers</th><th>Current Rate</th><th style="display:none;"></th></thead><tbody>' + $row + '</tbody></table>');
            } else {
                $('.agent-current-hours').append('<p>' + response.msg + '</p>');
            }
        });
    }

}