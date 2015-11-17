// JavaScript Document

// JavaScript Document
$(document).ready(function () {
    outcome.init()
});

var outcome = {
    init: function () {
        outcome.current_campaign();
        $('nav#filter-right').mmenu({
            navbar: {
                title: "Filters <span class='text-primary current-campaign'></span>"
            },
            extensions: ["pageshadow", "effect-menu-slide", "effect-listitems-slide", "pagedim-black"],
            offCanvas: {
                position: "right",
                zposition: "front"
            }
        });

        $('.daterange').daterangepicker({
                opens: "right",
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

        //optgroup
        $('li.dropdown-header').on('click', function (e) {
            setTimeout(function () {
                outcome.get_outcomes_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            outcome.outcome_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            outcome.get_outcomes_filter();
        });

        $(document).on("click", ".refresh-data", function (e) {
            e.preventDefault();
            outcome.outcome_panel();
        });

        $(document).on("click", ".clear-filters", function (e) {
            e.preventDefault();
            location.reload();
        });

        $(document).on("click", '.plots-tab', function(e) {
            e.preventDefault();
            $('.graph-color').show();
        });

        $(document).on("click", '.filters-tab,.searches-tab', function(e) {
            e.preventDefault();
            $('.graph-color').hide();
        });

        outcome.outcome_panel()
    },
    current_campaign: function () {
        var current_campaign = '';
        $.ajax({
            url: helper.baseUrl + 'reports/get_current_campaign',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                current_campaign = response.current_campaign;
            }
            $('.current-campaign').html(current_campaign);
        });
    },
    outcome_panel: function () {
        var results = [];

        $.ajax({
            url: helper.baseUrl + 'reports/outcome_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            var $row = "";
            var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined'?($('.graph-color').css('display') == 'none'?'none':'inline-block'):'none');
            $tbody = $('.outcome-data .ajax-table').find('tbody');
            $tbody.empty();
            if (response.success) {
                results = response;
                $('#outcome-name').text(response.outcome);
                $.each(response.data, function (i, val) {
                    if (response.data.length) {
                        var hours = Math.floor(val.duration / 3600);
                        var minutes = Math.floor((val.duration - (hours * 3600)) / 60);
                        var seconds = val.duration - (hours * 3600) - (minutes * 60);

                        if (hours < 10) {
                            hours = "0" + hours;
                        }
                        if (minutes < 10) {
                            minutes = "0" + minutes;
                        }
                        if (seconds < 10) {
                            seconds = "0" + seconds;
                        }

                        var duration = hours + ' h ' + minutes + ' m';

                        var style = "";
                        var success = "";
                        if (val.outcomes > 0 && val.duration > 0) {
                            success = "success";
                        }
                        else if ((val.outcomes > 0) && (val.duration == 0)) {
                            success = "danger";
                        }
                        else if (val.campaign == "TOTAL") {
                            style = "font-weight:bold;";
                        }
                        else {
                            success = "warning";
                        }

                        $tbody
                            .append("<tr class='" + success + "' style='" + style + "'><td class='outcome'>"
                            + val.id
                            + "</td><td class='name'>"
                            + val.name
                            + "</td><td class='outcomes'>"
                            + "<a href='" + val.outcomes_url + "'>" + val.outcomes + "</a>"
                            + "</td><td class='total_dials'>"
                            + "<a href='" + val.total_dials_url + "'>" + val.total_dials + "</a>"
                            + "</td><td>" +
                            (val.outcomes > 0?(Math.round((val.outcomes / val.total_dials) * 100)):0) + '%'
                            + "</td><td class='duration' style='duration'>"
                            + ((val.group != "time")&&(val.group != "reason") ? duration : "-")
                            + "</td><td class='rate' style='rate'>"
                            + ((val.group != "time")&&(val.group != "reason") ? val.rate : "-")
                            + "</td><td>"
                            + (val.id == 'TOTAL' || val.name.length == 0 || $.inArray(response.group, ['time', 'date', 'contact']) >= 0 ? "" : ("<span class='graph-color fa fa-circle' style='display:"+graph_color_display+"; color:#" + val.colour + "'></span>"))
                            + "</td></tr>");
                    }
                });
            } else {
                $tbody
                    .append("<tr><td colspan='6'>"
                    + response.msg
                    + "</td></tr>");
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

            $('#filters').html(filters);

            //////////////////////////////////////////////////////////
            //Graphics/////////////////////////////////////////////////
            //////////////////////////////////////////////////////////
            outcome.get_graphs(response);

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
                        var selected = ((type === "positive") ? "Selected" : "");
                        options += "<option value=" + val.id + " " + selected + ">" + val.name + "</option>";
                    });
                    options += "</optgroup>";
                });
                $('#outcome-filter').html(options).selectpicker('refresh');
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
                var title = '';
                switch (response.group) {
                    case 'campaign':
                        title = 'Outcomes by campaign';
                        break;
                    case 'agent':
                        title = 'Outcomes by agent';
                        break;
                    case 'date':
                        title = 'Outcomes by date';
                        break;
                    case 'contact':
                        title = 'Outcomes by date';
                        break;
                    case 'time':
                        title = 'Outcomes by time';
                        break;
                    case 'reason':
                        title = 'Outcomes by reason';
                        break;
                }

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


                if ($.inArray(response.group, ['contact', 'campaign', 'agent', 'reason']) >= 0 && response.data.length > 1) {

                    $.each(response.data, function (i, val) {
                        if (response.data.length) {
                            if (val.name.length > 0) {
                                rows.push([val.name, parseInt(val.outcomes)]);
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
                else if ($.inArray(response.group, ['time', 'date', 'contact']) >= 0 && response.data.length > 1) {
                    $.each(response.data, function (i, val) {
                        if (response.data.length) {
                            if (val.name.length > 0) {
                                rows.push([val.id, parseInt(val.outcomes)]);
                                colors.push('#' + val.colour);
                            }
                        }
                    });
                    data.addRows(rows);

                    var chart = new google.visualization.LineChart(document.getElementById('chart_div_1'));
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