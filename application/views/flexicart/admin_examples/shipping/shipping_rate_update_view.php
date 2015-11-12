			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Shipping Rates for <?php echo $shipping_data[$this->flexi_cart_admin->db_column('shipping_options', 'name')];?></h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/shipping">Manage Shipping options</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/insert_shipping_rate/<?php echo $shipping_data[$this->flexi_cart_admin->db_column('shipping_options', 'id')];?>">Insert New Shipping Rate</a>
				</p>

				<table class="table" >
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
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the row will be deleted upon the form being updated.">
								Delete
							</th>
						</tr>
					</thead>
				<?php if (! empty($shipping_rate_data)) { ?>	
					<tbody>
					<?php 
						foreach ($shipping_rate_data as $row) {
							$shipping_rate_id = $row[$this->flexi_cart_admin->db_column('shipping_rates', 'id')];
					?>
						<tr>
							<td>
								<input type="hidden" name="update[<?php echo $shipping_rate_id; ?>][id]" value="<?php echo $shipping_rate_id; ?>"/>
								<input type="hidden" name="update[<?php echo $shipping_rate_id; ?>][parent_id]" value="<?php echo $row[$this->flexi_cart_admin->db_column('shipping_rates', 'parent')]; ?>"/>
								<input type="text" name="update[<?php echo $shipping_rate_id; ?>][value]" value="<?php echo set_value('update['.$shipping_rate_id.'][value]',$row[$this->flexi_cart_admin->db_column('shipping_rates', 'value')]); ?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="update[<?php echo $shipping_rate_id; ?>][tare_weight]" value="<?php echo set_value('update['.$shipping_rate_id.'][tare_weight]',$row[$this->flexi_cart_admin->db_column('shipping_rates', 'tare_weight')]); ?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="update[<?php echo $shipping_rate_id; ?>][min_weight]" value="<?php echo set_value('update['.$shipping_rate_id.'][min_weight]',$row[$this->flexi_cart_admin->db_column('shipping_rates', 'min_weight')]); ?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="update[<?php echo $shipping_rate_id; ?>][max_weight]" value="<?php echo set_value('update['.$shipping_rate_id.'][max_weight]',$row[$this->flexi_cart_admin->db_column('shipping_rates', 'max_weight')]); ?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="update[<?php echo $shipping_rate_id; ?>][min_value]" value="<?php echo set_value('update['.$shipping_rate_id.'][min_value]',$row[$this->flexi_cart_admin->db_column('shipping_rates', 'min_value')]); ?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="update[<?php echo $shipping_rate_id; ?>][max_value]" value="<?php echo set_value('update['.$shipping_rate_id.'][max_value]',$row[$this->flexi_cart_admin->db_column('shipping_rates', 'max_value')]); ?>" class="width_50 validate_decimal"/>
							</td>
							<td class="align_ctr">
								<?php $status = (bool)$row[$this->flexi_cart_admin->db_column('shipping_rates', 'status')]; ?>
								<input type="hidden" name="update[<?php echo $shipping_rate_id; ?>][status]" value="0"/>
								<input type="checkbox" name="update[<?php echo $shipping_rate_id; ?>][status]" value="1" <?php echo set_checkbox('update['.$shipping_rate_id.'][status]','1', $status); ?>/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="update[<?php echo $shipping_rate_id; ?>][delete]" value="0"/>
								<input type="checkbox" name="update[<?php echo $shipping_rate_id; ?>][delete]" value="1"/>
							</td>
						</tr>
					<?php } ?>	
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8">
								<input type="submit" name="update_shipping_rates" value="Update Shipping Rates" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tfoot>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="8">
								There are no rates for this shipping option setup to view.<br/>
								<a href="<?php echo $base_url; ?>admin/shop/insert_shipping_rate/<?php echo $shipping_data[$this->flexi_cart_admin->db_column('shipping_options', 'id')];?>">Insert New Shipping Rate</a>
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


