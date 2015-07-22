// JavaScript Document

// JavaScript Document
$(document).ready(function () {
    sms.init()
});

var sms = {
    init: function () {
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
                sms.sms_panel()
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("click", ".template-filter", function (e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="template"]').val($(this).attr('id'));
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");
            sms.sms_panel()
        });

        $(document).on("click", ".campaign-filter", function (e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");
            sms.sms_panel()
        });
        $(document).on("click", ".agent-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="team"]').val('');
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");
            sms.sms_panel()
        });
        $(document).on("click", ".team-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
            $(this).closest('form').find('input[name="agent"]').val('');
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");
            sms.sms_panel()
        });
        $(document).on("click", ".source-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="source"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");
            sms.sms_panel()
        });
        $(document).on('click', '.show-sms-btn', function (e) {
            e.preventDefault();
            sms.show_sms($(this), $(this).attr('sms-status'));
        });
        $(document).on('click', '.close-sms-all', function (e) {
            e.preventDefault();
            sms.close_all_sms($(this));
        });
        $(document).on('click', '.view-sms-btn', function (e) {
            e.preventDefault();
            sms.view_sms($(this).attr('item-id'));
        });
        $(document).on('click', '.close-sms', function (e) {
            e.preventDefault();
            sms.close_sms($(this));
        });
        sms.sms_panel()
    },
    sms_panel: function (sms) {
        $.ajax({
            url: helper.baseUrl + 'reports/sms_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
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
    show_sms: function (btn, status) {
        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;
        $('<div class="modal-backdrop all-sms in"></div>').appendTo(document.body).hide().fadeIn();
        $('.sms-all-container').find('.sms-all-panel').show();
        $('.sms-all-content').show();
        $('.sms-all-container').fadeIn()
        $('.sms-all-container').animate({
            width: '600px',
            left: moveto,
            top: '10%'
        }, 1000);
        //Get sms data
        $.ajax({
            url: helper.baseUrl + "sms/get_sms_by_filter",
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize() + '&id=' + btn.closest('tr').find('.sql').text() + '&status=' + status
        }).done(function (response) {
            var $thead = $('.sms-all-table').find('thead');
            $thead.empty();

            var $tbody = $('.sms-all-table').find('tbody');
            $tbody.empty();
            var body = "";

            if (response.data.length > 0) {
                $.each(response.data, function (key, val) {
                    var status = (val.status != true) ? "red" : ((val.read_confirmed == 1) ? "green" : "");
                    var message = (val.status != true) ? "Sms no sent" : ((val.read_confirmed == 1) ? "Sms read confirmed " + " (" + val.read_confirmed_date + ")" : "Waiting sms read confirmation");
                    var send_to = (val.send_to.length > 15) ? val.send_to.substring(0, 15) + '...' : val.send_to;
                    var subject = (val.subject.length > 20) ? val.subject.substring(0, 20) + '...' : val.subject;
                    $record_option = '<a href="' + helper.baseUrl + "records/detail/" + val.urn + '"><span class="glyphicon glyphicon-chevron-right pull-right pointer" title="View the record" ></span></a>';
                    $view_option = '<span class="glyphicon glyphicon-eye-open ' + status + ' pull-right view-sms-btn pointer"  item-id="' + val.sms_id + '" title="' + message + '"></span>';
                    body += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="' + val.send_to + '" >' + send_to + '</td><td title="' + val.subject + '" >' + subject + '</td><td>' + $view_option + '</td><td>' + $record_option + '</td></tr>';
                });
                $thead.append('<tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr>');
                $tbody.append(body);
            } else if ((read == '1') && (sent == '1')) {
                $tbody.append('<p>No sms read</p>');
            } else if ((read == '0') && (sent == '1')) {
                $tbody.append('<p>No sms sent</p>');
            } else if (sent == '0') {
                $tbody.append('<p>No sms unsent</p>');
            }
        });
    },
    view_sms: function (sms_id) {
        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;
        $('<div class="modal-backdrop sms in"></div>').appendTo(document.body).hide().fadeIn();
        $('.sms-view-container').find('.edit-panel').show();
        $('.sms-view-content').show();
        $('.sms-view-container').fadeIn()
        $('.sms-view-container').animate({
            width: '600px',
            left: moveto,
            top: '10%'
        }, 1000);
        //Get template data
        $.ajax({
            url: helper.baseUrl + 'sms/get_sms',
            type: "POST",
            dataType: "JSON",
            data: {sms_id: sms_id}
        }).done(function (response) {
            var message = (response.data.status == true) ? "<th colspan='2' style='color:green'>This sms was sent successfuly</th>" : "<th colspan='2' style='color:red'>This sms was not sent</th>"
            var status = (response.data.status == true) ? "Yes" : "No";
            var read_confirmed = (response.data.read_confirmed == 1) ? "Yes " + " (" + response.data.read_confirmed_date + ")" : "No";
            var $tbody = $('.sms-view-table').find('tbody');
            $tbody.empty();
            body = "<tr>" +
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
            $tbody
                .append(body);
        });
    },
    close_sms: function () {
        $('.modal-backdrop.sms').fadeOut();
        $('.sms-container').fadeOut(500, function () {
            $('.sms-content').show();
            $('.sms-select-form')[0].reset();
            $('.alert').addClass('hidden');
        });
        $('.sms-view-container').fadeOut(500, function () {
            $('.sms-view-content').show();
        });
    }
}