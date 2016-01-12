// JavaScript Document

var hsl = {
    init: function () {

        filters.init();

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
            function (start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("click", '#webform-filter-submit', function (e) {
            e.preventDefault();
            hsl.webform_panel();
            $('#filter-right').data("mmenu").close();
        });

        hsl.appointments_panel();
        hsl.webform_panel();
    },
    /* the function for the appointments panel on the client dashboard */
    appointments_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_appointments_by_region_and_week',
            type: "POST",
            dataType: "JSON",
            data: $('#appointments-filter').serialize()
        }).done(function (response) {
            $('#appointments-panel').empty();
            var table = "<table class='table table-striped table-condensed'>";


            var thead = "<thead><tr>";
            thead += "<th>Hub</th>";
            $.each(response.weeks, function (key, val) {
                thead += "<th style='font-size: 13px; text-align: center'>" + val[0] + " - " + val[1] + "</th>";
            });
            thead += "</tr></thead>";

            var tbody = "<tbody><tr>";
            $.each(response.data, function (region_id, region) {
                tbody += "<tr>";
                tbody += "<td>" + region.region_name + "</td>";
                $.each(response.weeks, function (key, val) {
                    var url = helper.baseUrl + "search/custom/records/branch-region/" + region_id + "/start-date-appointment-from/" + val[2] + "/start-date-appointment-to/" + val[3];
                    if (region[key]) {
                        tbody += "<td style='text-align: center'><a href='" + url + "'>" + region[key]['num_appointments'] + "</a></td>";
                    }
                    else {
                        tbody += "<td style='text-align: center'><a href='" + url + "'>0</a></td>";
                    }
                });
                tbody += "</tr>";
            });
            tbody += "</tbody>";

            table += thead;
            table += tbody;
            table += "</table>";


            $('#appointments-panel').html(table);


        });
    },

    webform_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_hsl_webform_data',
            type: "POST",
            dataType: "JSON",
            data: $('.webform-filter').serialize()
        }).done(function (response) {
            $('.webform-date-range').html("["+response.date_from+" - "+response.date_to+"]");

            $('#completed-panel').find('tbody').empty();
            $('#completed-panel').find('tbody').html(
                "<tr>"+
                    "<td>"+response.webform_completed.completed + (parseInt(response.webform_completed.completed)>0?" (" + Math.round((response.webform_completed.completed * 100) / response.webform_completed.total, 2) + "%)":"")+"</td>"+
                    "<td>"+response.webform_completed.uncompleted + (parseInt(response.webform_completed.uncompleted)>0?" (" + Math.round((response.webform_completed.uncompleted * 100) / response.webform_completed.total, 2) + "%)":"")+"</td>"+
                "</tr>"
            );

            $('#hear-panel').empty();
            if (Object.keys(response.webform_hear).length>0) {
                $.each(response.webform_hear, function (key, val) {
                    var sub_hear = "";
                    $.each(val.sub_hear, function (sub_key, sub_val) {
                        sub_hear +=
                            "<tr>" +
                            "<div>" +
                            "<td>"+sub_key+"</td>" +
                            "<td>"+sub_val+" (" + Math.round((sub_val * 100) / val.count, 2) + "%)</td>" +
                            "</div>" +
                            "</tr>";
                    });

                    $('#hear-panel').append(
                        "<tr>" +
                        "<th>" +
                        "<span data-toggle='collapse' data-target='#accordion_"+key.split(' ')+"' class='clickable pointer'>" +
                        (Object.keys(val.sub_hear).length>0?'+':'') +
                        "</span>" +
                        "</th>" +
                        "<th>" +
                        key +
                        "<div id='accordion_"+key.split(' ')+"' class='collapse' style='width: 150%;'>" +
                        "<table style='width: 100%; font-size: 10px; font-weight: normal'>" +
                        sub_hear +
                        "</table>" +
                        "</div>" +
                        "</th>" +
                        "<td>" +
                        val.count + " (" + Math.round((val.count * 100) / response.webform_completed.total, 2) + "%)" +
                        "</td>" +
                        "</tr>"
                    );
                });
            }
            else {
                $('#hear-panel').html("Nothing found for that date range");
            }


            $('#source-panel').find('tbody').empty();
            if (Object.keys(response.webform_source).length>0) {
                $.each(response.webform_source, function (key, val) {

                    $('#source-panel').find('tbody').append(
                        "<tr>" +
                        "<th>"+key+"</th>" +
                        "<td>"+val + " (" + Math.round((val * 100) / response.webform_completed.total, 2) + "%)</td>" +
                        "</tr>"
                    );
                });
            }
            else {
                $('#source-panel').find('tbody').html("Nothing found for that date range");
            }
        });
    }
}