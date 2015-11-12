			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Insert New Location Type</h1>
				<a href="<?php echo $base_url; ?>admin/shop/location_types">Manage Location Types</a>				
				
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
								<input type="text" name="insert[<?php echo $row_id; ?>][name]" value="<?php echo set_value('insert['.$row_id.'][name]');?>" class="width_200 validate_alpha"/>
							</td>
							<td>
								<select name="insert[<?php echo $row_id; ?>][parent_location_type]" class="width_200">
									<option value="0">No Parent Location Type</option>
								<?php 
									foreach($location_type_data as $location_type) { 
										$id = $location_type[$this->flexi_cart_admin->db_column('location_type', 'id')];
								?>
									<option value="<?php echo $id; ?>" <?php echo set_select('insert['.$row_id.'][parent_location_type]', $id); ?>>
										<?php echo $location_type[$this->flexi_cart_admin->db_column('location_type', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
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
							<td colspan="3">
								<input type="submit" name="insert_location_type" value="Insert New Location Types" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


