// JavaScript Document
$(document).ready(function() {
    productivity.init()
});

var productivity = {
    init: function() {

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
            });

        //optgroup
        $('li.dropdown-header').on('click', function (e) {
            setTimeout(function () {
                //Get outcomes by campaigns selected
                productivity.get_outcomes_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            productivity.productivity_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            productivity.get_outcomes_filter();
        });

        $(document).on("click", ".refresh-data", function (e) {
            e.preventDefault();
            productivity.productivity_panel();
        });

        $(document).on("click", ".clear-filters", function (e) {
            e.preventDefault();
            location.reload();
        });

        productivity.productivity_panel();
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
                    + "<th>" + response.outcome_colname + "</th>"
                            + "<th>Talk Time</th>"
                            + "<th>Ring Time</th>"
                            + "<th>Total Duration</th>"
                    + "<th style='text-align: right'>Productivity (<span style='font-size: 10px'> " + response.outcome_colname + " per hour</span>)</th>"
                    + "<th></th>"
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
                        + "<td style='text-align: right'><span class='fa fa-circle' style='color:#" + val.colour + "'></span>"
                                + "</tr>"
                    );
                });
            } else {
                tbody.append('<p>' + response.msg + '</p>');
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
            productivity.get_graphs(response);

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
                data.addColumn('number', 'Slices');
                var rows = [];
                var colors = [];
                var title = 'Productivity';

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
                            if (val.agent.length > 0) {
                                rows.push([val.agent, parseInt(val.count)]);
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