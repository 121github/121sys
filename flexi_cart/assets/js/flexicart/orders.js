var orders = {
 
	init:function(){
		$(document).on('change','#category-select',function(){
			orders.get_subcategories($('#category-select').val());
		});
		$(document).on('change','#subcategory-select',function(){
			orders.get_items();
		});
		$(document).on('click','#clear-order',function(e){
			e.preventDefault();
			orders.save_order('destroy');
			$('#order-items-tab').trigger('click');
		});
		$(document).on('click','#destroy-order',function(e){
			e.preventDefault();
			orders.save_order('clear');
			$('#order-items-tab').trigger('click');
		});
		$(document).on('click','#update-order',function(e){
			e.preventDefault();
			orders.save_order('update');
		});
		$(document).on('click','#checkout-order',function(e){
			e.preventDefault();
			orders.checkout();
		});
		$(document).on('click','#confirm-order',function(e){
			e.preventDefault();
			$('#order-summary-tab').trigger('click');
		});
		$(document).on('click','#update-quantity',function(e){
			e.preventDefault();
			orders.save_order('update');
		});
		$(document).on('click','#order-summary-tab',function(e){
			e.preventDefault();
		orders.reload_cart();		
		});
		$(document).on('click','.remove-discount',function(e){
			e.preventDefault();
		orders.remove_discount($(this).attr('data-id'));			
		});
		$(document).on('click','#update-discount',function(e){
			e.preventDefault();
		orders.save_order('update_discount');	
		});
		$(document).on('click','#remove-all-discounts',function(e){
			e.preventDefault();
		orders.save_order('remove_all_discounts');		
		});
		
		$(document).on('click','.add-item-btn',function(e)
	{
		e.preventDefault();

		// Get the form data.
		var $form_inputs = $(this).closest('tr').find(':input');
		var form_data = {};
		$form_inputs.each(function() 
		{
			form_data[this.name] = $(this).val();
		});

		$.ajax(
		{
			url: helper.baseUrl+'standard_library/insert_ajax_form_item_to_cart',
			type: 'POST',
			data: form_data,
			success:function(data)
			{
				orders.refresh_quantities()
			}
		});
	});
		
		
		orders.get_subcategories(1);
	},
	reload_cart:function(){
	$.ajax(
		{
			url: helper.baseUrl+'standard_library/view_cart',
			type: 'POST',
			success:function(data)
			{
			$('#order-summary').html(data);
			}
		});	
	},
		complete_checkout:function(){
	$.ajax(
		{
			url: helper.baseUrl+'standard_library/checkout',
			type: 'POST',
			data: { checkout:true },
			dataType:"JSON",
			success:function(response)
			{
			window.location.href=response.url;
			}
		});	
	},
	get_subcategories:function(id){
		 $.ajax({
                url: helper.baseUrl + 'admin_library/get_subcategories',
                type: "POST",
                dataType: "JSON",
                data: {
                    'id': id
                },
                fail: function () {
                    flashalert.danger('Cannot find subcategories');
                }
            }).done(function (response) {
				var subcat_options ="<option value=''>--Select subcategory--</option>";
				$.each(response.data,function(i,row){
					subcat_options += "<option value='"+row.item_subcategory_id+"'>"+row.item_subcategory_name+"</option>";
				});
				$('#subcategory-select').html(subcat_options).selectpicker('refresh');
			});
	},
	get_items:function(){
		 $.ajax({
                url: helper.baseUrl + 'lite_library/get_items',
                type: "POST",
                dataType: "JSON",
                data: {
                    'item_category_id': $('#category-select').val(),
					'item_subcategory_id': $('#subcategory-select').val()
                },
                fail: function () {
                    flashalert.danger('Cannot find items');
                }
            }).done(function (response) {
				if(response.data.length>0){
		var contents = '<table class="table" id="item-list"><thead><tr><th>Item Name</th><th>Price</th><th>Quantity</th><th></th></tr></thead><tbody>';
		$.each(response.data,function(i,row){
			var quantity = "";
			if(row.quantity>0){
			 quantity += "<span class='glyphicon glyphicon-ok'></span> "+row.quantity+" added to cart";
			}
			contents += '<tr><td>'+row.item_name+'</td><td>'+row.item_price+'</td><td><input type="text" id="ex'+row.item_id+'_qty" name="quantity" value="1" style="width:70px; display:inline" class="width_50 input-sm validate_integer form-control"/><button class="add-item-btn btn btn-default btn-sm marl ">Add to cart</button></td><td><input type="hidden" name="item_id" value="'+row.item_id+'"/><input type="hidden" name="name" value="'+row.item_name+'"/><input type="hidden" name="price" value="18.25"/><span class="pull-left item-status text-success" id="status_'+row.item_id+'" style="width:200px">'+quantity+'</td></td></tr>'
		});
		contents += '</tbody></table>'
		$('#confirm-order').prop('disabled',false);		
				} else {
		var contents = "No items were found in the selected category";	
		$('#confirm-order').prop('disabled',true);		
				}
			
		$('#items-container').html(contents).show();
			});
	},
	refresh_quantities:function(){
		 $.ajax({
                url: helper.baseUrl + 'lite_library/get_cart_quantities',
                type: "POST",
				dataType:"JSON",
                fail: function () {
                    flashalert.danger('Error loading cart');
                }
            }).done(function (response) {
				$.each(response.data,function(k,row){
					console.log(row);
					 var quantity = "<span class='glyphicon glyphicon-ok'></span> "+row.quantity+" added to cart";
					$('#status_'+row.item_id).html(quantity);
				});
			});
	},
	
	save_order:function(action){
		 $.ajax({
                url: helper.baseUrl + 'standard_library/view_cart',
                type: "POST",
                data: $('#order-summary').find('form').serialize()+'&'+action+'=1',
                fail: function () {
                    flashalert.danger('Cannot find items');
                }
            }).done(function (response) {
				orders.reload_cart();
			});
	},
	checkout:function(action){
		 $.ajax({
                url: helper.baseUrl + 'standard_library/view_cart',
                type: "POST",
				dataType:"JSON",
                data: $('#order-summary').find('form').serialize()+'&checkout=1',
                fail: function () {
                    flashalert.danger('Cannot find items');
                }
            }).done(function (response) {
				if(response.success){
				orders.complete_checkout();	
				} else {
				flashalert.danger(response.msg);
				
				}
			});
	},
		remove_discount:function(id){
		 $.ajax({
                url: helper.baseUrl + 'standard_library/unset_discount',
                type: "POST",
                data: {id:id},
                fail: function () {
                    flashalert.danger('An error occured');
                }
            }).done(function (response) {
				orders.reload_cart();
			});
	}
	
	
	
}