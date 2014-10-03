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
                startDate: "02/07/2014",
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
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            activity.activity_panel()
        });

        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
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
                $('.activity-panel').append('<p>Total Dials:' + response.total + '</p>');
                $.each(response.data, function(i, val) {

                    $outcomes += '<a href="' + val.url + '" class="list-group-item"><i class="fa fa-comment fa-fw"></i>' + val.outcome + '<span class="pull-right text-muted small padl"><em>' + val.count + '</em></span><span class="padl pull-right small"><em>' + val.pc + '%</em></span></a>';
                });
                $('.activity-panel').append('<div class="list-group">' + $outcomes + '</div>');
            } else {
                $('.activity-panel').append('<p>' + response.msg + '</p>');
            }
        });
    }
}