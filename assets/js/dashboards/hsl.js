// JavaScript Document

var hsl = {
    init: function () {
        hsl.appointments_panel();
    },
    /* the function for the appointments panel on the client dashboard */
    appointments_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_appointments_by_region_and_week',
            type: "POST",
            dataType: "JSON",
            data: {}
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
                    if (region[key]) {
                        tbody += "<td style='text-align: center'>" + region[key]['num_appointments'] + "</td>";
                    }
                    else {
                        tbody += "<td style='text-align: center'>0</td>";
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
    }
}