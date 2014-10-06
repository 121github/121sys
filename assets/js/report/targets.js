var targets = {
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
                startDate: "02/07/2014",
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                reports.targets_panel();
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });
			//reports.targets_panel();
    },
    targets_panel: function() {
        $.ajax({
            url: helper.baseUrl + 'reports/target_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function() {
                $('.target-data').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {
            $('.target-data').empty();
            var $row = "";
            if (response.success) {
                $.each(response.data, function(i, val) {
                    $row += '<td>' + val.campaign + '</td><td>' + val.target + '</td><td>' + val.completed + '</td><td>' + val.progress + '</td><td>' + val.perday + '</td><td>' + val.average + '</td></tr>';
                });
                $('.target-data').append('<table class="table table-striped table-responsive"><thead><th>Campaign</th><th>Target</th><th>Completed</th><th>Progress</th><th>Daily Target</th><th>Daily Average</th></thead><tbody>' + $row + '</tbody></table>');
            } else {
                $('.target-data').append('<p>' + response.msg + '</p>');
            }
        });
    }
}