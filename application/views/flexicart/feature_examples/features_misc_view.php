			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
					<h3>Change Tax Rate</h3>
					<div class="frame_note">
						<small>
							On this demo site, for simplicity, the tax location is set at the same time as the shipping location via the 'Shipping' options on the 'View Cart' page.<br/>
							However, it is possible to set the shipping location and tax location independently of each other, the example below sets the tax location only.
						</small>
						<hr/>
						<label class="spacer_150">Tax Location:</label>
						<select name="tax_location">
							<option value="0"> - Country - </option>
						<?php foreach($countries as $country) { ?>
							<option value="<?php echo $country['loc_id'];?>" <?php echo ($this->flexi_cart->match_tax_location_id($country['loc_id'])) ? 'selected="selected"' : NULL;?>>
								<?php echo $country['loc_name'];?>
							</option>
						<?php } ?>
						</select>
						<input type="submit" name="update_tax" value="Update" class="link_button btn btn-default grey"/>				
					</div>
				<?php echo form_close();?>
			</div>

		</div>
	</div>
	
	<!-- Footer --><script>
$(function() {
	// Ajax example of how to use the 'get_taxed_currency_value()' function to convert a value into the current.
	$('#currency_value, #convert_to_currency').change(function()
	{
		var currency_value = ($('#currency_value').val() > 0) ? $('#currency_value').val() : 0;
		var convert_to_currency = $('#convert_to_currency').val();
	
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>lite_library/ajax_convert_currency',
			data: 'currency_value='+currency_value+'&convert_to_currency='+convert_to_currency,
			success: function(response){
				// Convert html euro and pound sign characters to unicode characters.
				response = response.replace('&euro;', '\u20AC'); 
				response = response.replace('&pound;', '\u00A3');
				$('#converted_currency').text(response);
			}
		});
	});
	
	// Ajax example of how to use the 'convert_weight()' function to convert a weight value into anoth weight type.
	$('#convert_weight, #convert_weight_from, #convert_weight_to').change(function()
	{
		var convert_weight = $('#convert_weight').val();
		var convert_weight_from = $('#convert_weight_from').val();
		var convert_weight_to = $('#convert_weight_to').val();
	
		$.ajax({
			type: 'POST',
			url: '<?php echo $base_url; ?>lite_library/ajax_convert_weight',
			data: 'convert_weight='+convert_weight+'&convert_weight_from='+convert_weight_from+'&convert_weight_to='+convert_weight_to,
			success: function(response){
				$('#converted_weight').text(response);
			}
		});
	});
});
</script>

