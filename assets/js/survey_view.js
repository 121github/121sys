// JavaScript Document

// JavaScript Document

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
        "ajax": { url:helper.baseUrl+"survey/process_view",
				  type:'POST',
				  beforeSend:function(){
					$('.dt_info').hide();  
				  },
				  complete:function(){ $('.dt_info').show(); }
		 		},                                                                                         
		"columns": [                                                                                           
            { "data": "campaign_name"},
            { "data": "name" },
            { "data": "fullname" },
            { "data": "survey_name" },
            { "data": "completed_date" },
			{ "data": "answer" },
			{ "data": "progress" },
            { "data": "options" }
        ],
		"columnDefs": [
		{ "type": "numeric", "targets": [3,4,5,6] },
		{
   			 "targets": [0,1,2,3,4,5,6],
   			 "data": null,
   			 "defaultContent": "-"
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
