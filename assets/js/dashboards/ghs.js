// JavaScript Document

var ghs = {
    init: function () {
        $(document).on('click', '#refresh-data', function () {
            ghs.data_panel(true);
        });
        $(document).on('click', '#search-address,#search-reference,#search-telephone', function (e) {
            e.preventDefault();
            var error = "";
            $.each($(this).closest('form').find('input'), function () {
                if ($(this).val() == "") {
                    error = "Please enter the " + $(this).prev('label').text();
                }
            });
            if (error == "") {
                ghs.search_record($(this));
            } else {
                flashalert.danger(error);
            }

        });
    },
    search_record: function ($btn) {
        $.ajax({
            url: helper.baseUrl + 'search/quicksearch/ghs',
            type: "POST",
            dataType: "JSON",
            data: $btn.closest('form').serialize()
        }).done(function (response) {
            var results = "", table = "";
            if (response.success) {
                table = "<table class='table table-striped table-condensed'><thead><tr><th>Data</th><th>Contact</th><th>Address</th><th>Postcode</th><th>Last Outcome</th><th>Owner</th></tr></thead>";
                $.each(response.data, function (i, row) {
                    results += "<p><span class='text-success'>The record was found!</span>";
                    table += "<tr class='pointer' data-modal='view-record' data-urn='" + row.urn + "'><td>" + row.source_name + "</td><td>" + row.name + "</td><td>" + row.add1 + "</td><td>" + row.postcode + "</td><td>" + row.outcome + "</td><td>" + row.user + "</td></tr>";
                });
                table += "</table>";
            } else {
                results = "<p class='text-danger'>No record was found, please create a new record and capture all the contact details. If it's a housing association tenant please update the new record with the call outcome <b>Query</b>. If it's a private tenant please complete the data capture form.</p>"
                results += "<a href='" + helper.baseUrl + "data/add_record' class='btn btn-info'>Create New</a>";
            }
            results += "</p>";
            $('#search-results').html(results + table);
        });

    },
    /* the function for the urgent panel on the client dashboard */
    urgent_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'trackvia/get_rebookings',
            type: "POST",
            dataType: "JSON",
            data: $('.urgent-filter').serialize(),
        }).done(function (response) {
            $('.urgent-panel').empty();
            var $urgents = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $urgents += '<li><a class="text-' + val.col + ' tt pointer" href="' + helper.baseUrl + 'records/detail/' + val.urn + '">' + val.fullname + '</a> <small>' + val.campaign_name + '</small><br><span class="small">Last called: ' + val.lastcall + '</span></li>';
                });
                $('.urgent-panel').append('<ul>' + $urgents + '</ul>');
                $('.tt').tooltip();
            } else {
                $('.urgent-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the urgent panel on the client dashboard */
    data_panel: function (tv) {
        $.ajax({
            url: helper.baseUrl + 'trackvia/get_counts',
            type: "POST",
            dataType: "JSON",
            data: {tv: tv},
            beforeSend: function () {
                if (tv) {

                    $('#refresh-data').html('<img src="' + helper.baseUrl + 'assets/img/panel-loading.gif" />');
                }
            }
        }).done(function (response) {
            $('#refresh-data').html('<span class="fa fa-refresh"></span>');
            $('.data-panel').empty();
            var $data = "";
            if (response['GHS Southway survey']) {
                var table = "<div class='table-responsive'><table class='table table-hover table-striped'><thead><tr><th>Type</th><th>Trackvia</th><th>121System</th></thead><tbody>";
                $.each(response, function (name, row) {
                    if (name == "GHS Private booked" || name == "GHS Southway booked" || name == "GHS Citywest booked") {
                        var tt = "<span class='fa fa-exclamation-circle tt' data-toggle='tooltip' data-placement='right' title='This click through will show more records because this figure is for future appointments only'></span>";
                    } else {
                        var tt = "";
                    }
                    if (row.trackvia) {
                        var tv = row.trackvia;
                    } else {
                        var tv = "-";
                    }
                    table += '<tr><td>' + name + '</td><td>' + tv + '</td><td><a class="pointer" href="' + helper.baseUrl + 'search/custom/records/source/' + row.source + '/">' + row.one2one + '</a> ' + tt + '</td></tr>';
                });
                table += "</tbody></table>";
                $('.data-panel').html(table);
                $('.tt').tooltip();
            } else {
                $('.data-panel').append('There was an error retrieving the data');
            }
        });
    }
}