			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Insert New Shipping Option</h1>
				<p><a href="<?php echo $base_url; ?>admin/shop/shipping">Manage Shipping Options</a></p>
				
				<table class="table" >
					<caption>Shipping Option</caption>
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="<strong>Name Field Required</strong><br/>The name and a short description of the shipping option.">
								Option Name / <br/>Description
							</th>
							<th class="tooltip_trigger"
								title="Set the location that the shipping option is applied to.">
								Location
							</th>
							<th class="tooltip_trigger"
								title="Set the zone that the shipping option is applied to. <br/>Note: If a location is set, it has priority over a zone rule.">
								Zone
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="If checked, sets whether the shipping option is displayed with options that are available for more specific locations. <br/>For example, if checked for 'United States', the option will also be displayed with 'New York' options.">
								Inc. Sub Locations
							</th>
							<th class="align_ctr tooltip_trigger"
								title="Sets the tax rate charged on the total value of shipping, but not the tax rate of any other values within the cart. <br/>Note: Leave blank to use the default cart tax rate.">
								Tax Rate (%)
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="If checked, sets whether the shipping option can be included in cart discounts. <br/>For example, a 10% discount on the cart value could be excluded from including the shipping value.">
								Discount
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the shipping option will be set as 'active'.">
								Status
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="text" name="insert_option[name]" value="<?php echo set_value('insert_option[name]');?>" placeholder="Name" class="width_125"/><br/>
								<textarea name="insert_option[description]" placeholder="Description" class="width_125"><?php echo set_value('insert_option[description]');?></textarea>
							</td>
							<td>
							<?php foreach($locations_tiered as $location_type => $locations) { ?>
								<select name="insert_option[location][]" id="shipping_<?php echo strtolower(url_title($location_type.'_101', 'underscore'));?>" class="width_150">
									<option value="0" class="parent_id_0">- Select <?php echo $location_type; ?> -</option>
								<?php 
									// Note: CI's set_select() function does not return the empty '[]' from the name 'insert_option[location][]'.
									// Therefore, ensure it is set as "set_select('insert_option[location]', $id)".
									foreach($locations as $location) { 
										$id = $location[$this->flexi_cart_admin->db_column('locations', 'id')];
								?>
									<option value="<?php echo $id; ?>" class="parent_id_<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'parent')]; ?>" <?php echo set_select('insert_option[location]', $id); ?>>
										<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
									</option>
								<?php } ?>
								</select><br/>
							<?php } ?>
							</td>
							<td>
								<select name="insert_option[zone]" class="width_150">
									<option value="0">No Shipping Zone</option>
								<?php 
									foreach($shipping_zones as $zone) { 
										$id = $zone[$this->flexi_cart_admin->db_column('location_zones', 'id')];
								?>
									<option value="<?php echo $id; ?>" <?php echo set_select('insert_option[zone]', $id); ?>>
										<?php echo $zone[$this->flexi_cart_admin->db_column('location_zones', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert_option[inc_sub_locations]" value="0"/>
								<input type="checkbox" name="insert_option[inc_sub_locations]" value="1" <?php echo set_checkbox('insert_option[inc_sub_locations]', '1'); ?>/>
							</td>
							<td class="align_ctr">
								<input type="text" name="insert_option[tax_rate]" value="<?php echo set_value('insert_option[tax_rate]');?>" placeholder="Default" class="width_50 validate_decimal"/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert_option[discount_inclusion]" value="0"/>
								<input type="checkbox" name="insert_option[discount_inclusion]" value="1" <?php echo set_checkbox('insert_option[discount_inclusion]', '1'); ?>/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert_option[status]" value="0"/>
								<input type="checkbox" name="insert_option[status]" value="1" <?php echo set_checkbox('insert_option[status]', '1', TRUE); ?>/>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="table" >
					<caption>Shipping Rate Tiers</caption>
					<thead>
						<tr>
							<th class="spacer_100 info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/>The shipping rate of the shipping option tier.">
								Rate (&pound;)
							</th>
							<th class="tooltip_trigger"
								title="The tare weight represents the weight of the packaging material required for shipping. The weight is included when matching shipping options with the weight of the cart items.">
								Tare Weight (g)
							</th>
							<th class="tooltip_trigger"
								title="Sets the minimum weight required to activate the shipping option tier. <br/>Note: The 'tare weight' will be included when weighing the cart items.">
								Min Weight (g)
							</th>
							<th class="tooltip_trigger"
								title="Sets the maximum weight permitted to activate the shipping option tier. <br/>Note: The 'tare weight' will be included when weighing the cart items.">
								Max Weight (g)
							</th>
							<th class="tooltip_trigger"
								title="Sets the minimum value of the cart that is required to activate the shipping option tier.">
								Min Value (&pound;)
							</th>
							<th class="tooltip_trigger"
								title="Sets the maximum value of the cart that is permitted to activate the shipping option tier.">
								Max Value (&pound;)
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the shipping rate tier will be set as 'active'.">
								Status
							</th>
							<th class="spacer_125 align_ctr tooltip_trigger" 
								title="Copy or remove a specific row and its data.">
								Copy / Remove
							</th>
						</tr>
					</thead>
					<tbody>
					<?php 
						for($i = 0; ($i == 0 || (isset($validation_row_ids[$i]))); $i++) { 
							$row_id = (isset($validation_row_ids[$i])) ? $validation_row_ids[$i] : $i;
					?>
						<tr>
							<td>
								<input type="text" name="insert_rate[<?php echo $row_id; ?>][value]" value="<?php echo set_value('insert_rate['.$row_id.'][value]', '0.00');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert_rate[<?php echo $row_id; ?>][tare_weight]" value="<?php echo set_value('insert_rate['.$row_id.'][tare_weight]', '0');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert_rate[<?php echo $row_id; ?>][min_weight]" value="<?php echo set_value('insert_rate['.$row_id.'][min_weight]', '0');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert_rate[<?php echo $row_id; ?>][max_weight]" value="<?php echo set_value('insert_rate['.$row_id.'][max_weight]',' 9999');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert_rate[<?php echo $row_id; ?>][min_value]" value="<?php echo set_value('insert_rate['.$row_id.'][min_value]', '0.00');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert_rate[<?php echo $row_id; ?>][max_value]" value="<?php echo set_value('insert_rate['.$row_id.'][max_value]', '9999.00');?>" class="width_50 validate_decimal"/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert_rate[<?php echo $row_id; ?>][status]" value="0"/>
								<input type="checkbox" name="insert_rate[<?php echo $row_id; ?>][status]" value="1" <?php echo set_checkbox('insert_rate['.$row_id.'][status]', '1', TRUE); ?>/>
							</td>
							<td class="align_ctr">
								<input type="button" value="+" class="copy_row link_button btn btn-default"/>
								<input type="button" value="x" <?php echo ($i == 0) ? 'disabled="disabled"' : NULL;?> class="remove_row link_button btn btn-default"/>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8">
								<input type="submit" name="insert_shipping" value="Insert Shipping Option" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  <script type="text/javascript" src="<?php echo base_url() ?>assets/flexicart/js/admin_global.js"></script>
<script>
$(function() {
	// As this page is listing multiple tax options all on the same page, and therefore multiple location menus, use the jQuery 'each()' function to call the top level menu of each location type ('Country' in this example). 
	$('select[id^="shipping_country"]').each(function() 
	{
		var elem_id = $(this).attr('id');
		var shipping_id = elem_id.substring(elem_id.lastIndexOf('_')+1);
	
		// !IMPORTANT NOTE: The dependent_menu functions must be called in their reverse order - i.e. the most specific locations first.
		dependent_menu('shipping_state_'+shipping_id, 'shipping_post_zip_code_'+shipping_id, false, true);
		dependent_menu('shipping_country_'+shipping_id, 'shipping_state_'+shipping_id, ['shipping_post_zip_code_'+shipping_id], true);
	});
});
</script>

