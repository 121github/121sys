// JavaScript Document
// JavaScript Document
$(document).ready(function () {
    data_counts.init()
});

var data_counts = {
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
                data_counts.get_outcomes_filter();
                data_counts.get_sources_filter();
                data_counts.get_pots_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            data_counts.data_counts_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            data_counts.get_outcomes_filter();
            data_counts.get_sources_filter();
            data_counts.get_pots_filter();
        });

        $(document).on("click", ".refresh-data", function (e) {
            e.preventDefault();
            data_counts.data_counts_panel();
        });
		$(document).on("click", "#data-counts-table tbody tr .campain-link", function (e) {
            e.preventDefault();
            data_counts.data_counts_by_pot($(this).attr('data-id'));
        });
		

        data_counts.data_counts_panel()
    },
    data_counts_panel: function (campaign) {

        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined' ? ($('.graph-color').css('display') == 'none' ? 'none' : 'inline-block') : 'none');

        $.ajax({
            url: helper.baseUrl + 'reports/data_counts',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('.data-counts-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.data-counts-panel').empty();
            var $row = "";
            if (response.success) {
                var $body = "";
                var $header = "";
                var $header_extra = "";
                var $colours = "";
                $('.data-counts-panel').append('<p>Total Records: ' + response.total + '</p>');
				//tr,ta,tp,va,vp,wa,wp,fd,fc
                $header += '<table id="data-counts-table" class="table small table-striped"><thead><tr><th>Campaign</th><th>Total Records</th><th>Total Available</th><th>Total Parked</th><th>Virgin Available</th><th>Virgin Parked</th><th>Working Available</th><th>Working Parked</th><th>Dead</th><th>Complete</th>';

                $.each(response.data, function (i, val) {
                    $body += '<tr>' +
                            '<td class="campain-link pointer" data-id="'+val.id+'">' + val.name + '</td>' +
                            '<td><em><a href="' + val.url_tr + '">' + val.tr + '</a></td>' +
							'<td><em><a href="' + val.url_ta + '">' + val.ta + '</a></td>' +
							'<td><em><a href="' + val.url_tp + '">' + val.tp + '</a></td>' +
							'<td><em><a href="' + val.url_va + '">' + val.va + '</a></td>' +
							'<td><em><a href="' + val.url_vp + '">' + val.vp + '</a></td>' +
							'<td><em><a href="' + val.url_wa + '">' + val.wa + '</a></td>' +
							'<td><em><a href="' + val.url_wp + '">' + val.wp + '</a></td>' +
							'<td><em><a href="' + val.url_fd + '">' + val.fd + '</a></td>' +
							'<td><em><a href="' + val.url_fc + '">' + val.fc + '</a></td>'

                    $colours = '<td style="text-align: right"><span class="graph-color fa fa-circle" style="display:' + graph_color_display + '; color:#' + val.colour + '"> </span></td>';
                    $body += $colours + '</tr>';
                });
                $header += '</tr></thead><tbody>';
                $body += '</tbody></table>';
                $('.data-counts-panel').append('<div class="list-group">' + $header + $body + '</div>');
            } else {
                $('.data-counts-panel').append('<p>' + response.msg + '</p>');
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
            data_counts.get_graphs(response);
        });
    },
	data_counts_by_pot: function (campaign) {

        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined' ? ($('.graph-color').css('display') == 'none' ? 'none' : 'inline-block') : 'none');

        $.ajax({
            url: helper.baseUrl + 'reports/data_counts_by_pot',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()+'&campaign='+campaign,
            beforeSend: function () {
                $('.data-counts-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.data-counts-panel').empty();
            var $row = "";
            if (response.success) {
                var $body = "";
                var $header = "";
                var $header_extra = "";
                var $colours = "";
                $('.data-counts-panel').append('<p>Total Dials:' + response.total + '</p>');
				//tr,ta,tp,va,vp,wa,wp,fd,fc
                $header += '<table id="data-counts-table" class="table small table-striped"><thead><tr><th>Data Pot</th><th>Total Records</th><th>Total Available</th><th>Total Parked</th><th>Virgin Available</th><th>Virgin Parked</th><th>Working Available</th><th>Working Parked</th><th>Dead</th><th>Complete</th>';

                $.each(response.data, function (i, val) {
                    $body += '<tr>' +
                            '<td>' + val.name + '</td>' +
                            '<td><em><a href="' + val.url_tr + '">' + val.tr + '</a></td>' +
							'<td><em><a href="' + val.url_ta + '">' + val.ta + '</a></td>' +
							'<td><em><a href="' + val.url_tp + '">' + val.tp + '</a></td>' +
							'<td><em><a href="' + val.url_va + '">' + val.va + '</a></td>' +
							'<td><em><a href="' + val.url_vp + '">' + val.vp + '</a></td>' +
							'<td><em><a href="' + val.url_wa + '">' + val.wa + '</a></td>' +
							'<td><em><a href="' + val.url_wp + '">' + val.wp + '</a></td>' +
							'<td><em><a href="' + val.url_fd + '">' + val.fd + '</a></td>' +
							'<td><em><a href="' + val.url_fc + '">' + val.fc + '</a></td>'

                    $colours = '<td style="text-align: right"><span class="graph-color fa fa-circle" style="display:' + graph_color_display + '; color:#' + val.colour + '"> </span></td>';
                    $body += $colours + '</tr>';
                });
                $header += '</tr></thead><tbody>';
                $body += '</tbody></table>';
                $('.data-counts-panel').append('<div class="list-group">' + $header + $body + '</div>');
            } else {
                $('.data-counts-panel').append('<p>' + response.msg + '</p>');
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
            data_counts.get_graphs(response);
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
                        options += "<option value=" + val.id + ">" + val.id + "</option>";
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
                            if (val.id.length > 0) {
                                rows.push([val.id, parseInt(val.tr)]);
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