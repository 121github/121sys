// JavaScript Document
$(document).ready(function() {
    agentappointment.init()
});

var agentappointment = {
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
                agentappointment.agentappointment_panel()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            agentappointment.agentappointment_panel()
        });

        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            agentappointment.agentappointment_panel()
        });
        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            agentappointment.agentappointment_panel()
        });
        agentappointment.agentappointment_panel()
    },
    agentappointment_panel: function(agentappointment) {
        $.ajax({
            url: helper.baseUrl + 'reports/agentappointment_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            var $row = "";
            $tbody = $('.agentappointment-data .ajax-table').find('tbody');
    		$tbody.empty();
            if (response.success) {
            	$.each(response.data, function(i, val) {
                    if (response.data.length) {
						$tbody
							.append("<tr><td class='agent'>"
									+ val.agent
								+ "</td><td class='name'>"
									+ val.name
								+ "</td><td class='appointments'>"
									+ val.appointments
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