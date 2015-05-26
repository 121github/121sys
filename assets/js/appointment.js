// JavaScript Document
$(document).ready(function() {
    maps.initialize("appointments");
    appointment.init();
});
//allow the map.js file to call a generic function to redraw the table specified here (appointment)
function map_table_reload() {
    appointment.table.draw();
}

var appointment = {
    init: function() {
        this.table;
        appointment.reload_table();
    },
    reload_table: function() {
        var table = "<table width='100%' class='table table-striped table-bordered table-hover data-table'><thead><tr><th>Date</th><th>Company</th><th>Allocation</th><th>Created</th><th>Postcode</th></tr></thead>";
        table += "<tfoot><tr><th>Date</th><th>Company</th><th>Allocation</th><th>Created</th><th>Postcode</th></tr></tfoot></table>";

        $('#table-wrapper').html(table);
        appointment.populate_table();
    },
    populate_table: function(table_name) {
        appointment.table = $('.data-table').DataTable({
            "oLanguage": {
                "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
            },
            "dom": '<"row"<"col-xs-12 col-sm-5"<"dt_info"i>r><"col-xs-12 col-sm-7"p>><"row"<"col-lg-12"t>><"clear">',
            "autoWidth": true,
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "pagingType": "full",
            "iDisplayLength": 10,
            responsive: true,
            "ajax": {
                url: helper.baseUrl + "appointments/appointment_data",
                type: 'POST',
                beforeSend: function() {
                    $('.dt_info').hide();
                    maps.appointments = [];
                },
                data: function(d) {
                    d.extra_field = false;
                    d.bounds = maps.getBounds();
                    d.map = $('#map-view-toggle').prop('checked');
                },
                complete: function(d) {
                    $('.dt_info').show();
                    $('.tt').tooltip();
                    //Show the appointments in the map
                    maps.showItems();
                }
            },
            "deferRender": true,
            "columns": [{
                "data": "start"
            }, {
                "data": "name"
            }, {
                "data": "attendee"
            }, {
                "data": "date_added"
            }, {
                "data": "postcode"
            }],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4],
                "data": null,
                "defaultContent": "-"
            }],
            "createdRow": function(row, data, dataIndex) {
                $(row).attr('data-id', data['appointment_id']);
                $(row).attr('data-modal', 'view-appointment');
                $(row).attr('postcode', data['postcode']);
                $(row).addClass('pointer');
                if (data['change_type'] == "delete") {
                    $(row).addClass('danger');
                }
                maps.items.push(data);
            }
        });

        //filterable columns
        // Setup - adds search input boxes to the footer row
        $('.data-table tfoot th').each(function() {
            var title = $('.data-table thead th').eq($(this).index()).text();
            if (title == "Options") {
                $(this).html('');
            } else {
                var search_val = appointment.table.column($(this).index()).search();
                $(this).html('<input class="dt-filter form-control" placeholder="Filter..." value="' + search_val[0] + '" />');
            }
        });

        // Apply the search
        appointment.table.columns().eq(0).each(function(colIdx) {

            var run_filter = debounce(function() {
                appointment.table.column(colIdx).search(this.value).draw();
            }, 1000);

            $('input', appointment.table.column(colIdx).footer()).on('keyup change', run_filter);

        });

        //this moves the search input boxes to the top of the table
        var r = $('.data-table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', 8);
        });
        $('.data-table thead').append(r);
        $('#search_0').css('text-align', 'center');
    }

}