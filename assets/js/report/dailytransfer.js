// JavaScript Document
$(document).ready(function() {
    dailytransfer.init()
});

var dailytransfer = {
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
                startDate: moment(),
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                dailytransfer.dailytransfer_panel()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailytransfer.dailytransfer_panel()
        });
		   $(document).on("click", ".view-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="view"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailytransfer.dailytransfer_panel()
        });
        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailytransfer.dailytransfer_panel()
        });
        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailytransfer.dailytransfer_panel()
        });
        $(document).on("click", ".source-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="source"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailytransfer.dailytransfer_panel()
        });
        dailytransfer.dailytransfer_panel()
    },
    dailytransfer_panel: function(dailytransfer) {
        $.ajax({
            url: helper.baseUrl + 'reports/dailytransfer_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            var $row = "";
            $tbody = $('.dailytransfer-data .ajax-table').find('tbody');
    		$tbody.empty();
            if (response.success) {
            	$.each(response.data, function(i, val) {
                    if (response.data.length) {
                    	
                    	var hours   = Math.floor(val.duration / 3600);
                        var minutes = Math.floor((val.duration - (hours * 3600)) / 60);
                        var seconds = val.duration - (hours * 3600) - (minutes * 60);

                        if (hours   < 10) {hours   = "0"+hours;}
                        if (minutes < 10) {minutes = "0"+minutes;}
                        if (seconds < 10) {seconds = "0"+seconds;}
                        
                        var duration    = hours+' h '+minutes+' m';
                        
                        var style = "";
                        var success = "";
                        if (val.total_transfers>0 && val.duration>0) {
                    		success = "success";
                    	}
                    	else if ((val.total_transfers>0) && (val.duration==0)) {
                    		success = "danger";
                    	}
                    	else if (val.date == "TOTAL") {
                    		style = "font-weight:bold;";
                    	}
                    	else {
                    		success = "warning";
                    	}
                       	
						
						$tbody
						.append("<tr class='"+success+"' style='"+style+"'><td class='date'>"
									+ val.date
								+ "</td><td class='name'>"
									+ val.name
								+ "</td><td class='transfers'>"
								+ 	"<a href='" + val.transfers_url + "'>" + val.transfers + "</a>"
								+ "</td><td class='cross_transfers'>"
								+ 	"<a href='" + val.cross_transfers_url + "'>" + val.cross_transfers + "</a>"
								+ "</td><td class='total transfers'>"
								+ 	"<a href='" + val.total_transfers_url + "'>" + val.total_transfers + "</a>"
								+ "</td><td class='total_dials'>"
								+ 	"<a href='" + val.total_dials_url + "'>" + val.total_dials + "</a>"
								+ "</td><td class='template_cc' style='duration'>"
									+ duration
								+ "</td><td class='template_bcc' style='rate'>"
									+ val.rate
								+ "</td></tr>");
                    }
                });
            } else {
            	$tbody
				.append("<tr><td colspan='6'>"
					+ response.msg
					+ "</td></tr>");
            }
        });
    }
}