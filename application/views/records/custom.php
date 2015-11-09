
<table class="table table-striped data-table table-bordered table-hover">  
        <thead> 
          <?php foreach($columns as $col){ ?>
  <th><?php echo $col['header'] ?></th>
  <?php } ?>
  </thead>
  
<tfoot>
  <tr>
          <?php foreach($columns as $col){ ?>
  <th><?php echo $col['header'] ?></th>
  <?php } ?>
  </tr>
</tfoot>
      </table>
      
      <script>
	  
	  $(document).ready( function () {
	
var table = $('.data-table').DataTable({
	 buttons: [
            'copy', 'csv', 'excel', 'print'
        ],
		colReorder: true,
            "oLanguage": {
                "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
            },
            "dom": '<"row"<"col-xs-12 col-sm-5"<"dt_info"i>r><"col-xs-12 col-sm-7"p>><"row"<"col-lg-12"t><"col-lg-12"<"pull-left"l> <"pull-left marl" B>>><"clear">',
			"lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
		"bAutoWidth": true,
	 	"processing": true,
        "serverSide": true,
		"scrollX": true,
   		//ordering:  false,
		"iDisplayLength": 10,
		stateSave: true,
        "ajax": { url:helper.baseUrl+"search/process_custom_view",
				  type:'POST',
				  beforeSend:function(){
					$('.dt_info').hide();  
				  },
				  complete:function(){ $('.dt_info').show(); }
		 		},                                                                                                                                   "createdRow": function (row, data, dataIndex) {
                    $(row).attr('data-urn', data['urn']);
					$(row).attr('data-modal', 'view-record');
					$(row).addClass('pointer');
				},
		"columns": [                                                                                           
            { "data": "campaign_name"},
            { "data": "fullname" },
            { "data": "outcome" },
            { "data": "date_updated" },
            { "data": "nextcall" }
        ],
		"columnDefs": [{
   			 "targets": [0,1,2,3,4],
   			 "data": null,
   			 "defaultContent": "na"
  					}]
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
                $icon_btn = $('<button class="btn btn-default btn-sm iconpicker record-icon" role="iconpicker" data-icon="" data-index="' + $(this).index() + '" data-iconset="fontawesome" style="color:#0066"></button>');
                $(this).html($icon_btn);
                table.get_used_icons();
            }
            else {
                var search_val = table.column($(this).index()).search();
				if(typeof search_val[0]!=="undefined"){
				var filter_val = search_val[0];	
				} else {
				var filter_val = "";	
				}
                $(this).html('<input class="dt-filter input-sm form-control" ' + filter_attribute + ' value="' + filter_val + '" />');
            }
        });
 
    // Apply the search
    table.columns().eq( 0 ).each( function ( colIdx ) {
        $('input', table.column( colIdx ).footer()).on( 'keyup change', function(){
            table
                .column(colIdx)
                .search(this.value)
                .draw();
        });
    });
	
	  //this moves the search input boxes to the top of the table
  
  var r = $('.data-table tfoot tr');
  r.find('th').each(function(){
    $(this).css('padding', 8);
  });
  $('.data-table thead').append(r);
  $('#search_0').css('text-align', 'center');
  
	
});

</script>