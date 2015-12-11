// JavaScript Document

// JavaScript Document
$(document).ready(function () {
    email.init()
});

var email = {
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
                email.get_outcomes_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            email.email_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            email.get_outcomes_filter();
        });

        $(document).on("click", ".refresh-data", function (e) {
            e.preventDefault();
            email.email_panel();
        });

        $(document).on('click', '.show-emails-btn', function (e) {
            e.preventDefault();
            email.show_emails($(this), $(this).attr('email-sent'), $(this).attr('email-read'), $(this).attr('email-pending'));
        });
        $(document).on('click', '.view-email-btn', function (e) {
            e.preventDefault();
            email.view_email($(this).attr('item-id'));
        });
        email.email_panel();
    },
    email_panel: function () {
        var graph_color_display = (typeof $('.graph-color').css('display') != 'undefined' ? ($('.graph-color').css('display') == 'none' ? 'none' : 'inline-block') : 'none');

        $.ajax({
            url: helper.baseUrl + 'reports/email_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('.email-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            var $row = "";
            $tbody = $('.email-data .ajax-table').find('tbody');
            $tbody.empty();
            if (response.success) {
                $('#email-name').text(response.email);
                $.each(response.data, function (i, val) {
                    if (response.data.length) {

                        var style = "";
                        var success = "";
                        if (val.total_emails > 0) {
                            success = "success";
                        }
                        else if (val.campaign == "TOTAL") {
                            style = "font-weight:bold;";
                        }
                        else {
                            success = "warning";
                        }

                        var colour = ((val.id != 'TOTAL')?"</td><td style='text-align: right'><span class='graph-color fa fa-circle' style='display:" + graph_color_display + "; color:#" + val.colour + "'> </span>":"");

                        $tbody
                            .append("<tr class='" + success + "' style='" + style + "'><td class='id'>"
                            + val.id + "<span class='sql' style='display:none'>" + val.sql + "</span>"
                            + "</td><td class='name'>"
                            + val.name
                            + "</td><td class='emails_sent'>"
                            + "<a href='#' class='show-emails-btn' email-read='0' email-sent='1' style='font-weight: bold; font-size: 19px;'>" + val.emails_sent + "</a>" +
                            "<a href='" + val.emails_sent_url + "' style='font-size:10px;color:black'> (See records...)</a>"
                            + "</td><td class='emails_read'>"
                            + "<a href='#' class='show-emails-btn' email-read='1' email-sent='1' style='color:green; font-weight: bold;'>" + val.emails_read + "</a>" +
                            "<a href='" + val.emails_read_url + "' style='font-size:10px;color:black;'> (See records...)</a>"
                            + "</td><td class='percent_read' style='color:green;'>"
                            + val.percent_read
                            + "</td><td class='emails_pending'>"
                            + "<a href='#' class='show-emails-btn' email-sent='0' email-pending='1' style='color: orange; font-weight: bold;'>" + val.emails_pending + "</a>" +
                            "<a href='" + val.emails_pending_url + "' style='font-size:10px; color:black;'> (See records...)</a>"
                            + "</td><td class='percent_pending' style='color:orange;'>"
                            + val.percent_pending
                            + "</td><td class='emails_unsent'>"
                            + "<a href='#' class='show-emails-btn' email-sent='0' style='color: red; font-weight: bold;'>" + val.emails_unsent + "</a>" +
                            "<a href='" + val.emails_unsent_url + "' style='font-size:10px; color:black;'> (See records...)</a>"
                            + "</td><td class='percent_unsent' style='color:red;'>"
                            + val.percent_unsent
                            + colour
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

            $('#filters').html(filters);

            //////////////////////////////////////////////////////////
            //Graphics/////////////////////////////////////////////////
            //////////////////////////////////////////////////////////
            email.get_graphs(response);
        });
    },
    show_emails: function (btn, sent, read, pending) {

        $.ajax({
            url: helper.baseUrl + "modals/show_all_email",
            type: "POST",
            dataType: "HTML"
        }).done(function (data) {
            var mheader = "Showing all emails", $mbody = $(data), mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
            modals.load_modal(mheader, $mbody, mfooter);
            email.load_emails(btn, sent, read, pending);
        });
    },
    load_emails: function (btn, sent, read, pending) {

        //Get emails data
        $.ajax({
            url: helper.baseUrl + "email/get_emails_by_filter",
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize() + '&id=' + btn.closest('tr').find('.sql').text() + '&sent=' + sent + '&read=' + read + '&pending=' + pending
        }).done(function (response) {
            var tbody = "";

            if (response.data.length > 0) {
                $.each(response.data, function (key, val) {
                    var status = (val.status != true) ? "red" : ((val.read_confirmed == 1) ? "green" : "");
                    var message = (val.status != true) ? "Email no sent" : ((val.read_confirmed == 1) ? "Email read confirmed " + " (" + val.read_confirmed_date + ")" : "Waiting email read confirmation");
                    var send_to = (val.send_to.length > 15) ? val.send_to.substring(0, 15) + '...' : val.send_to;
                    var subject = (val.subject.length > 20) ? val.subject.substring(0, 20) + '...' : val.subject;
                    $record_option = '<a href="' + helper.baseUrl + "records/detail/" + val.urn + '"><span class="glyphicon glyphicon-chevron-right pull-right pointer" title="View the record" ></span></a>';
                    $view_option = '<span class="glyphicon glyphicon-eye-open ' + status + ' pull-right view-email-btn pointer"  item-id="' + val.email_id + '" title="' + message + '"></span>';
                    tbody += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="' + val.send_to + '" >' + send_to + '</td><td title="' + val.subject + '" >' + subject + '</td><td>' + $view_option + '</td><td>' + $record_option + '</td></tr>';
                });
                var table = '<thead><tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr></thead><tbody>' + tbody + '</tbody>';
                $('#email-all-table').html(table);
            } else if ((read == '1') && (sent == '1')) {
                modal_body.html('<p>No emails read</p>');
            } else if ((read == '0') && (sent == '1')) {
                modal_body.html('<p>No emails sent</p>');
            } else if (sent == '0' && pending) {
                modal_body.html('<p>No emails pending</p>');
            } else if (sent == '0') {
                modal_body.html('<p>No emails unsent</p>');
            }
        });
    },
    view_email: function (email_id) {
        //Get template data
        $.ajax({
            url: helper.baseUrl + 'modals/view_email',
            dataType: "HTML",
        }).done(function (data) {
            var mheader = "View Email", $mbody = $(data), mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
            modals.load_modal(mheader, $mbody, mfooter);
            email.load_email_view(email_id);
        });
    },
    load_email_view: function (email_id) {
        //Get template data
        $.ajax({
            url: helper.baseUrl + 'email/get_email',
            type: "POST",
            dataType: "JSON",
            data: {email_id: email_id}
        }).done(function (response) {
            var message = (response.data.status == true) ? "<th colspan='2' style='color:green'>This email was sent successfuly</th>" : "<th colspan='2' style='color:red'>This email was not sent</th>"
            var status = (response.data.status == true) ? "Yes" : "No";
            var read_confirmed = (response.data.read_confirmed == 1) ? "Yes " + " (" + response.data.read_confirmed_date + ")" : "No";

            tbody = "<tr>" +
                message +
                "</tr>" +
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
                "<th>CC</th>" +
                "<td class='cc'>" + response.data.cc + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>BCC</th>" +
                "<td class='bcc'>" + response.data.bcc + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>Subject</th>" +
                "<td class='subject'>" + response.data.subject + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th colspan=2>Body</th>" +
                "</tr>" +
                "<td colspan=2 class='body'>" + response.data.body + "</td>" +
                "</tr>" +
                "<th>Sent</th>" +
                "<td class='status'>" + status + "</td>" +
                "</tr>" +
                "<th>Read Confirmed</th>" +
                "<td class='read_confirmed'>" + read_confirmed + "</td>" +
                "</tr>"
            if (response.attachments.length > 0) {
                body += "<tr>" +
                    "<th colspan=2>Attachments</th>" +
                    "</tr>";
                $.each(response.attachments, function (key, val) {
                    body += "<tr>" +
                        "<td colspan='2' class='attachments'><a target='_blank' href='" + val.path + "'>" + val.name + "</td>" +
                        "</tr>";
                });
            }
            $('#email-view-table').html(tbody);
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
                            if (parseInt(val.emails_sent) > 0) {
                                rows_sent.push([name, parseInt(val.emails_sent)]);
                            }
                            if (parseInt(val.emails_read) > 0) {
                                rows_read.push([name, parseInt(val.emails_read)]);
                            }
                            if (parseInt(val.emails_pending) > 0) {
                                rows_pending.push([name, parseInt(val.emails_pending)]);
                            }
                            if (parseInt(val.emails_unsent) > 0) {
                                rows_unsent.push([name, parseInt(val.emails_unsent)]);
                            }
                        }
                    });
                    data_sent.addRows(rows_sent);
                    data_read.addRows(rows_read);
                    data_pending.addRows(rows_pending);
                    data_unsent.addRows(rows_unsent);

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_sent'));
                    chart.draw(data_sent, {'legend': {position: 'none'},'colors': colors,'title': "Emails Sent",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_read'));
                    chart.draw(data_read, {'legend': {position: 'none'},'colors': colors,'title': "Emails Read",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_pending'));
                    chart.draw(data_pending, {'legend': {position: 'none'},'colors': colors,'title': "Emails Pending",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

                    var chart = new google.visualization.PieChart(document.getElementById('chart_div_unsent'));
                    chart.draw(data_unsent, {'legend': {position: 'none'},'colors': colors,'title': "Emails Unsent",'width': 300,'height': 300,'hAxis': {textPosition: 'none'},curveType: 'function'});

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