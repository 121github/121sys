// JavaScript Document
$(document).ready(function() {
    dials_report.init();
});

var dials_report = {
    init: function() {

        filters.init();

        if ($('.filter-form').find('input[name="outcome"]').val() == ""){
            $('.filter-form').find('input[name="outcome"]').val(70);
            $('.filter-form').find('input[name="colname"]').val("Transfer");
        }

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Any Time': ["01/01/2014", moment()],
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
                startDate: "01/01/2014",
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
                dials_report.get_outcomes_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            dials_report.dials_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            dials_report.get_outcomes_filter();
        });

        $(document).on("click", ".refresh-data", function (e) {
            e.preventDefault();
            dials_report.dials_panel();
        });

        dials_report.dials_panel();
    },
    dials_panel: function() {

        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined'?($('.graph-color').css('display') == 'none'?'none':'inline-block'):'none');

        var table = $('.dials-table');
        table.empty();

        $.ajax({
            url: helper.baseUrl + 'reports/dials_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function() {
                $('.dials-table').find('tbody').append('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {
            table.empty();
            if (response.success) {
                var search_url = "";

                var thead = ("<tr class='info'><th>Total Dials</th>"
                    + "<th>" + response.total_dials.total + "</th>"
                    + "<th></th>"
                    + "<th></th></tr>"
                );

                tbody = '';
                $.each(response.total_dials, function(i, val) {
                    if (i != 'total') {
                        search_url = helper.baseUrl + 'search/custom/records/'
                            + 'update-date-from/'+$('.filter-form').find('input[name="date_from"]').val()
                            + '/update-date-to/'+$('.filter-form').find('input[name="date_to"]').val()
                            + response.filter_url
                            + '/contact-made/' + ((val.contact == 'Total Contact')?'1':'0');

                        tbody += ("<tr>"
                        + "<td>"+val.contact+"</td>"
                        + "<td>"+"<a href='" + search_url + "'>"+val.num+"</a>"
                        + "<td>"+((val.num*100)/response.total_dials.total).toFixed(2)+"%</td>"
                        + "<td style='text-align: right'><span class='graph-color fa fa-circle' style='display:"+graph_color_display+"; color:#" + val.colour + "' ></span>"
                        + "</tr>");
                    }
                });

                table.append(thead+tbody);

                tbody = '';
                if (typeof response.dials_by_outcome.contact != 'undefined') {
                    thead = ("<tr class='success'><th>Contact Outcome</th>"
                        + "<th>" + response.dials_by_outcome.contact.total + "</th>"
                        + "<th></th>"
                        + "<th></th></tr>"
                    );

                    if (response.dials_by_outcome.contact.total > 0) {
                        $.each(response.dials_by_outcome.contact, function(i, val) {
                            if (i != 'total') {
                                search_url = helper.baseUrl + 'search/custom/records/'
                                    + 'update-date-from/'+$('.filter-form').find('input[name="date_from"]').val()
                                    + '/update-date-to/'+$('.filter-form').find('input[name="date_to"]').val()
                                    + response.filter_url
                                    + '/outcome/'+val.outcome_id
                                    + '/contact-made/1';

                                tbody += ("<tr>"
                                + "<td>"+val.outcome+"</td>"
                                + "<td>"+"<a href='" + search_url + "'>"+val.num+"</a>"
                                + "<td>"+((val.num*100)/response.dials_by_outcome.contact.total).toFixed(2)+"%</td>"
                                + "<td style='text-align: right'><span class='graph-color fa fa-circle' style='display:"+graph_color_display+"; color:#" + val.colour + "' ></span>"
                                + "</tr>");
                            }
                        });
                    }
                    else {
                        tbody += ("<tr>"
                        + "<td colspan='3' style='color: red'>No records with Contact Outcomes currently with these filters</td>"
                        + "</tr>");
                    }
                }
                else {
                    thead = ("<tr class='success'><th>Contact Outcome</th>"
                        + "<th>0</th>"
                        + "<th></th>"
                        + "<th></th></tr>"
                    );

                    tbody += ("<tr>"
                    + "<td colspan='3' style='color: red'>No records with Contact Outcomes currently with these filters</td>"
                    + "</tr>");
                }


                table.append(thead+tbody);

                tbody = '';
                if (typeof response.dials_by_outcome.no_contact != 'undefined') {
                    thead = ("<tr class='success'><th>No Contact Outcome</th>"
                        + "<th>" + response.dials_by_outcome.no_contact.total + "</th>"
                        + "<th></th>"
                        + "<th></th></tr>"
                    );

                    if (response.dials_by_outcome.no_contact.total > 0) {
                        $.each(response.dials_by_outcome.no_contact, function(i, val) {
                            if (i != 'total') {
                                search_url = helper.baseUrl + 'search/custom/records/'
                                    + 'update-date-from/'+$('.filter-form').find('input[name="date_from"]').val()
                                    + '/update-date-to/'+$('.filter-form').find('input[name="date_to"]').val()
                                    + response.filter_url
                                    + '/outcome/'+val.outcome_id
                                    + '/contact-made/0';

                                tbody += ("<tr>"
                                + "<td>"+val.outcome+"</td>"
                                + "<td>"+"<a href='" + search_url + "'>"+val.num+"</a>"
                                + "<td>"+((val.num*100)/response.dials_by_outcome.no_contact.total).toFixed(2)+"%</td>"
                                + "<td style='text-align: right'><span class='graph-color fa fa-circle' style='display:"+graph_color_display+"; color:#" + val.colour + "' ></span>"
                                + "</tr>");
                            }
                        });
                    }
                    else {
                        tbody += ("<tr>"
                        + "<td colspan='3' style='color: red'>No records With No Contact Outcomes currently with these filters</td>"
                        + "</tr>");
                    }
                }
                else {
                    thead = ("<tr class='success'><th>No Contact Outcome</th>"
                        + "<th>0</th>"
                        + "<th></th>"
                        + "<th></th></tr>"
                    );

                    tbody += ("<tr>"
                    + "<td colspan='3' style='color: red'>No records With No Contact Outcomes currently with these filters</td>"
                    + "</tr>");
                }

                table.append(thead+tbody);

            } else {
                table.append('<p style="padding: 10px;">' + response.msg + '</p>');
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
            dials_report.get_graphs(response);

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
                        options += "<option value=" + val.id + ">" + val.name + "</option>";
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
                data.addColumn('number', 'Dials');

                var data2 = new google.visualization.DataTable();
                data2.addColumn('string', 'Topping');
                data2.addColumn('number', 'Dials');

                var data3 = new google.visualization.DataTable();
                data3.addColumn('string', 'Topping');
                data3.addColumn('number', 'Dials');

                var rows = [];
                var rows2 = [];
                var rows3 = [];
                var colors = [];
                var colors2 = [];
                var colors3 = [];

                // Set chart options
                //var height = data.getNumberOfRows() * 21 + 30;
                var options = {
                    'legend': {position: 'none'},
                    'title': 'Total Records',
                    'width': 300,
                    'height': 300,
                    'hAxis': {textPosition: 'none'},
                    'colors': colors,
                    curveType: 'function'
                };

                var options2 = {
                    'legend': {position: 'none'},
                    'title': 'Contact Outcome',
                    'width': 300,
                    'height': 300,
                    'hAxis': {textPosition: 'none'},
                    'colors': colors2,
                    curveType: 'function'
                };

                var options3 = {
                    'legend': {position: 'none'},
                    'title': 'No Contact Outcome',
                    'width': 300,
                    'height': 300,
                    'hAxis': {textPosition: 'none'},
                    'colors': colors3,
                    curveType: 'function'
                };

                if (response.total_dials.total > 0) {

                    if (typeof response.total_dials != 'undefined') {
                        $.each(response.total_dials, function (i, val) {
                            if (i != 'total') {
                                rows.push([val.num_dials, parseInt(val.num)]);
                                colors.push('#' + val.colour);
                            }
                        });
                    }
                    data.addRows(rows);

                    if (typeof response.dials_by_outcome.contact != 'undefined') {
                        $.each(response.dials_by_outcome.contact, function (i, val) {
                            if (i != 'total') {
                                rows2.push([val.outcome, parseInt(val.num)]);
                                colors2.push('#' + val.colour);
                            }
                        });
                    }
                    data2.addRows(rows2);

                    if (typeof response.dials_by_outcome.no_contact != 'undefined') {
                        $.each(response.dials_by_outcome.no_contact, function (i, val) {
                            if (i != 'total') {
                                rows3.push([val.outcome, parseInt(val.num)]);
                                colors3.push('#' + val.colour);
                            }
                        });
                    }
                    data3.addRows(rows3);


                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_1'));
                    chart.draw(data, options);

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_2'));
                    chart.draw(data2, options2);

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_3'));
                    chart.draw(data3, options3);
                }
                else {
                    $('#chart_div_1').html("No data");
                    $('#chart_div_2').html("");
                    $('#chart_div_3').html("");
                }
            }
        });
    }
}