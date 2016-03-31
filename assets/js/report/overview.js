// JavaScript Document
$(document).ready(function () {
    overview.init()
});

var overview = {
    init: function () {

        filters.init();

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
            function (start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        //optgroup
        $('li.dropdown-header').on('click', function (e) {
            setTimeout(function () {
                //Get outcomes by campaigns selected
                overview.get_outcomes_filter();
                overview.get_sources_filter();
                overview.get_pots_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            overview.overview_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            overview.get_outcomes_filter();
            overview.get_sources_filter();
            overview.get_pots_filter();
        });

        $(document).on("click", ".refresh-data", function (e) {
            e.preventDefault();
            overview.overview_panel();
        });

        overview.overview_panel()
    },
    overview_panel: function (campaign) {
		var pots = $('#by-pots').val()=="1"?"/pots":"";
        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined' ? ($('.graph-color').css('display') == 'none' ? 'none' : 'inline-block') : 'none');

        $.ajax({
            url: helper.baseUrl + 'reports/overview_data'+pots,
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('.overview-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.overview-panel').empty();
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
                $('.overview-panel').append('<p>Total Dials:<a href="'+response.total_url+'">' + response.total + '</a></p>');
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
                $('.overview-panel').append('<div class="list-group">' + table + '</div>');
            } else {
                $('.overview-panel').append('<p>' + response.msg + '</p>');
            }

            //////////////////////////////////////////////////////////
            //Filters/////////////////////////////////////////////////
            //////////////////////////////////////////////////////////
            var filters = "";

            filters += "<span class='btn btn-default btn-xs clear-filters pull-right'>" +
                "<span class='glyphicon glyphicon-remove' style='padding-left:3px; color:black;'></span> Clear" +
                "</span>";

            //Date
            filters += "<h5><strong>Date </strong></h5>" +
                "<ul>" +
                "<li style='list-style-type:none'>" + $(".filter-form").find("input[name='date_from']").val() + "</li>" +
                "<li style='list-style-type:none'>" + $(".filter-form").find("input[name='date_to']").val() + "</li>" +
                "</ul>";

            //Campaigns
            var size = ($('.campaign-filter  option:selected').size() > 0 ? "(" + $('.campaign-filter  option:selected').size() + ")" : '');
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Campaigns</strong> " + size + "</h5><ul>";
            $('.campaign-filter  option:selected').each(function (index) {
                filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
            });
            filters += "</ul>";

            //Outcomes
            var size = ($('.outcome-filter  option:selected').size() > 0 ? "(" + $('.outcome-filter  option:selected').size() + ")" : '');
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Outcomes</strong> " + size + "</h5><ul>";
            $('.outcome-filter option:selected').each(function (index) {
                var color = "black";
                if ($(this).parent().attr('label') === 'positive') {
                    color = "green";
                }
                filters += "<li style='list-style-type:none'><span style='color: " + color + "'>" + $(this).text() + "</span></li>";
            });
            filters += "</ul>";

            //Teams
            var size = ($('.team-filter  option:selected').size() > 0 ? "(" + $('.team-filter  option:selected').size() + ")" : '');
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Teams</strong> " + size + "</h5><ul>";
            $('.team-filter  option:selected').each(function (index) {
                filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
            });
            filters += "</ul>";


            //Agents
            var size = ($('.agent-filter  option:selected').size() > 0 ? "(" + $('.agent-filter  option:selected').size() + ")" : '');
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Agents</strong> " + size + "</h5><ul>";
            $('.agent-filter  option:selected').each(function (index) {
                filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
            });
            filters += "</ul>";


                        //Sources
            var size = ($('.source-filter  option:selected').size() > 0 ? "(" + $('.source-filter  option:selected').size() + ")" : '');
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Sources</strong> " + size + "</h5><ul>";
            $('.source-filter  option:selected').each(function (index) {
                filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
            });
            filters += "</ul>";
			
			//Pots
            var size = ($('.pot-filter  option:selected').size() > 0 ? "(" + $('.pot-filter  option:selected').size() + ")" : '');
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Pots</strong> " + size + "</h5><ul>";
            $('.pot-filter  option:selected').each(function (index) {
                filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
            });
            filters += "</ul>";

            $('#filters').html(filters);

            //////////////////////////////////////////////////////////
            //Graphics/////////////////////////////////////////////////
            //////////////////////////////////////////////////////////
            overview.get_graphs(response);
        });
    },
    get_outcomes_filter: function () {
        $.ajax({
            url: helper.baseUrl + 'reports/get_outcomes_filter',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_outcomes, function (type, data) {
                    options += "<optgroup label=" + type + ">";
                    $.each(data, function (i, val) {
                        options += "<option value=" + val.id + ">" + val.name + "</option>";
                    });
                    options += "</optgroup>";
                });
                $('#outcome-filter').html(options).selectpicker('refresh');
            }
        });
    },
    get_sources_filter: function () {
        $.ajax({
            url: helper.baseUrl + 'reports/get_sources_filter',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_sources, function (i, val) {
                    options += "<option value=" + val.id + ">" + val.name + "</option>";
                });
                $('#source-filter').html(options).selectpicker('refresh');
            }
        });
    },

    get_pots_filter: function () {
        $.ajax({
            url: helper.baseUrl + 'reports/get_pots_filter',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_pots, function (i, val) {
                    options += "<option value=" + val.id + ">" + val.name + "</option>";
                });
                $('#pot-filter').html(options).selectpicker('refresh');
            }
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