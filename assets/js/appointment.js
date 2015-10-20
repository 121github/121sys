
//allow the map.js file to call a generic function to redraw the table specified here (appointment)
function initializemaps(){
   		maps.initialize("appointment");
}

function map_table_reload() {
     appointment.table.columns.adjust().draw();
	 appointment.table.columns.adjust();
}

function full_table_reload() {
    appointment.table.destroy();
    appointment.table.destroy();
    appointment.reload_table();
}

var appointment = {
    init: function () {
        this.table;
		$('#map-view-toggle').bootstrapToggle({
            onstyle: 'success',
            size: 'mini',
        }).show().bootstrapToggle('off');

	   $(document).on('change','#map-view-toggle',function(){
	       maps.showMap($(this));
            map_table_reload();
	   });
        $('#container-fluid form').find('input[name="date_from"]').val(moment().format('YYYY-MM-DD'));
        $('#container-fluid form').find('input[name="date_to"]').val(moment().add('days', 29).format('YYYY-MM-DD'));

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
                    'Next 7 Days': [moment(), moment().add('days', 6)],
                    'Next 30 Days': [moment(), moment().add('days', 29)],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Next Month': [moment().add('month', 1).startOf('month'), moment().add('month', 1).endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "02/07/2014",
                startDate: moment(),
                endDate: moment().add('days', 29)
            },
            function (start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                appointment.reload_table();
            });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

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
        var headings = "";
        column_count = new Array();
        $.each(table_columns.headings, function (i, header) {
            headings += "<th>" + header + "</th>";
            column_count[i] = i;
        });
        var table = "<table width='100%' class='table small table-striped table-bordered table-hover data-table'><thead><tr>" + headings + "</tr></thead>";
        table += "<tfoot><tr>" + headings + "</tr></tfoot></table>";

        $('#table-wrapper').html(table);
        appointment.populate_table();
    },
		  populate_table: function (table_name) {
        appointment.table = $('.data-table').DataTable({
           buttons: [
            'copy', 'csv', 'excel', 'print'
        ],
		colReorder: true,
            "oLanguage": {
                "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
            },
            "dom": '<"row"<"col-xs-12 col-sm-5"<"dt_info"i>r><"col-xs-12 col-sm-7"p>><"row"<"col-lg-12"t><"col-lg-12"<"pull-left"l> <"pull-left marl" B>>><"clear">',
			"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
			
            "width": "100%",
            "scrollX": true,
            "processing": true,
            "serverSide": true,
            "pagingType": "full",
            "iDisplayLength": 50,
            responsive: true,
            order: [[0, "desc"]],
            "ajax": {
              url: helper.baseUrl + "appointments/appointment_data",
                type: 'POST',
                beforeSend: function () {
                    $('.dt_info').hide();
                    maps.items = [];
                },
                data: function (d) {
                    d.extra_field = false;
                    d.bounds =(typeof map=="undefined"?null:maps.getBounds()),
                    d.map = $('#map-view-toggle').prop('checked');
                    d.group = $('.filter-form').find('input[name="group"]').val();
                    d.date_from = $('.filter-form').find('input[name="date_from"]').val();
                    d.date_to = $('.filter-form').find('input[name="date_to"]').val();
                },
                complete: function (d) {
                    $('.dt_info').show();
                    $('.tt').tooltip();
                    //Show the records in the map
                    maps.showItems();
                    maps.current_postcode = getCookie('current_postcode');
                    planner_permission = d.responseJSON.planner_permission;
                    maps.temp_bounds = null;

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
                $icon_btn = $('<button class="btn btn-default btn-sm iconpicker record-icon" role="iconpicker" data-icon="" data-index="' + $(this).index() + '" data-iconset="fontawesome" style="color:#0066"></button>');
                $(this).html($icon_btn);
                appointment.get_used_icons();
            }
            else {
                var search_val = appointment.table.column($(this).index()).search();
				if(typeof search_val[0]!=="undefined"){
				var filter_val = search_val[0];	
				} else {
				var filter_val = "";	
				}
                $(this).html('<input class="dt-filter input-sm form-control" ' + filter_attribute + ' value="' + filter_val + '" />');
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
		
		
		appointment.table.on('column-reorder',function(e, settings, details){
   	$.ajax({url:helper.baseUrl+'datatables/save_order',
	type:"POST",
	dataType:"JSON",
	data:{ columns: appointment.table.colReorder.order(),table:3 }
	})
	})
	},
	   get_used_icons: function () {
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
                var icon = (e.icon == 'empty' ? 'Icon' : e.icon);
                appointment.table.column($(this).attr('data-index')).search(icon).draw();
            });
        });
    }

}