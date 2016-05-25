function full_table_reload() {
    view.table.destroy();
    view.table.destroy();
    view.reload_table();
}

var view = {
    init: function() {
        this.table;
		this.planner_permission = false;

        $(document).on("click", ".group-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="group"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");
            maps.colour_by = $('.filter-form').find('input[name="group"]').val();
            view.table.columns.adjust().draw();
        });
		view.reload_table();
    },
    get_used_icons: function() {
        $.ajax({
            url: helper.baseUrl + 'records/get_used_icons',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            $('.record-icon').iconpicker();
            $('.record-icon').iconpicker('setIconset', {
                iconClass: 'fa',
                iconClassFix: '',
                icons: response.icons
            }).change(function(e) {
                var icon = (e.icon == 'empty' ? 'Icon' : e.icon);
                view.table.column($(this).attr('data-index')).search(icon).draw();
            });
        });
    },
    reload_table: function() {
        var headings = "";
        column_count = new Array();
        $.each(table_columns.headings, function(i, header) {
            headings += "<th>" + header + "</th>";
            column_count[i] = i;
        });
        var table = "<table width='100%' class='table small table-striped table-bordered table-hover data-table'><thead><tr>" + headings + "</tr></thead>";
        table += "<tfoot><tr>" + headings + "</tr></tfoot></table>";

        $('#view-container').html(table);
        view.populate_table();
    },
	clear_filters:function(){
			view.table.columns().eq(0).each(function(colIdx) {
			$('thead input').val('');
			view.table.column(colIdx).search('');	
			});
		},
    populate_table: function(table_name) {
        var start_time = new Date().getTime();
        view.table = $('.data-table').DataTable({
            buttons: [
                'copy', 'csv', 'excel', 'print'
            ],
            colReorder: true,
            "oLanguage": {
                "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
            },
            "dom": '<"row top-row"<"col-xs-12 col-sm-5"<"dt_info"i>r><"col-xs-12 col-sm-7"p>><"row"<"col-lg-12"t>><"row bottom-row"<"col-lg-12"<"pull-left"l> <"pull-right marl" B>>><"clear">',
            "lengthMenu": [
                [10, 25, 50, 100, 1000],
                [10, 25, 50, 100, 1000]
            ],
            "width": "100%",
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "pagingType": "full",
            "iDisplayLength": 100,
            responsive: true,
            order: [
                [0, "desc"]
            ],
            "ajax": {
                url: helper.baseUrl + process_url,
                type: 'POST',
                beforeSend: function() {
                    $('.dt_info div').empty()
                    start_time = new Date().getTime();
					$('.loading-overlay').fadeIn();
                },
                data: function(d) {
                    d.extra_field = false;
                    d.group = $('.filter-form').find('input[name="group"]').val();
					d.bounds = false;
					d.map = false;
					d.date_from = $('.filter-form').find('input[name="date_from"]').val();
                    d.date_to = $('.filter-form').find('input[name="date_to"]').val();
                },
                error: function(xhr, error, thrown) {
                    if (error == "parsererror") {
                        alert("Oops! There was an error parsing the data.");
                    }
                },
                complete: function(d) {
                    request_time = (new Date().getTime() - start_time) / 1000;
                    $('.dt_info').show().find('div').append(' <span class="tt" data-html="true" data-toggle="tooltip" data-placement="bottom" title="Process time ' + Number(d.responseJSON.process_time) + ' seconds<br>Query time ' + Number(d.responseJSON.query_time) + ' seconds<br>Request time ' + request_time + ' seconds"><span class="glyphicon glyphicon-info-sign"></span></span>');
                    $('.tt').tooltip();
					$('.loading-overlay').fadeOut();
                    //Show search options if some filter exist
                    if (view.has_filter) {
                        $('.dataTables_info').append("<span class='glyphicon glyphicon-filter red modal-show-filter-options pointer'></span>");
                    }
                }
				
            },
            "deferRender": true,
            "columns": table_columns.columns,
            "columnDefs": [{
                "targets": column_count,
                "data": null,
                "defaultContent": "-"
            }],
            "createdRow": function(row, data, dataIndex) {
				if(page_name=="records"){
				$(row).attr('data-urn', data.urn);
                $(row).attr('data-id', data.marker_id);
                $(row).attr('data-modal', 'view-record');
				} else if(page_name=="appointment"){
				$(row).attr('data-urn', data.urn);
                $(row).attr('data-id', data.marker_id);
				$(row).attr('data-modal', 'view-appointment');	
				}
                $(row).attr('postcode', data['postcode']);
                $(row).addClass('pointer');
                if (data['change_type'] == "delete") {
                    $(row).addClass('danger');
                }
				if(page_name=="recordings"){
                $(row).attr('data-path', data.filepath);
				$(row).attr('data-id', data.id);
				}
            }
        });

			

        //filterable columns
        $('.data-table tfoot th').each(function() {
            var title = $('.data-table thead th').eq($(this).index()).text();
            var filter_attribute = 'placeholder="Filter..."';
            if (title == "Icon"||title== "Distance") {
                var filter_attribute = "disabled";
            }

            if (title == "Options") {
                vbp
                $(this).html('');
            } else if (title == "Icon") {
                $icon_btn = $('<button class="btn btn-default btn-sm iconpicker record-icon" role="iconpicker" data-icon="" data-index="' + $(this).index() + '" data-iconset="fontawesome" style="color:#0066"></button>');
                $(this).html($icon_btn);
                view.get_used_icons();
            } else {
                var search_val = view.table.column($(this).index()).search();
                if (typeof search_val[0] !== "undefined") {
                    var filter_val = search_val[0];
                } else {
                    var filter_val = "";
                }
                $(this).html('<input class="dt-filter input-sm form-control" ' + filter_attribute + ' value="' + filter_val + '" />');
            }
        });

	

        // Apply the search
        view.table.columns().eq(0).each(function(colIdx) {
            var run_filter = debounce(function() {
				var order = view.table.colReorder.order();
                view.table.column(order[colIdx]).search(this.value).draw();
            }, 1000);
            $('input', view.table.column(colIdx).footer()).on('keyup change', run_filter);
        });


        $("div.dataTables_scrollFootInner table tfoot tr").appendTo('div.dataTables_scrollHeadInner table thead');

        $('#search_0').css('text-align', 'center');

        view.table.on('column-reorder', function(e, settings, details) {
            $.ajax({
                url: helper.baseUrl + 'datatables/save_order',
                type: "POST",
                dataType: "JSON",
                data: {
                    columns: view.table.colReorder.order(),
                    view: table_columns.view_id
                }
            }).done(function(response){
				
			});
        });
    },

}