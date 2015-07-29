
//allow the map.js file to call a generic function to redraw the table specified here (appointment)
function map_table_reload() {
	view_records.table.columns.adjust().draw();
}

function full_table_reload(){
	view_records.table.destroy();
	view_records.table.destroy();
	view_records.reload_table();
}

var view_records = {
    init: function() {
        this.table;

        $(document).on("click", ".group-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="group"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            maps.colour_by = $('.filter-form').find('input[name="group"]').val();
            view_records.table.columns.adjust().draw();
        });

        view_records.reload_table();
    },
    get_used_icons: function() {
        $.ajax({
            url: helper.baseUrl + 'records/get_used_icons',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
			 $('.record-icon').iconpicker();
            $('.record-icon').iconpicker('setIconset', {
                iconClass: 'fa',
                iconClassFix: '',
                icons: response.icons
            }).change(function (e) {
                    var icon = (e.icon=='empty'?'Icon':e.icon);
                   view_records.table.column($(this).attr('data-index')).search(icon).draw();
                });
        });
    },
    reload_table: function() {
		var headings = "";
		column_count = new Array();
		$.each(table_columns.headings,function(i,header){
			headings += "<th>"+header+"</th>";
			column_count[i] = i;
		});
        var table = "<table width='100%' class='table small table-striped table-bordered table-hover data-table'><thead><tr>"+headings+"</tr></thead>";
        table += "<tfoot><tr>"+headings+"</tr></tfoot></table>";

        $('#table-wrapper').html(table);
        view_records.populate_table();
    },
    populate_table: function(table_name) {
        view_records.table = $('.data-table').DataTable({
            "oLanguage": {
                "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
            },
            "dom": '<"row"<"col-xs-12 col-sm-5"<"dt_info"i>r><"col-xs-12 col-sm-7"p>><"row"<"col-lg-12"t>><"clear">',
            "width": "100%",
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "pagingType": "full",
            "iDisplayLength": 50,
            responsive: true,
            order: [[ 0, "desc" ]],
            "ajax": {
                url: helper.baseUrl + "records/process_view",
                type: 'POST',
                beforeSend: function() {
                    $('.dt_info').hide();
                    maps.items = [];
                },
                data: function(d) {
                    d.extra_field = false;
                    d.bounds = (maps.temp_bounds?maps.temp_bounds:maps.getBounds());
                    d.map = $('#map-view-toggle').prop('checked');
                    d.group = $('.filter-form').find('input[name="group"]').val();
                },
                complete: function(d) {
                    $('.dt_info').show();
                    $('.tt').tooltip();
                    //Show the records in the map
                    maps.showItems();
                    maps.current_postcode = d.responseJSON.current_postcode;
                    planner_permission = d.responseJSON.planner_permission;
                    maps.temp_bounds = null;
					
                    //Show search options if some filter exist
                    if (view_records.has_filter) {
                        $('.dataTables_info').append("<span class='glyphicon glyphicon-filter red modal-show-filter-options pointer'></span>");
                    }
                }
            },
            "deferRender": true,
            "columns": table_columns.columns,
            "columnDefs": [
			{
                "targets": column_count,
                "data": null,
                "defaultContent": "-"
            }],
            "createdRow": function(row, data, dataIndex) {
                $(row).attr('data-urn', data.urn);
				$(row).attr('data-id', data.urn);
                $(row).attr('data-modal', 'view-record');
                $(row).attr('postcode', data['postcode']);
                $(row).addClass('pointer');
                if (data['change_type'] == "delete") {
                    $(row).addClass('danger');
                }
                maps.items.push(data);
                //$(row).attr('data-id', records.length - 1);
            }
        });

        //filterable columns
        // Setup - adds search input boxes to the footer row
        $('.data-table tfoot th').each(function() {
            var title = $('.data-table thead th').eq($(this).index()).text();
			var filter_attribute = 'placeholder="Filter..."';
            if (title == "Icon") {
                var filter_attribute = "disabled";
            } 

            if (title == "Options") {
                $(this).html('');
            }
            else if (title == "Icon") {
				$icon_btn = $('<button class="btn btn-default btn-sm iconpicker record-icon" role="iconpicker" data-icon="" data-index="'+$(this).index()+'" data-iconset="fontawesome" style="color:#0066"></button>');
				$(this).html($icon_btn);
				view_records.get_used_icons();
            }
            else {
                var search_val = view_records.table.column($(this).index()).search();
                $(this).html('<input class="dt-filter input-sm form-control" '+filter_attribute+' value="' + search_val[0] + '" />');
            }
        });

        // Apply the search
        view_records.table.columns().eq(0).each(function(colIdx) {



            var run_filter = debounce(function() {
                view_records.table.column(colIdx).search(this.value).draw();
            }, 1000);

            $('input', view_records.table.column(colIdx).footer()).on('keyup change', run_filter);

        });

     

  
$("div.dataTables_scrollFootInner table tfoot tr").appendTo('div.dataTables_scrollHeadInner table thead');

$('#search_0').css('text-align', 'center');
	
    }



}
