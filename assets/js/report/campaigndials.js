// JavaScript Document
$(document).ready(function() {
    campaigndials.init()
});

var campaigndials = {
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
            campaigndials.campaigndials_panel()
        });
    $(document).on("click", '.daterange', function(e) {
        e.preventDefault();
    });
        

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaigndials.campaigndials_panel()
        });

        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaigndials.campaigndials_panel($(this).text())
        });
        $(document).on("click", ".team-manager-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="team-manager"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaigndials.campaigndials_panel()
        });
        $(document).on("click", ".source-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="source"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            campaigndials.campaigndials_panel()
        });
        campaigndials.campaigndials_panel()
    },
    campaigndials_panel: function(agent_name) {
    	var totalHead = "Total";
    	if (agent_name && agent_name != "Show All") {
    		totalHead = agent_name;
    	}
        $.ajax({
            url: helper.baseUrl + 'reports/campaigndials_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            var $row = "";
            $tbody = $('.campaigndials-data .ajax-table').find('tbody');
            $thead = $('.campaigndials-data .ajax-table').find('thead');
            $tbody.empty();
    		$thead.empty();
            if (response.success) {
            	
            	$thead
            		.append("<tr>"
            				+"<th>Name</th>"
            				+"<th>"+totalHead+"</th>"
            				+"</tr>");
            	
            	
            	$.each(response.data, function(i, val) {
                    if (response.data.length) {
                    	$tbody.
                    		append("<tr><td class='name'>"
									+ val.name
								+ "</td><td class='total'>"
									+ val.total
								+ "</td></tr>");
                    }
                    
                });
            } else {
            	$tbody
				.append("<tr><td colspan='3'>"
					+ response.msg
					+ "</td></tr>");
            }
        });
    }
}