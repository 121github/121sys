// JavaScript Document
$(document).ready(function() {
    activity.init()
});

var activity = {
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
                activity.activity_panel()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            activity.activity_panel()
        });

        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="colname"]').val($(this).text());
			$(this).closest('form').find('input[name="team"]').val('');
            activity.activity_panel()
        });
		$(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="colname"]').val($(this).text());
			$(this).closest('form').find('input[name="agent"]').val('');
            activity.activity_panel()
        });
		$(document).on("click", ".source-filter", function(e) {
            e.preventDefault();
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="source"]').val($(this).attr('id'));
            activity.activity_panel()
        });
        activity.activity_panel()
    },
    activity_panel: function(campaign) {
        $.ajax({
            url: helper.baseUrl + 'reports/activity_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function() {
                $('.activity-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {
            $('.activity-panel').empty();
            var $row = "";
            if (response.success) {
                var $outcomes = "";
				var $header ="";
				var $header_extra = "";
                $('.activity-panel').append('<p>Total Dials:' + response.total + '</p>');
				$header += '<table class="table actvity-table"><thead><tr><th>Outcome</th><th>Count</th>';
				var $colname = '<th>Call center %</th>';
                $.each(response.data, function(i, val) {
					$outcomes += '<tr><td>' + val.outcome + '</td><td><em><a href="' + val.url + '">' + val.count + '</a></td><td> ' + val.pc + '%</em></td>';
					if(val.overall){
					$header_extra = '<th>Call center %</th>';
					$outcomes += '<td> ' + val.overall + '%</td>';
					$colname = '<th>'+ val.colname + '</th>';
					}
                    $outcomes+= '</tr>';
                });
				console.log($header_extra);
				$header += $colname+$header_extra+'</tr></thead>';
				$outcomes += '</table>';
                $('.activity-panel').append('<div class="list-group">' + $header+$outcomes + '</div>');
            } else {
                $('.activity-panel').append('<p>' + response.msg + '</p>');
            }
        });
    }
}