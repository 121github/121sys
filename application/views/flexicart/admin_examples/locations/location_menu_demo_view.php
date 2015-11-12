			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
					<ul>
						<li>
							<h5>Tiered Location Menus - JavaScript Required</h5>
							<small>To demonstrate the tier functionality, select 'Australia' or 'United States' as the country.</small>
						</li>
						<li>
						<?php foreach($locations_tiered as $location_type => $locations) { ?>
							<select id="row_<?php echo strtolower(url_title($location_type.'_0', 'underscore'));?>" class="dependent_menu width_150">
								<option value="0" class="parent_id_0">- Select <?php echo $location_type; ?> -</option>
							<?php foreach($locations as $location) { ?>
								<option value="<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'id')]; ?>" class="parent_id_<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'parent')]; ?>">
									<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
								</option>
							<?php } ?>
							</select>
							<br/>
						<?php } ?>
						</li>
						<li>
							<p>This method requires that a multi-dimensional array of location types ('Country', 'States'), and location names ('United Kingdom', 'New York' etc.) are looped through and rendered as html select menus, this array is generated using flexi carts 'locations_tiered()' function.</p>
							<p>The select menus must all have a unique id that the JavaScript can reference them by. This example uses a combination of the current '$location_type' and a row id, the functions 'strtolower()' and 'url_title()' are then used to format the id, removing unwanted characters and lowering the case.</p>
							<p>Then for each location option, a class of 'parent_id_#' is set stating the current locations parent id, if is the top level parent, its class is 'parent_id_0'. 
							This then allows JavaScript to relate child locations with parent locations. This relationship can continue for infinite generations (Location types).</p>
							<p>Example: Menu 'A' has an option selected with a value of '3', JavaScript will then update menu 'B' with only options that have a class of 'parent_id_3'.</p>
						</li>
						<li>
							<hr/>
						</li>	
						<li>	
							<h5>Inline Location Menu</h5>
						</li>	
						<li>	
							<select class="width_150">
								<option value="0">- Select Location -</option>
							<?php foreach($locations_inline as $location) { ?>
								<option value="<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'id')]; ?>">
									<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
								</option>
							<?php } ?>
							</select>
						</li>
						<li>
							<p>This is an example of how to display the location data inline via one select menu, rather than across multiple select menus.</p>
							<p>This method does not require any JavaScript and is ideal if space is limited, however, it can be harder to read and can grow in length very quickly - especially if many location types and locations are listed.</p>
						</li>
					</ul>
				<?php echo form_close();?>						
			</div>
			
		</div>
	</div>
	
	<!-- Footer -->  <script type="text/javascript" src="<?php echo base_url() ?>assets/flexicart/js/admin_global.js"></script>
<script>
$(function() {
	// Initialise each of the pages dependent menus, starting with 'row_country' as the top level menu.
	initialise_dependent_menu('row_country');
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
		var row_id = elem_id.substring(elem_id.lastIndexOf('_')+1);
		
		// !IMPORTANT NOTE: The dependent_menu functions must be called in their reverse order - i.e. the most specific locations first (State, Country).
		dependent_menu('row_state_'+row_id, 'row_post_zip_code_'+row_id, false, true);
		dependent_menu('row_country_'+row_id, 'row_state_'+row_id, ['row_post_zip_code_'+row_id], true);
	});
}
</script>

