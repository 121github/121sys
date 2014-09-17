
<table class="table table-striped data-table table-bordered">  
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
		"dom": '<"top">p<"dt_info"i>rt<"bottom"lp><"clear">',
		"oLanguage": {
            "sProcessing": "<img src='"+helper.baseUrl+"assets/img/ajax-loader-bar.gif'>"
        },
		"bAutoWidth": false,
	 	"processing": true,
        "serverSide": true,
   		//ordering:  false,
		"iDisplayLength": 10,
		stateSave: true,
        "ajax": { url:helper.baseUrl+"search/process_custom_view",
				  type:'POST',
				  beforeSend:function(){
					$('.dt_info').hide();  
				  },
				  complete:function(){ $('.dt_info').show(); }
		 		},                                                                                                                                       
		"columns": [                                                                                           
            { "data": "campaign_name"},
            { "data": "fullname" },
            { "data": "outcome" },
            { "data": "date_updated" },
            { "data": "nextcall" },
            { "data": "options" }
        ],
		"columnDefs": [{
   			 "targets": [0,1,2,3,4],
   			 "data": null,
   			 "defaultContent": "na"
  					}]
	});
	
		//filterable columns
    // Setup - adds search input boxes to the footer row
    $('.data-table tfoot th').each( function () {
        var title = $('.data-table thead th').eq( $(this).index() ).text();
		if(title=="Options"){
		$(this).html( '' );
			 } else { 
		var search_val = table.column($(this).index()).search();
        $(this).html( '<input class="dt-filter form-control" placeholder="Filter..." value="'+search_val[0]+'" />' );
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