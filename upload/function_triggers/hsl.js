var campaign_functions = {
init:function(){
	campaign_functions.get_branch_info();
	$(document).on('click','#closest-branch',function(e){
		e.preventDefault();
		campaign_functions.get_branch_info();
	});
	$(document).on('click','a.region-select',function(e){
		e.preventDefault();
		var id = $(this).attr('data-branch-id');
		campaign_functions.get_branch_info(id);
	});
},
get_branch_info:function(id){
	var contact_postcode = $('input[name="contact_postcode"]').val();
	$.ajax({ url:helper.baseUrl+'ajax/get_branch_info',
	type:"POST",
	dataType:"JSON",
	data:	{postcode:contact_postcode,branch_id:id}
	}).done(function(response){
		if(response){
		var branch_info = "";	
			branch_info += "<table class='table table-condensed table-striped'><thead><tr><th>Branch</th><th>Hub</th><th>Driver</th><th>Consultant</th><th>Distance</th></tr><thead><tbody>";
			$.each(response.branches,function(i,row){
			branch_info += "<tr><td>"+row.branch_name+"</td><td>"+row.region_name+"</td><td>"+row.drivers[0].name+"</td><td>"+row.consultants[0].name+"</td><td>"+row.distance+"</td></tr>";
			});
			branch_info += "</tbody></table>";
		 $('#branch-info').html(branch_info);
		} else {
			$('#branch-info').html("<p>Please enter a contact postcode to find the closest hub, or select a hub using the options above</p>");
		}
	}).fail(function(){
		$('#branch-info').html("<p>Please enter a contact postcode to find the closest hub, or select a hub using the options above</p>");
		
	});
	
	

	
},
	//add function to add to planner when an appointment is added/updated
}

$(document).ready(function(){
	campaign_functions.init();
});