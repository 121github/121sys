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
            sms.show_all_sms($(this), $(this).attr('sms-status'));
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
                    var $delete_option = "";
                    if (helper.permissions['delete sms'] > 0) {
                        $delete_option = '<span class="glyphicon glyphicon-trash pull-right del-sms-btn marl" data-target="#modal" item-modal="1" item-id="' + val.sms_id + '" title="Delete sms" ></span>';
                    }
                    $view_option = '<span class="glyphicon ' + status + ' pull-right view-sms-btn pointer"  item-id="' + val.sms_id + '" title="' + message + '"></span>';
                    tbody += '<tr><td>' + val.sent_date + '</td><td>' + val.send_from + '</td><td title="' + val.send_to + '" >' + val.send_to + '</td><td title="' + val.text + '" >' + val.text + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
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
                "<td class='body'>" + response.data.text + "</td>" +
                "</tr>" +
                "<tr>" +
                "<th>Status</th>" +
                "<td class='status'>" + response.data.status + "</td>" +
                "</tr>" +
                "<tr>" +
                "<tr>" +
                "<th>User</th>" +
                "<td class='body'>" + (response.data.name?response.data.name:"AUTO") + "</td>" +
                "</tr>";
            $('#sms-view-table').html(tbody);
        });
    }
}