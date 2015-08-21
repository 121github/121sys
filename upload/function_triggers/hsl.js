var campaign_functions = {
init:function(){

	
	
	campaign_functions.get_branch_info();
	$(document).on('click','#closest-branch',function(e){
		e.preventDefault();
		if($('input[name="contact_postcode"]').val()==""){
	flashalert.danger("You must capture a valid customer postcode first");	
	} else  {
		campaign_functions.get_branch_info();
	}
	});
	$(document).on('click','a.region-select',function(e){
		e.preventDefault();
		var id = $(this).attr('data-branch-id');
		campaign_functions.get_branch_info(id);
	});
	
	$(document).on('change','input[name="hub-choice"]',function(){
	 var driver_id = $(this).val();
	 $('a.filter[data-val="'+driver_id+'"]').trigger('click');
	})
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
			branch_info += "<table class='table table-condensed table-striped'><thead><tr><th>Hub</th><th>Consultant</th><th>Branch</th><th>Distance</th></tr><thead><tbody>";
			$.each(response.branches,function(i,row){
			branch_info += "<tr><td>"+row.region_name+"</td><td>"+row.consultants[0].name+"</td><td>"+row.branch_name+"</td><td>"+row.distance+"</td><td><input type='radio' name='hub-choice' data-branch='"+row.branch_id+"' data-region='"+row.region_id+"' value='"+row.drivers[0].id+"'/></td></tr>";
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
}

var quick_planner = { 
check_selections:function(driver,branch){
	if(driver>0&&branch>0){
	return true	
	} else {
	flashalert.danger("Please select a hub/branch");	
	}
	
},
load_planner:function(){
	var contact_postcode = $('input[name="contact_postcode"]').val();
	var driver = $('input[name="hub-choice"]:checked').val();
	var branch = $('input[name="hub-choice"]:checked').attr('data-branch');
	
	if(quick_planner.check_selections(driver,branch)){
	
	$.ajax({ url:helper.baseUrl+'planner/simulate_hsl_planner',
	type:"POST",
	dataType:"JSON",
	data:	{postcode:contact_postcode,driver_id:driver,branch_id:branch}
	}).done(function(response){
		
	});
	}
	
}
}
	//add function to add to planner when an appointment is added/updated


$(document).ready(function(){
	campaign_functions.init();
	//hsl requests
	$(".record-panel .panel-heading").html($(".record-panel .panel-heading").html().replace("Record Details", "Progress Summary"));
	
});