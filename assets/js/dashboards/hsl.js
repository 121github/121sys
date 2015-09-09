// JavaScript Document

var hsl = {
    init: function () {

        $('.daterange').daterangepicker({
                opens: "left",
                singleDatePicker: true,
                showDropdowns: true,
                format: 'DD/MM/YYYY',
                minDate: "01/01/2010",
                startDate: moment()
            },
            function (start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('D MMMM YYYY'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                hsl.appointments_panel();
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        hsl.appointments_panel();
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
    }
}