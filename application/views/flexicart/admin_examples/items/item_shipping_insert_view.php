			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Insert New Item Shipping Rule</h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/items">Manage Items</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/item_shipping/<?php echo $item_data['item_id']; ?>">Manage <?php echo $item_data['item_name']; ?> Shipping Rules</a>
				</p>
				
				<table class="table" >
					<caption><?php echo $item_data['item_name']; ?></caption>
					<thead>
						<tr>
							<th class="tooltip_trigger" 
								title="Set the location that the shipping rule is applied to.">
								Location
							</th>
							<th class="tooltip_trigger" 
								title="Set the zone that the shipping rule is applied to. <br/>Note: If a location is set, it has priority over a zone.">
								Zone
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="The rate the item costs to ship to the selected location/zone. <br/>Note:Leave blank (Not '0') if not setting a rate.">
								Shipping Rate (&pound;)
							</th>
							<th class="spacer_150 align_ctr tooltip_trigger" 
								title="Set whether an item is 'Whitelisted' (Only permitted) or 'Blacklisted' (Not permitted) to being shipped to a location. <br/>If set as 'Location Not Banned', the item can be shipped to all locations.">
								Shipping Ban Status
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the cart will calculate the items shipping separate from the rest of the cart, and then add the cost to the final shipping charge.">
								Ship Seperate
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the shipping rule will be set as 'active'.">
								Status
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="Copy or remove a specific row and its data.">
								Copy / Remove
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<select name="insert[0][location]" class="width_175">
									<option value="0">No Shipping Location</option>
								<?php foreach($locations_inline as $location) { ?>
									<option value="<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'id')]; ?>">
										<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td>
								<select name="insert[0][zone]" class="width_175">
									<option value="0">No Shipping Zone</option>
								<?php foreach($shipping_zones as $zone) { ?>
									<option value="<?php echo $zone[$this->flexi_cart_admin->db_column('location_zones', 'id')]; ?>">
										<?php echo $zone[$this->flexi_cart_admin->db_column('location_zones', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td>
								<input type="text" name="insert[0][value]" value="" placeholder="NULL" class="width_75"/>
							</td>
							<td class="align_ctr">
								<select name="insert[0][banned]" class="width_150">
									<option value="0">Location Not Banned</option>
									<option value="1">Whitelist Location</option>
									<option value="2">Blacklist Location</option>
								</select>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[0][separate]" value="0"/>
								<input type="checkbox" name="insert[0][separate]" value="1"/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[0][status]" value="0"/>
								<input type="checkbox" name="insert[0][status]" value="1" checked="checked"/>
							</td>
							<td class="align_ctr">
								<input type="button" value="+" class="copy_row link_button btn btn-default"/>
								<input type="button" value="x" disabled="disabled" class="remove_row link_button btn btn-default"/>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="7">
								<input type="submit" name="insert_item_shipping" value="Insert New Item Shipping Rules" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


