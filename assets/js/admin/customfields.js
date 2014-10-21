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
					$('.fields_container').find('input[name="'+row.field+'"]').val(row.field_name);
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