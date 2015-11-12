			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Insert New <?php echo $location_type_data[$this->flexi_cart_admin->db_column('location_type', 'name')]; ?> Locations</h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/location_types">Manage Location Types</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/locations/<?php echo $location_type_data[$this->flexi_cart_admin->db_column('location_type', 'id')]; ?>">Manage <?php echo $location_type_data[$this->flexi_cart_admin->db_column('location_type', 'name')]; ?></a>
				</p>
									
				<table class="table" >
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/>Name of the location.">
								Name
							</th>
							<th class="tooltip_trigger"
								title="Sets the locations 'Parent'. <br/>For Example, 'New York' would have 'United States' as its parent.">
								Parent Location
							</th>
							<th class="tooltip_trigger"
								title="Locations can be grouped together with other non-related locations into Shipping Zones. Shipping rates can then be applied to all locations within these zones. <br/>For example, 'Eastern Europe' and 'Western Europe'.">
								Shipping Zone
							</th>
							<th class="tooltip_trigger"
								title="Locations can be grouped together with other non-related locations into Tax Zones. Tax rates can then be applied to all locations within these zones. <br/>For example, 'European EU Countries' and 'European Non-EU Countries'.">
								Tax Zone
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="If checked, the location will be set as 'active'.">
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
								<input type="text" name="insert[<?php echo $row_id; ?>][name]" value="<?php echo set_value('insert['.$row_id.'][name]');?>" class="width_150"/>
							</td>
							<td>
								<select name="insert[<?php echo $row_id; ?>][parent_location]" class="width_150">
									<option value="0">No Parent Location</option>
								<?php 
									foreach($locations_inline as $location) { 
										$id = $location[$this->flexi_cart_admin->db_column('locations', 'id')];
								?>
									<option value="<?php echo $id; ?>" <?php echo set_select('insert['.$row_id.'][parent_location]', $id); ?>>
										<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td>
								<select name="insert[<?php echo $row_id; ?>][shipping_zone]" class="width_150">
									<option value="0">No Shipping Zone</option>
								<?php 
									foreach($shipping_zones as $zone) { 
										$id = $zone[$this->flexi_cart_admin->db_column('location_zones', 'id')];
								?>
									<option value="<?php echo $id; ?>" <?php echo set_select('insert['.$row_id.'][shipping_zone]', $id); ?>>
										<?php echo $zone[$this->flexi_cart_admin->db_column('location_zones', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td>
								<select name="insert[<?php echo $row_id; ?>][tax_zone]" class="width_150">
									<option value="0">No Tax Zone</option>
								<?php 
									foreach($tax_zones as $zone) { 
										$id = $zone[$this->flexi_cart_admin->db_column('location_zones', 'id')];
								?>
									<option value="<?php echo $id; ?>" <?php echo set_select('insert['.$row_id.'][tax_zone]', $id); ?>>
										<?php echo $zone[$this->flexi_cart_admin->db_column('location_zones', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[<?php echo $row_id; ?>][status]" value="0"/>
								<input type="checkbox" name="insert[<?php echo $row_id; ?>][status]" value="1" <?php echo set_checkbox('insert['.$row_id.'][status]', 1, TRUE); ?>/>
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
							<td colspan="6">
								<input type="submit" name="insert_location" value="Insert New <?php echo $location_type_data[$this->flexi_cart_admin->db_column('location_type', 'name')]; ?> Locations" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


