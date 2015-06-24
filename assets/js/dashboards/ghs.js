// JavaScript Document

var ghs = {
	init:function(){
		$(document).on('click','#search-address,#search-reference',function(e){
			e.preventDefault();
			ghs.search_record($(this));
		});
	},
search_record:function($btn){
	$.ajax({url:helper.baseUrl+'search/search_address',
	type:"POST",
	dataType:"JSON",
	data: $btn.closest('form').serialize()
	}).done(function(response){
		var results;
		if(response.urn){
		results = "<p class='text-success'>Record was found</p>";
		results += "<a href='"+helper.baseUrl+"records/detail/"+response.urn+"' class='btn btn-success'>View</a>";
		} else {
		results = "<p class='text-danger'>No record was found, if this is a housing association tenant they should call 08002483743. If it is a private tenant you can create a new record.</p>"	
		results += "<a href='"+helper.baseUrl+"data/add_record' class='btn btn-info'>Create New</a>";
		}
		$('#search-results').html(results);
	});
	
}
	
}