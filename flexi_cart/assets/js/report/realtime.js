// JavaScript Document
$(document).ready(function() {
    realtime.init()
});

var realtime = {
    init: function() {

        if ($('.filter-form').find('input[name="outcome"]').val() == ""){
            $('.filter-form').find('input[name="outcome"]').val(70);
            $('.filter-form').find('input[name="colname"]').val("Transfer");
        }

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
                $(this).closest('ul').find('a').css("color","black");
                $(this).css("color","green");
                realtime.realtime_panel()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });
        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            realtime.realtime_panel()
        });
		$(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            realtime.realtime_panel()
        });
        $(document).on("click", ".outcome-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="outcome"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="colname"]').val($(this).text());
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            realtime.realtime_panel()
        });
		$(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="colname"]').val($(this).text());
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            realtime.realtime_panel()
        });

        realtime.realtime_panel()
    },
    realtime_panel: function() {

        var thead = $('.realtime-table').find('thead');
        thead.empty();
        var tbody = $('.realtime-table').find('tbody');
        tbody.empty();

        $.ajax({
            url: helper.baseUrl + 'reports/realtime_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function() {
                $('.realtime-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {

            if (response.success) {
                var total_duration = 0;
                var table = "<div class='table-responsive'><table class='table table-striped'><thead><tr><th>User</th><th>Dials</th><th>Duration</th><th>Dials per hour</th></tr></thead></tbody>";
				$.each(response.data,function(i,row){
					table += "<tr><td>"+row.name+"</td><td>"+row.count+"</td><td>"+row.duration+"</td><td>"+row.dph+"</td></tr>"
				})
				
				table += "</tbody></table>";
				  $('.realtime-panel').html(table);
            } else {
	  		$('.realtime-panel').html("<p>No data was found</p>");
            }
        });
    },
    toHHMMSS: function (secs) {
        var sec_num = parseInt(secs, 10); // don't forget the second param
        var hours   = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        var time    = hours+':'+minutes+':'+seconds;
        return time;
    }
}