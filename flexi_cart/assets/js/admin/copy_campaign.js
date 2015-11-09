// JavaScript Document
$(document).ready(function(){
	$(document).on('click','#copycampaignsubmit',function(e){
		e.preventDefault();
		$.ajax({url: helper.baseUrl+'admin/copy_campaign',
		dataType:"JSON",
		type:"POST",
		data: $('#copycampaignform').serialize()
	}).done(function(response){
		if(response.success){
		flashalert.success("New campaign was created!");	
		} else {
		flashalert.danger(response.msg);		
		}
	});
	});
	
	$(document).on('click','#selectall',function(e){
		e.preventDefault();
		$('#copycampaignselect').selectpicker('selectAll');
	});
		$(document).on('click','#deselectall',function(e){
		e.preventDefault();
		$('#copycampaignselect').selectpicker('deselectAll');
	});
});