// JavaScript Document
$(document).ready(function () {
    maps.initialize("appointments");
    appointment.init();
});
//allow the map.js file to call a generic function to redraw the table specified here (appointment)
function map_table_reload() {
    appointment.table.columns.adjust().draw();
}

var appointment = {
    init: function () {
        this.table;

        $(document).on("click", ".group-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="group"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            maps.colour_by = $('.filter-form').find('input[name="group"]').val();
            appointment.reload_table();
        });

        appointment.reload_table();

        //Set icons used for the filter
        appointment.get_used_icons();
    },
    get_used_icons: function() {
        $.ajax({
            url: helper.baseUrl + 'records/get_used_icons',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            $('#record-icon').iconpicker('setIconset', {
                iconClass: 'fa',
                iconClassFix: '',
                icons: response.icons
            });
        });
    },
    reload_table: function () {
        var table = "<table width='100%' class='table table-striped table-bordered table-hover data-table'><thead><tr><th>Icon</th><th>Date</th><th>Company</th><th>Allocation</th><th>Created</th><th>Postcode</th></tr></thead>";
        table += "<tfoot><tr><th></th><th>Date</th><th>Company</th><th>Allocation</th><th>Created</th><th>Postcode</th></tr></tfoot></table>";

        $('#table-wrapper').html(table);
        appointment.populate_table();
    },
    populate_table: function (table_name) {
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
            "iDisplayLength": 50,
            order: [[ 1, "desc" ]],
            responsive: true,
            "ajax": {
                url: helper.baseUrl + "appointments/appointment_data",
                type: 'POST',
                beforeSend: function () {
                    $('.dt_info').hide();
                    maps.items = [];
                },
                data: function (d) {
                    d.extra_field = false;
                    d.bounds = maps.getBounds();
                    d.map = $('#map-view-toggle').prop('checked');
                    d.group = $('.filter-form').find('input[name="group"]').val();
                },
                complete: function (d) {
                    $('.dt_info').show();
                    $('.tt').tooltip();
                    //Show the appointments in the map
                    maps.showItems();
                    planner_permission = d.responseJSON.planner_permission;
                }
            },
            "deferRender": true,
            "columns": [{
                "data": "record_color",
                "orderable": false,
                render:function(e) {
                    var element_ar = e.split('/');
                    var color = element_ar[0];
                    var icon = element_ar[1];

                    if(!icon){
                        return '&nbsp;';
                    } else {
                        return '<span class="fa '+icon+'" style="font-size:20px; color: '+color+'">&nbsp;</span>';
                    }
                }
            }, {
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
            "columnDefs": [
			{"width": "20px","targets": 0},{
                "targets": [0, 1, 2, 3, 4, 5],
                "data": null,
                "defaultContent": "-"
            }],
            "createdRow": function (row, data, dataIndex) {
                $(row).attr('data-id', data['appointment_id']);
				$(row).attr('data-urn', data['urn']);
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
        $('.data-table tfoot th').each(function () {
            var title = $('.data-table thead th').eq($(this).index()).text();
			var filter_attribute = 'placeholder="Filter..."';
            if (title == "Icon") {
                var filter_attribute = "disabled";
            } 
            if (title == "Options") {
                $(this).html('');
            }
            else if (title == "Icon") {
                $('#record-icon').on('change', function (e) {
                    var icon = (e.icon=='empty'?'':e.icon);
                    appointment.table.column($(this).index()).search(icon).draw();

                });
            }
            else {
                var search_val = appointment.table.column($(this).index()).search();
                $(this).html('<input class="dt-filter form-control" '+filter_attribute+' value="' + search_val[0] + '" />');
            }
        });

        // Apply the search
        appointment.table.columns().eq(0).each(function (colIdx) {

            var run_filter = debounce(function () {
                appointment.table.column(colIdx).search(this.value).draw();
            }, 1000);

            $('input', appointment.table.column(colIdx).footer()).on('keyup change', run_filter);

        });

        //this moves the search input boxes to the top of the table
        var r = $('.data-table tfoot tr');
        r.find('th').each(function () {
            $(this).css('padding', 8);
        });
        $('.data-table thead').append(r);
        $('#search_0').css('text-align', 'center');
    }

}