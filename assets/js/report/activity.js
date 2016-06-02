// JavaScript Document
$(document).ready(function() {
    common_report_functions.init()
    report.init();
    filters.init();
});

var report = {
    init: function() {
        report.load_panel();
    },
    load_panel: function() {

        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined' ? ($('.graph-color').css('display') == 'none' ? 'none' : 'inline-block') : 'none');

        $.ajax({
            url: helper.baseUrl + 'reports/activity_data',
            type: "POST",
            dataType: "JSON",
            data: $('#filter-form').serialize(),
            beforeSend: function() {
                $('.report-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {
            $('.report-panel').empty();
            var $row = "";
            if (response.success) {
                var $outcomes = "";
                var $header = "";
                var $header_extra = "";
                var $colours = "";
                $('.report-panel').append('<p>Total Dials:<a href="' + response.total_url + '">' + response.total + '</a></p>');
                $header += '<table class="table actvity-table"><thead><tr><th>Outcome</th><th>Count</th>';
                var $colname = '<th>Call centre %</th>';
                $.each(response.data, function(i, val) {
                    $outcomes += '<tr>' +
                        '<td>' + val.outcome + '</td>' +
                        '<td><em><a href="' + val.url + '">' + val.count + '</a></td>' +
                        '<td> ' + val.pc + '%</em></td>';
                    if (val.overall) {
                        $header_extra = '<th>Call centre %</th>';
                        $outcomes += '<td> ' + val.overall + '%</td>';
                        $colname = '<th>' + val.colname + '</th>';
                    }

                    $colours = '<td style="text-align: right"><span class="graph-color fa fa-circle" style="display:' + graph_color_display + '; color:#' + val.colour + '"> </span></td>';
                    $outcomes += $colours + '</tr>';
                });
                $header += $colname + $header_extra + '<th></th></tr></thead>';
                $outcomes += '</table>';
                $('.report-panel').append('<div class="list-group">' + $header + $outcomes + '</div>');
            } else {
                $('.report-panel').append('<p>' + response.msg + '</p>');
            }
            common_report_functions.filters();
            report.get_graphs(response);
        });
    },


    get_graphs: function(response) {

        google.load('visualization', '1', {
            packages: ['corechart'],
            'callback': function() {
                // Create the data table.
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Topping');
                data.addColumn('number', 'Outcomes');
                var rows = [];
                var colors = [];
                var title = 'Outcomes';

                // Set chart options
                //var height = data.getNumberOfRows() * 21 + 30;
                var options = {
                    'legend': {
                        position: 'none'
                    },
                    'title': title,
                    'width': 300,
                    'height': 300,
                    'hAxis': {
                        textPosition: 'none'
                    },
                    'colors': colors,
                    curveType: 'function'
                };


                if (response.data.length > 1) {

                    $.each(response.data, function(i, val) {
                        if (response.data.length) {
                            if (val.outcome.length > 0) {
                                rows.push([val.outcome, parseInt(val.count)]);
                                colors.push('#' + val.colour);
                            }
                        }
                    });
                    data.addRows(rows);


                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_1'));
                    chart.draw(data, options);

                    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_2'));
                    chart.draw(data, options);

                } else {
                    $('#chart_div_1').html("No data");
                    $('#chart_div_2').html("");
                }
            }
        });
    }
}