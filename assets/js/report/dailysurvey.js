// JavaScript Document
$(document).ready(function() {
    dailysurvey.init()
});

var dailysurvey = {
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
                startDate: "02/07/2014",
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                dailysurvey.dailysurvey_panel()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailysurvey.dailysurvey_panel()
        });
        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailysurvey.dailysurvey_panel()
        });
        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailysurvey.dailysurvey_panel()
        });
        $(document).on("click", ".source-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="source"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            dailysurvey.dailysurvey_panel()
        });
        dailysurvey.dailysurvey_panel()
    },
    dailysurvey_panel: function(dailysurvey) {
        $.ajax({
            url: helper.baseUrl + 'reports/dailysurvey_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            var $row = "";
            $tbody = $('.dailysurvey-data .ajax-table').find('tbody');
    		$tbody.empty();
            if (response.success) {
            	$.each(response.data, function(i, val) {
                    if (response.data.length) {
                    	if (val.total_surveys>0) {
                    		success = "success";
                    	}
                    	else {
                    		success = "warning";
                    	}
						$tbody
							.append("<tr class='"+success+"'><td class='date'>"
									+ val.date
								+ "</td><td class='name'>"
									+ val.name
								+ "</td><td class='complete_surveys'>"
									+ val.complete_surveys
								+ "</td><td class='refused_surveys'>"
									+ val.refused_surveys
								+ "</td><td class='total_surveys'>"
									+ val.total_surveys
								+ "</td><td class='total_dials'>"
									+ val.total_dials
								+ "</td><td class='template_cc' style='duration'>"
									+ val.duration
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