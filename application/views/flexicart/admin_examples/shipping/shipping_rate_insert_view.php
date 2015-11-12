			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Add Shipping Rate for <?php echo $shipping_data[$this->flexi_cart_admin->db_column('shipping_options', 'name')];?></h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/shipping">Manage Shipping options</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/shipping_rates/<?php echo $shipping_data[$this->flexi_cart_admin->db_column('shipping_options', 'id')];?>">Manage <?php echo $shipping_data[$this->flexi_cart_admin->db_column('shipping_options', 'name')];?> Rates</a>
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
								<input type="text" name="insert[<?php echo $row_id; ?>][value]" value="<?php echo set_value('insert['.$row_id.'][value]', '0.00');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][tare_weight]" value="<?php echo set_value('insert['.$row_id.'][tare_weight]', '0');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][min_weight]" value="<?php echo set_value('insert['.$row_id.'][min_weight]', '0');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][max_weight]" value="<?php echo set_value('insert['.$row_id.'][max_weight]',' 9999');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][min_value]" value="<?php echo set_value('insert['.$row_id.'][min_value]', '0.00');?>" class="width_50 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][max_value]" value="<?php echo set_value('insert['.$row_id.'][max_value]', '9999.00');?>" class="width_50 validate_decimal"/>
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
							<td colspan="8">
								<input type="submit" name="insert_shipping_rate" value="Insert Shipping Option Rates" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


