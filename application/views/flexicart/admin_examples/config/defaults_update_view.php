			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>	
				<fieldset>
					<legend>Currency</legend>
					<small>Defines the default currency that prices are displayed in when a user first visits the site.</small>
					<ul>
						<li>
							<label for="currency">Default Currency</label>
							<select id="currency" name="update[currency]" class="width_250 tooltip_trigger"
								title="Set the default currency that cart values are displayed as."
							>
								<option value="0"> - Select Default Currency - </option>
							<?php 
								foreach($currency_data as $currency) { 
									$id = $currency[$this->flexi_cart_admin->db_column('currency', 'id')];
									$default = $default_currency[$this->flexi_cart_admin->db_column('currency', 'id')];
							?>
								<option value="<?php echo $id; ?>" <?php echo set_select('update[currency]', $id, ($default == $id)); ?>>
									<?php echo $currency[$this->flexi_cart_admin->db_column('currency', 'name')]; ?>
								</option>
							<?php } ?>
							</select>
						</li>
					</ul>
				</fieldset>
					
				<fieldset>
					<legend>Shipping</legend>
					<small>Defines the default shipping location and shipping option (Method) that are displayed when a user first visits the site.</small>
					<ul>
						<li>
							<label for="shipping_location">Default Shipping Location</label>
							<select id="shipping_location" name="update[shipping_location]" class="width_250 tooltip_trigger"
								title="Set the default location that shipping options and rates are displayed for."
							>
								<option value="0"> - Select Default Shipping Location - </option>
							<?php 
								foreach($locations_inline as $location) { 
									$id = $location[$this->flexi_cart_admin->db_column('locations', 'id')];
									$default = $default_ship_location[$this->flexi_cart_admin->db_column('locations', 'id')];
							?>
								<option value="<?php echo $id; ?>" <?php echo set_select('update[shipping_location]', $id, ($default == $id)); ?>>
									<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
								</option>
							<?php } ?>
							</select>
						</li>
						<li>
							<label for="shipping_option">Default Shipping Option</label>
							<select id="shipping_option" name="update[shipping_option]" class="width_250 tooltip_trigger"
								title="Set the default shipping option that is displayed."
							>
								<option value="0"> - Select Default Shipping Option - </option>
							<?php 
								foreach($shipping_data as $option) { 
									$id = $option[$this->flexi_cart_admin->db_column('shipping_options', 'id')];
									$default = $default_ship_option[$this->flexi_cart_admin->db_column('shipping_options', 'id')];
							?>
								<option value="<?php echo $id; ?>" <?php echo set_select('update[shipping_option]', $id, ($default == $id)); ?>>
									<?php echo $option[$this->flexi_cart_admin->db_column('shipping_options', 'name')]; ?>
								</option>
							<?php } ?>
							</select>
						</li>
					</ul>
				</fieldset>
					
				<fieldset>
					<legend>Tax</legend>
					<small>Defines the default tax location and tax rate that is displayed when a user first visits the site.</small>
					<ul>
						<li>
							<label for="tax_location">Default Tax Location</label>
							<select id="tax_location" name="update[tax_location]" class="width_250 tooltip_trigger"
								title="Set the default location that the cart tax rate is based on."
							>
								<option value="0"> - Select Default Tax Location - </option>
							<?php 
								foreach($locations_inline as $location) { 
									$id = $location[$this->flexi_cart_admin->db_column('locations', 'id')];
									$default = $default_tax_location[$this->flexi_cart_admin->db_column('locations', 'id')];
							?>
								<option value="<?php echo $id; ?>" <?php echo set_select('update[tax_location]', $id, ($default == $id)); ?>>
									<?php echo $location[$this->flexi_cart_admin->db_column('locations', 'name')]; ?>
								</option>
							<?php } ?>
							</select>
						</li>
						<li>
							<label for="tax_rate">Default Tax Rate</label>
							<select id="tax_rate" name="update[tax_rate]" class="width_250 tooltip_trigger"
								title="Select the default tax rate that is displayed."
							>
								<option value="0"> - Select Default Tax Rate - </option>
							<?php 
								foreach($tax_data as $tax_rate) { 
									$id = $tax_rate[$this->flexi_cart_admin->db_column('tax', 'id')];
									$default = $default_tax_rate[$this->flexi_cart_admin->db_column('tax', 'id')];								
							?>
								<option value="<?php echo $id; ?>" <?php echo set_select('update[tax_rate]', $id, ($default == $id)); ?>>
									<?php echo $tax_rate[$this->flexi_cart_admin->db_column('tax', 'name')]; ?>
								</option>
							<?php } ?>
							</select>
						</li>
					</ul>
				</fieldset>
				
				<fieldset>
					<legend>Update Cart Defaults</legend>
					<input type="submit" name="update_defaults" value="Update Cart Defaults" class="link_button btn btn-default large"/>
				</fieldset>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


