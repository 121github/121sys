// JavaScript Document
$(document).ready(function() {
    productivity.init()
});

var productivity = {
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
                productivity.productivity_panel()
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
            productivity.productivity_panel()
        });
		$(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            productivity.productivity_panel()
        });
        $(document).on("click", ".outcome-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="outcome"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="colname"]').val($(this).text());
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            productivity.productivity_panel()
        });

        productivity.productivity_panel()
    },
    productivity_panel: function() {

        var thead = $('.productivity-table').find('thead');
        thead.empty();
        var tbody = $('.productivity-table').find('tbody');
        tbody.empty();

        $.ajax({
            url: helper.baseUrl + 'reports/productivity_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function() {
                $('.productivity-table').find('tbody').append('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {
            tbody.empty();
            if (response.success) {
                var total_duration = 0;
                var productivity_val = "";
                var search_url = "";
                thead.append("<tr><th>Agent</th>"
                            + "<th>"+$('.filter-form').find('input[name="colname"]').val()+"</th>"
                            + "<th>Talk Time</th>"
                            + "<th>Ring Time</th>"
                            + "<th>Total Duration</th>"
                            + "<th style='text-align: right'>Productivity (<span style='font-size: 10px'> "+$('.filter-form').find('input[name="colname"]').val()+" per hour</span>)</th>"
                );

				$.each(response.data, function(i, val) {
                    total_duration = ((parseInt(val.duration)+parseInt(val.ring_time))/3600);
                    productivity_val = ((val.count/total_duration).toFixed(2));
                    search_url = helper.baseUrl + 'search/custom/history/'
                                                +'contact-from/'+$('.filter-form').find('input[name="date_from"]').val()
                                                +'/contact-to/'+$('.filter-form').find('input[name="date_to"]').val()
                                                +'/outcome/'+$('.filter-form').find('input[name="outcome"]').val()
                                                +'/user/'+val.agent_id;

                    tbody.append("<tr class='"+(total_duration == 0?"danger":"success")+"'><td>"+val.agent
                                + "<td>"+"<a href='" + search_url + "'>" + val.count + "</a>"
                                + "<td>"+productivity.toHHMMSS(val.duration)
                                + "<td>"+productivity.toHHMMSS(val.ring_time)
                                + "<td>"+productivity.toHHMMSS(parseInt(val.duration)+parseInt(val.ring_time))
                                + "<td style='text-align: right'>"+(total_duration>0?productivity_val:"ERROR")
                                + "</tr>"
                    );
                });
            } else {
                tbody.append('<p>' + response.msg + '</p>');
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