// JavaScript Document

function table_columns(){
	 var form = "";
		$('.save-modal').show();
		$('.confirm-modal').hide();
	   $('.modal-title').text('Choose display data');
	   $.ajax({ url:helper.baseUrl+'ajax/get_table_columns',
	   type:"POST",
	   dataType:"JSON",
	   }).done(function(response){
		  form ="<form>";
		  $.each(response,function(key,fields){
			 form += "<p>"+key+"</p>"; 
			 form += "<select class='fieldpicker' multiple title='Select the "+key+"'>";
			 $.each(fields,function(key,field){
				 form += "<option value='"+key+"'>"+field+"</p>";
			 });
			  form += "</select>";
		  });
		  form +="</form>";
		  
		  
		 $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html(form);
		$('.fieldpicker').selectpicker();
        $(".save-modal").off('click').show();
        $('.save-modal').on('click', function(e) {
           console.log('load');
        });
		 
		 
		 
	   });
	   
        
}

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
        "ajax": { url:helper.baseUrl+"records/process_view",
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
  
  $(document).on('click','.reset-dt-filter',function(table){
	 localStorage.removeItem("DataTables_DataTables_Table_0_/thinkmoney-nps/records/view"); 
	 table.search().draw();
 
  });

});


