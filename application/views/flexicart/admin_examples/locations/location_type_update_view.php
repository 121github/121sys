			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Manage Location Types</h1>
				<p><a href="<?php echo $base_url; ?>admin/shop/insert_location_type">Insert New Location Type</a></p>
					
				<table class="table" >
					<thead>
						<tr>
							<th class="spacer_250 info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/>The name for the type of locations that will be related. <br/>For example, 'Country', 'State' etc.">
								Location Type
							</th>
							<th class="tooltip_trigger"
								title="Sets the location types 'Parent'. <br/>For Example, 'State' would have 'Country' as its parent.">
								Parent Location Type 
							</th>
							<th class="spacer_175 align_ctr tooltip_trigger"
								title="Manage and Insert locations related to the location type.">
								Related Locations
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the row will be deleted upon the form being updated.">
								Delete
							</th>
						</tr>
					</thead>
				<?php if (! empty($location_type_data)) { ?>	
					<tbody>
					<?php 						
						foreach ($location_type_data as $row) {
							$location_type_id = $row[$this->flexi_cart_admin->db_column('location_type', 'id')];
					?>
						<tr>
							<td>
								<input type="hidden" name="update[<?php echo $location_type_id; ?>][id]" value="<?php echo $location_type_id; ?>"/>
								<input type="text" name="update[<?php echo $location_type_id; ?>][name]" value="<?php echo set_value('update['.$location_type_id.'][name]', $row[$this->flexi_cart_admin->db_column('location_type', 'name')]); ?>" class="width_200 validate_alpha"/>
							</td>
							<td>
								<?php $parent_location_type = $row[$this->flexi_cart_admin->db_column('location_type', 'parent')];?>
								<select name="update[<?php echo $location_type_id; ?>][parent_location_type]" class="width_200">
									<option value="0">No Parent Location Type</option>
								<?php 
									foreach($location_type_data as $location_type) { 
										$id = $location_type[$this->flexi_cart_admin->db_column('location_type', 'id')];
								?>
									<option value="<?php echo $id; ?>" <?php echo set_select('update['.$location_type_id.'][parent_location_type]', $id, ($parent_location_type == $id)); ?>>
										<?php echo $location_type[$this->flexi_cart_admin->db_column('location_type', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td class="align_ctr">
								<a href="<?php echo $base_url; ?>admin/shop/locations/<?php echo $location_type_id;?>">Manage</a> | 
								<a href="<?php echo $base_url; ?>admin/shop/insert_location/<?php echo $location_type_id;?>">Insert New</a> 
							</td>
							<td class="align_ctr">
								<input type="hidden" name="update[<?php echo $location_type_id; ?>][delete]" value="0"/>
								<input type="checkbox" name="update[<?php echo $location_type_id; ?>][delete]" value="1"/>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4">
								<input type="submit" name="update_location_types" value="Update Location Types" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tfoot>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="4">
								There are no location types setup to view.<br/>
								<a href="<?php echo $base_url; ?>admin/shop/insert_location_type">Insert New Location Type</a>
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


