// JavaScript Document
$(document).ready(function() {
    campaignsurvey.init()
});

var campaignsurvey = {
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
                campaignsurvey.campaignsurvey_panel()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaignsurvey.campaignsurvey_panel()
        });
        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="team"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaignsurvey.campaignsurvey_panel()
        });
        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="agent"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaignsurvey.campaignsurvey_panel()
        });
        $(document).on("click", ".source-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="source"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaignsurvey.campaignsurvey_panel()
        });
        campaignsurvey.campaignsurvey_panel()
    },
    campaignsurvey_panel: function(campaignsurvey) {
        $.ajax({
            url: helper.baseUrl + 'reports/campaignsurvey_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            var $row = "";
            $tbody = $('.campaignsurvey-data .ajax-table').find('tbody');
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
                        if (val.complete_surveys>0 && val.duration>0) {
                    		success = "success";
                    	}
                    	else if ((val.complete_surveys>0) && (val.duration==0)) {
                    		success = "danger";
                    	}
                    	else if (val.campaign == "TOTAL") {
                    		style = "font-weight:bold;";
                    	}
                    	else {
                    		success = "warning";
                    	}
                        
						$tbody
						.append("<tr class='"+success+"' style='"+style+"'><td class='campaign'>"
									+ val.campaign
								+ "</td><td class='name'>"
									+ val.name
								+ "</td><td class='complete_surveys'>"
									+ 	"<a href='" + val.complete_surveys_url + "'>" + val.complete_surveys + "</a>"
								+ "</td><td class='refused_surveys'>"
									+ 	"<a href='" + val.refused_surveys_url + "'>" + val.refused_surveys + "</a>"
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