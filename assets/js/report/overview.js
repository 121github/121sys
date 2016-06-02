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
		var pots = $('#by-pots').val()=="1"?"/pots":"";
        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined' ? ($('.graph-color').css('display') == 'none' ? 'none' : 'inline-block') : 'none');

        $.ajax({
            url: helper.baseUrl + 'reports/overview_data'+pots,
            type: "POST",
            dataType: "JSON",
            data: $('#filter-form').serialize(),
            beforeSend: function () {
                $('.report-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.report-panel').empty();
            var $row = "";
            if (response.success) {
                var table_body = "";
				var table_row = "";
				var row_counts = "";
                var table_header = "";
                var header_extra = "";
                var colours = "";
				var campaigns = "";
				var table = "";
				var total_count = 0;
                $('.report-panel').append('<p>Total Dials:<a href="'+response.total_url+'">' + response.total + '</a></p>');
                $.each(response.data, function (user, row) {
					table_row = "";
					campaigns = "";
					row_counts = "";
					var user = "";
					$.each(row, function (campaign_id, val) {
					if(typeof val.user!=="undefined"){
					if(val.user=="Total"){
					user = "<th>"+val.user+"</th>";
					} else {
					user = "<td>"+val.user+"</td>";
					}
					if(val.count>0){
					
					total_count = '<td><em><a href="' + (val.user_id==0&&val.user=="Total"?val.grand_total_url:val.total_url) + '">'+response.totals[val.user_id]+'</a></td>';
					} else {
					total_count = "<td>"+response.totals[val.user_id]+"</td>";	
					}
					}
					campaigns += "<th style='background:#"+val.color+"'>"+val.campaign+"</th>";
					if(val.count>0){
                    row_counts +=  '<td><em><a href="' + val.url + '">' + val.count + '</a></td>';
					} else {
					row_counts +=  '<td>' + val.count + '</a></td>';	
					}
                });
				  table_row += '<tr>'+user+row_counts+total_count+'</tr>';
				  table_body += table_row;
				});
                table_header += "<table class='table overview-table'><thead><tr><th>User</th>"+campaigns+"<th>Total</th></tr></thead><tbody>";
                table += table_header+table_body+'</tbody></table>';
                $('.report-panel').append('<div class="list-group">' + table + '</div>');
            } else {
                $('.report-panel').append('<p>' + response.msg + '</p>');
            }

   common_report_functions.filters();
            report.get_graphs(response);
        });
    },
  

    get_graphs: function (response) {

        google.load('visualization', '1', {
            packages: ['corechart'], 'callback': function () {
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
                    'legend': {position: 'none'},
                    'title': title,
                    'width': 300,
                    'height': 300,
                    'hAxis': {textPosition: 'none'},
                    'colors': colors,
                    curveType: 'function'
                };


                if (response.data.length > 1) {

                    $.each(response.data, function (i, val) {
                        if (response.data.length) {
                            if (i.length > 0) {
                                rows.push([i, parseInt(val.count)]);
                                colors.push('#' + val.colour);
                            }
                        }
                    });
                    data.addRows(rows);


                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_1'));
                    chart.draw(data, options);

                    var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_2'));
                    chart.draw(data, options);

                }
                else {
                    $('#chart_div_1').html("No data");
                    $('#chart_div_2').html("");
                }
            }
        });
    }
}