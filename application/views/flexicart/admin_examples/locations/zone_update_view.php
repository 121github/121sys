			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Location Zones</h1>
				<p><a href="<?php echo $base_url; ?>admin/shop/insert_zone">Insert New Zone</a></p>

				<table class="table" >
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/>The name of the zone.">
								Name
							</th>
							<th class="tooltip_trigger"
								title="A brief description of the purpose of the zone and the regions covered.">
								Description
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the zone will be set as 'active'.">
								Status
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the row will be deleted upon the form being updated.">
								Delete
							</th>
						</tr>
					</thead>
				<?php if (! empty($location_zone_data)) { ?>	
					<tbody>
					<?php 						
						foreach ($location_zone_data as $row) {
							$location_zone_id = $row[$this->flexi_cart_admin->db_column('location_zones', 'id')];
					?>
						<tr>
							<td>
								<input type="hidden" name="update[<?php echo $location_zone_id; ?>][id]" value="<?php echo $location_zone_id; ?>"/>
								<input type="text" name="update[<?php echo $location_zone_id; ?>][name]" value="<?php echo set_value('update['.$location_zone_id.'][name]',$row[$this->flexi_cart_admin->db_column('location_zones', 'name')]); ?>" class="width_175"/>
							</td>
							<td>
								<textarea name="update[<?php echo $location_zone_id; ?>][description]" class="width_400"><?php echo set_value('update['.$location_zone_id.'][description]',$row[$this->flexi_cart_admin->db_column('location_zones', 'description')]); ?></textarea>
							</td>
							<td class="align_ctr">
								<?php $status = (bool)$row[$this->flexi_cart_admin->db_column('location_zones', 'status')]; ?>
								<input type="hidden" name="update[<?php echo $location_zone_id; ?>][status]" value="0"/>
								<input type="checkbox" name="update[<?php echo $location_zone_id; ?>][status]" value="1" <?php echo set_checkbox('update['.$location_zone_id.'][status]','1', $status); ?>/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="update[<?php echo $location_zone_id; ?>][delete]" value="0"/>
								<input type="checkbox" name="update[<?php echo $location_zone_id; ?>][delete]" value="1"/>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4">
								<input type="submit" name="update_zones" value="Update Zones" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tfoot>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="4">
								There are no zones setup to view.<br/>
								<a href="<?php echo $base_url; ?>admin/shop/insert_zone">Insert New Zone</a>
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


