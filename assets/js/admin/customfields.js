var admin = {
    //initialize all the generic javascript datapickers etc for this page
    init: function() {
			$(document).on('change','.campaign-select',function(){
				admin.load_fields($(this).val());
			});
			$(document).on('click','#save_fields',function(e){
				e.preventDefault();
				admin.save_fields($(this).val());
			});
    },
	load_fields:function(campaign){
		$('.fields_container').find('input').val('');
		$.ajax({url:helper.baseUrl+'admin/get_custom_fields',
				type:"POST",
				data:{campaign:campaign},
				dataType:"JSON"
		}).done(function(response){
			if(response.length>0){
				$.each(response,function(i,row){
					$('.fields_container').find('#'+row.field).val(row.field_name);
					if(row.is_visible=="1"){
					$('.fields_container').find('#'+row.field+'_vis').prop('checked',true);
					} else {
					$('.fields_container').find('#'+row.field+'_vis').prop('checked',false);	
					}
					if(row.is_renewal=="1"){
					$('.fields_container').find('#'+row.field+'_ren').prop('checked',true);
					} else {
					$('.fields_container').find('#'+row.field+'_ren').prop('checked',false);	
					}
					if(row.editable=="1"){
					$('.fields_container').find('#'+row.field+'_edi').prop('checked',true);
					} else {
					$('.fields_container').find('#'+row.field+'_edi').prop('checked',false);	
					}
					if(row.is_select=="1"){
					$('.fields_container').find('#'+row.field+'_sel').prop('checked',true);
					} else {
					$('.fields_container').find('#'+row.field+'_sel').prop('checked',false);	
					}
					if(row.is_radio=="1"){
					$('.fields_container').find('#'+row.field+'_rad').prop('checked',true);
					} else {
					$('.fields_container').find('#'+row.field+'_rad').prop('checked',false);	
					}
				});
				
			}
			$('.fields_container').show();
		});
		
	},
		save_fields:function(){
		$.ajax({url:helper.baseUrl+'admin/save_custom_fields',
				type:"POST",
				data:$('form').serialize(),
				dataType:"JSON"
		}).done(function(response){
			flashalert.success("Fields saved to campaign");
		});
		
	}
	
}// JavaScript Document