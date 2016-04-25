// JavaScript Document

// JavaScript Document
$(document).ready(function () {
    sms.init()
});

var sms = {
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
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
					'Last Year': [moment().subtract('month', 12).startOf('month'),  moment()]
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
                sms.get_outcomes_filter();

                sms.get_sources_filter();
                sms.get_pots_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            sms.sms_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            sms.get_outcomes_filter();

            sms.get_sources_filter();
            sms.get_pots_filter();
        });

        $(document).on("click", ".refresh-data", function (e) {
            e.preventDefault();
            sms.sms_panel();
        });

        $(document).on('click', '.show-sms-btn', function (e) {
            e.preventDefault();
            sms.show_sms($(this), $(this).attr('sms-sent'), $(this).attr('sms-read'), $(this).attr('sms-pending'));
        });
        $(document).on('click', '.view-sms-btn', function (e) {
            e.preventDefault();
            sms.view_sms($(this).attr('item-id'));
        });
        sms.sms_panel();
    },
    sms_panel: function () {
        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined' ? ($('.graph-color').css('display') == 'none' ? 'none' : 'inline-block') : 'none');

        $.ajax({
            url: helper.baseUrl + 'reports/sms_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('.sms-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            var $row = "";
            $tbody = $('.sms-data .ajax-table').find('tbody');
            $tbody.empty();
            if (response.success) {
                $('#sms-name').text(response.sms);
              $.each(response.data, function (i, val) {
                    if (response.data.length) {

                        var style = "";
                        var success = "";
                        if (val.total_sms > 0) {
                            success = "success";
                        }
                        else if (val.campaign == "TOTAL") {
                            style = "font-weight:bold;";
                        }
                        else {
                            success = "warning";
                        }

                        $tbody
                            .append("<tr class='" + success + "' style='" + style + "'><td class='id'>"
                            + val.id + "<span class='sql' style='display:none'>" + val.sql + "</span>"
                            + "</td><td class='name'>"
                            + val.name
                            + "</td><td class='sms_sent'>"
                            + "<a href='#' class='show-sms-btn' style='font-weight: bold; font-size: 19px;'>" + val.sms_sent + "</a>" +
                            "<a href='" + val.sms_sent_url + "' style='font-size:10px;color:black'> (See records...)</a>"
                            + "</td><td class='credits'>"
                            + val.credits
                            + "</td><td class='sms_delivered'>"
                            + "<a href='#' class='show-sms-btn' sms-status='delivered' style='color:green; font-weight: bold;'>" + val.sms_delivered + "</a>" +
                            "<a href='" + val.sms_delivered_url + "' style='font-size:10px;color:black'> (See records...)</a>"
                            + "</td><td class='percent_sent' style='color:green;'>"
                            + val.percent_sent
                            + "</td><td class='sms_pending'>"
                            + "<a href='#' class='show-sms-btn' sms-status='pending' style='color: orange; font-weight: bold;'>" + val.sms_pending + "</a>" +
                            "<a href='" + val.sms_pending_url + "' style='font-size:10px; color:black;'> (See records...)</a>"
                            + "</td><td class='sms_unknown'>"
                            + "<a href='#' class='show-sms-btn' sms-status='unknown' style='color:orange; font-weight: bold;'>" + val.sms_unknown + "</a>" +
                            "<a href='" + val.sms_unknown_url + "' style='font-size:10px;color:black;'> (See records...)</a>"
                            + "</td><td class='percent_pending' style='color:orange;'>"
                            + val.percent_pending
                            + "</td><td class='sms_undelivered'>"
                            + "<a href='#' class='show-sms-btn' sms-status='undelivered' style='color:red; font-weight: bold;'>" + val.sms_undelivered + "</a>" +
                            "<a href='" + val.sms_undelivered_url + "' style='font-size:10px;color:black;'> (See records...)</a>"
                            + "</td><td class='sms_error'>"
                            + "<a href='#' class='show-sms-btn' sms-status='error' style='color: red; font-weight: bold;'>" + val.sms_error + "</a>" +
                            "<a href='" + val.sms_error_url + "' style='font-size:10px; color:black;'> (See records...)</a>"
                            + "</td><td class='percent_unsent' style='color:red;'>"
                            + val.percent_unsent
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

            //Templates
            var size = ($('.template-filter  option:selected').size() > 0 ? "(" + $('.template-filter  option:selected').size() + ")" : '');
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Templates</strong> " + size + "</h5><ul>";
            $('.template-filter option:selected').each(function (index) {
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
            sms.get_graphs(response);
        });
    },
  close_all_sms: function () {
        $('.modal-backdrop.all-sms').fadeOut();
        $('.sms-container').fadeOut(500, function () {
            $('.sms-content').show();
            $('.sms-select-form')[0].reset();
            $('.alert').addClass('hidden');
        });
        $('.sms-all-container').fadeOut(500, function () {
            $('.sms-all-content').show();
        });
    },
    show_all_sms: function (btn, status) {

        $.ajax({
            url: helper.baseUrl + "modals/show_all_sms",
            type: "POST",
            dataType: "HTML"
        }).done(function (data) {
            var mheader = "Showing "+(status?status:'all')+" sms", $mbody = $(data), mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
            modals.load_modal(mheader, $mbody, mfooter);
            sms.show_sms(btn, status);
        });
    },
    show_sms: function (btn, status) {
        //Get sms data
        $.ajax({
            url: helper.baseUrl + "sms/get_sms_by_filter",
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize() + '&id=' + btn.closest('tr').find('.sql').text() + '&status=' + status
        }).done(function (response) {
            var tbody = '';

            if (response.data.length > 0) {
                $.each(response.data, function (key, val) {
                    var status = "glyphicon-eye-open green";
                    var message = "sms sent ";
                    switch(val.status) {
                        case "PENDING":
                            status = "glyphicon-time red";
                            message = "sms pending to send";
                            break;
                        case "UNKNOWN":
                            status = "glyphicon-question-sign orange";
                            message = "sms sending...";
                            break;
                        case "UNDELIVERED":
                            status = "glyphicon-eye-close red";
                            message = "sms undelivered";
                            break;
                        case "ERROR":
                            status = "glyphicon-warning-sign red";
                            message = "sms sent error";
                            break;
                    }
                    $record_option = '<a href="' + helper.baseUrl + "records/detail/" + val.urn + '"><span class="glyphicon glyphicon-chevron-right pull-right pointer" title="View the record" ></span></a>';
                    $view_option = '<span class="glyphicon ' + status + ' pull-right view-sms-btn pointer"  item-id="' + val.sms_id + '" title="' + message + '"></span>';
                    tbody += '<tr><td>' + val.sent_date + '</td><td>' + val.send_from + '</td><td title="' + val.send_to +'" >' + '</td><td title="' + val.text + '" >' + val.text + '</td><td>' + $view_option + '</td><td>' + $record_option + '</td></tr>';
                });
            } else if (status=='delivered') {
                tbody =  '<tr><td>No sms delivered</td></tr>';
            } else if (status=='undelivered') {
                tbody =  '<tr><td>No sms pending</td></tr>';
            } else if (status=='pending') {
                tbody =  '<tr><td>No sms undelivered</td></tr>';
            } else if (status=='unknown')       {
                tbody =  '<tr><td>No sms unknown</td></tr>';
            } else if (status=='error') {
                tbody =  '<tr><td>No sms with error</td></tr>';
            } else {
                $tbody =  '<tr><td>No sms</td></tr>';
            }
            var table = '<thead><tr><th>Date</th><th>From</th><th>To</th><th>Subject</th><th></th><th></th></tr></thead><tbody>' + tbody + '</tbody>';
            $('#sms-all-table').html(table);
        });
    },
    view_sms: function (sms_id) {
        //Get template data
        $.ajax({
            url: helper.baseUrl + 'modals/view_sms',
            dataType: "HTML",
        }).done(function (data) {
            var mheader = "View sms", $mbody = $(data), mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
            modals.load_modal(mheader, $mbody, mfooter);
            sms.show_sms_view(sms_id);
        });
    },
    show_sms_view: function (sms_id) {
        //Get template data
        $.ajax({
            url: helper.baseUrl + 'sms/get_sms',
            type: "POST",
            dataType: "JSON",
            data: {sms_id: sms_id}
        }).done(function (response) {
            var tbody =
                "<tr>" +
                "<th>Sent Date</th>" +
                "<td class='sent_date'>" + response.data.sent_date + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>From</th>" +
                "<td class='from'>" + response.data.send_from + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>To</th>" +
                "<td class='to'>" + response.data.send_to + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>Msg</th>" +
                "<td class='text'>" + response.data.text + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>Credits</th>" +
                "<td class='credits'>" + response.data.credits + "</td>" +
                "</tr>" +
                "<tr>" +
                "<tr>" +
                "<th>User</th>" +
                "<td class='name'>" + (response.data.name?response.data.name:"AUTO") + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>Status</th>" +
                "<td class='status'>" + response.data.status + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>Comments</th>" +
                "<td class='comments'>" + response.data.comments + "</td>" +
                "</tr>";
            $('#sms-view-table').html(tbody);
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
                var data_sent = new google.visualization.DataTable();
                data_sent.addColumn('string', 'Topping');
                data_sent.addColumn('number', 'Sent');

                var data_read = new google.visualization.DataTable();
                data_read.addColumn('string', 'Topping');
                data_read.addColumn('number', 'Read');

                var data_pending = new google.visualization.DataTable();
                data_pending.addColumn('string', 'Topping');
                data_pending.addColumn('number', 'Pending');

                var data_unsent = new google.visualization.DataTable();
                data_unsent.addColumn('string', 'Topping');
                data_unsent.addColumn('number', 'Unsent');

                var rows_sent = [];
                var rows_read = [];
                var rows_pending = [];
                var rows_unsent = [];

                var colors = [];

                if (response.data.length > 1) {
                    $.each(response.data, function (i, val) {
                        if (response.data.length && val.id != "TOTAL") {
                            var name = ((val.name != "All")?val.name:val.id);
                            colors.push('#' + val.colour);
                            if (parseInt(val.sms_sent) > 0) {
                                rows_sent.push([name, parseInt(val.sms_sent)]);
                            }
                            if (parseInt(val.sms_read) > 0) {
                                rows_read.push([name, parseInt(val.sms_read)]);
                            }
                            if (parseInt(val.sms_pending) > 0) {
                                rows_pending.push([name, parseInt(val.sms_pending)]);
                            }
                            if (parseInt(val.sms_unsent) > 0) {
                                rows_unsent.push([name, parseInt(val.sms_unsent)]);
                            }
                        }
                    });
                    data_sent.addRows(rows_sent);
                    data_read.addRows(rows_read);
                    data_pending.addRows(rows_pending);
                    data_unsent.addRows(rows_unsent);

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_sent'));
                    chart.draw(data_sent, {'legend': {position: 'none'},'colors': colors,'title': "sms Sent",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_read'));
                    chart.draw(data_read, {'legend': {position: 'none'},'colors': colors,'title': "sms Read",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_pending'));
                    chart.draw(data_pending, {'legend': {position: 'none'},'colors': colors,'title': "sms Pending",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_unsent'));
                    chart.draw(data_unsent, {'legend': {position: 'none'},'colors': colors,'title': "sms Unsent",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

                }
                else {
                    $('#chart_div_sent').html("No data");
                    $('#chart_div_read').html("");
                    $('#chart_div_pending').html("");
                    $('#chart_div_unsent').html("");
                }
            }
        });
    }
}
