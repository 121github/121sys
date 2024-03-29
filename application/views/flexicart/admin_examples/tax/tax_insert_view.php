			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Insert New Tax</h1>
				<p><a href="<?php echo $base_url; ?>admin/shop/tax">Manage Taxes</a></p>
				
				<table class="table" >
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/> The name of the tax rate.">
								Name
							</th>
							<th class="tooltip_trigger"
								title="Set the location that the tax rate is applied to.">
								Location
							</th>
							<th class="tooltip_trigger"
								title="Set the zone that the tax rate is applied to. <br/>Note: If a location is set, it has priority over a zone rule.">
								Zone
							</th>
							<th class="info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/> Sets the tax rate as a percentage.">
								Tax Rate (%)
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the tax rate will be set as 'active'.">
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
							// !IMPORTANT: Remember to use the $i value to update the select menu id value "strtolower(url_title($location_type.'_'.$i, 'underscore'))", when using the copy/remove function.
					?>
						<tr>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][name]" value="<?php echo set_value('insert['.$row_id.'][name]');?>" class="width_150"/>
							</td>
							<td>
							<?php foreach($locations_tiered as $location_type => $locations) { ?>
								<select name="insert[<?php echo $row_id; ?>][location][]" id="tax_<?php echo strtolower(url_title($location_type.'_'.$i, 'underscore'));?>" class="dependent_menu width_175">
									<option value="0" class="parent_id_0">- Select <?php echo $location_type; ?> -</option>
								<?php 
									// Note: CI's set_select() function does not return the empty '[]' from the name 'insert['.$row_id.'][location][]'.
									// Therefore, ensure it is set as "set_select('insert['.$row_id.'][location]', $id)".
									foreach($locations as $location) { 
										$id = $location[$this->flexi_cart_admin->db_column('locations', 'id')];
								?>
									<option value="<?php echo $id; ?>" class="parent_id_<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'parent')]; ?>" <?php echo set_select('insert['.$row_id.'][location]', $id); ?>>
										<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
									</option>
								<?php } ?>
								</select><br/>
							<?php } ?>
							</td>
							<td>
								<select name="insert[<?php echo $row_id; ?>][zone]" class="width_175">
									<option value="0">No Tax Zone</option>
								<?php 
									foreach($tax_zones as $zone) { 
										$id = $zone[$this->flexi_cart_admin->db_column('location_zones', 'id')];
								?>
									<option value="<?php echo $id; ?>" <?php echo set_select('insert['.$row_id.'][zone]', $id); ?>>
										<?php echo $zone[$this->flexi_cart_admin->db_column('location_zones', 'name')]; ?>
									</option>
								<?php } ?>
								</select>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][rate]" value="<?php echo set_value('insert['.$row_id.'][rate]');?>" class="width_50 validate_decimal"/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[<?php echo $row_id; ?>][status]" value="0"/>
								<input type="checkbox" name="insert[<?php echo $row_id; ?>][status]" value="1" <?php echo set_checkbox('insert['.$row_id.'][status]', '1', TRUE); ?>/>
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
								<input type="submit" name="insert_tax" value="Insert New Taxes" class="link_button btn btn-default large"/>
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
	// Initialise each of the pages dependent menus, starting with 'tax_country' as the top level menu.
	initialise_dependent_menu('tax_country');
});

// !IMPORTANT NOTE: The 'initialise_dependent_menu()' must be customised as per each pages dependent menu requirements.
// The function must be placed outside of the jQuery $(function(){}); call to be accessible by the 'dependent_menu()' function.
function initialise_dependent_menu(elem_id)
{
	// As this page is listing multiple records all on the same page, and therefore multiple location menus,
	// use the jQuery 'each()' function to call the top level menu of each location type ('Country' in this example). 
	$('select[id^="'+elem_id+'"]').each(function() 
	{
		var elem_id = $(this).attr('id');
		var tax_id = elem_id.substring(elem_id.lastIndexOf('_')+1);
		
		// !IMPORTANT NOTE: The dependent_menu functions must be called in their reverse order - i.e. the most specific locations first (State, Country).
		dependent_menu('tax_state_'+tax_id, 'tax_post_zip_code_'+tax_id, false, true);
		dependent_menu('tax_country_'+tax_id, 'tax_state_'+tax_id, ['tax_post_zip_code_'+tax_id], true);
	});
}
</script>

