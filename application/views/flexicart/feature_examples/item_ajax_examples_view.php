<?php echo form_open($base_url.'standard_library/insert_ajax_form_item_to_cart', array('class'=>'position_right'));?>
					<h6>Example #504</h6>
				
					<div class="frame_note parallel_target">
						<small>
							This example updates the items price depending on the option selected.<br/>
							The example does not submit an array of the items options, so the user cannot change the selected option once added to the cart.
						</small>
						<hr/>
						
						<ul>
							<li>
								<label for="ex504_qty">Quantity:</label>
								<input type="text" id="ex504_qty" name="quantity" value="1" class="width_50 validate_integer"/>
							</li>
							<li>
								<label for="ex504_option">Option with Price:</label>
								<select id="ex504_option" name="option_with_price" class="width_175">
									<option value="1">Option #1 @ <?php echo $this->flexi_cart->get_taxed_currency_value(24.95);?></option>
									<option value="2">Option #2 @ <?php echo $this->flexi_cart->get_taxed_currency_value(32.95);?></option>
									<option value="3">Option #3 @ <?php echo $this->flexi_cart->get_taxed_currency_value(49.95);?></option>
								</select> 
							</li>
							<li>
								<hr/>
								<label>Add to cart:</label>							
								<input type="submit" name="add_item_ajax_form_504" value="Submit" class="link_button btn btn-default add_item_via_ajax_form"/>
								
								<input type="hidden" name="item_id" value="504"/>
								<input type="hidden" name="name" value="Item #504, added via an AJAX form with priced options"/>
							</li>
						</ul>
					</div>
				<?php echo form_close();?>					
			</fieldset>
		</div>
	</div>
	
	<!-- Footer --><script>
$(function() 
{
	// Example of adding a item to the cart via a link.
	$('.add_item_via_ajax_link').click(function(event)
	{
		event.preventDefault();

		$.ajax(
		{
			url:$(this).attr('href'),
			success:function(data)
			{
				ajax_update_mini_cart(data);
			}
		});
	});

	// Example of adding a item to the cart via a link.
	$('.add_item_via_ajax_form').click(function(event)
	{
		event.preventDefault();

		// Get the parent form.
		var parent_form = $(this).closest('form');
		
		// Get the url the ajax form data to be submitted to.
		var submit_url = parent_form.attr('action');

		// Get the form data.
		var $form_inputs = parent_form.find(':input');
		var form_data = {};
		$form_inputs.each(function() 
		{
			form_data[this.name] = $(this).val();
		});

		$.ajax(
		{
			url: submit_url,
			type: 'POST',
			data: form_data,
			success:function(data)
			{
				ajax_update_mini_cart(data);
			}
		});
	});

	// A function to display updated ajax cart data from the mini cart menu.
	function ajax_update_mini_cart(data)
	{
		// Replace the current mini cart with the ajax loaded mini cart data. 
		var ajax_mini_cart = $(data).find('#mini_cart');
		$('#mini_cart').replaceWith(ajax_mini_cart);

		// Display a status within the mini cart stating the cart has been updated.
		$('#mini_cart_status').show();

		// Set the new height of the menu for animation purposes.
		var min_cart_height = $('#mini_cart ul:first').height();
		$('#mini_cart').attr('data-menu-height', min_cart_height);
		$('#mini_cart').attr('class', 'js_nav_dropmenu');

		// Scroll to the top of the page.
		$('body').animate({'scrollTop':0}, 250, function()
		{
			// Notify the user that the cart has been updated by showing the mini cart.
			$('#mini_cart ul:first').stop().animate({'height':min_cart_height}, 400).delay(3000).animate({'height':'0'}, 400, function()
			{
				$('#mini_cart_status').hide();
			});
		});
	}
});
</script>

