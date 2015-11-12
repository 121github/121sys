			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
					<h1>Manage Currencies</h1>
					<p><a href="<?php echo $base_url; ?>admin/shop/insert_currency">Insert New Currency</a></p>

					<table class="table" >
						<thead>
							<tr>
								<th class="info_req tooltip_trigger"
									title="The name of the currency.">
									Name
								</th>
								<th class="info_req tooltip_trigger"
									title="The exchange rate of the currency compared to the carts default currency.">
									Exchange Rate
								</th>
								<th>Symbol</th>
								<th class="info_req tooltip_trigger"
									title="The currency symbol to display with currency values. For example '$' to display '$9.99'.">
									Symbol HTML
								</th>
								<th class="spacer_75 align_ctr tooltip_trigger"
									title="If checked, the currency symbol will be suffixed to the end of the currency value rather than the front. For example<br/> Checked: '9.99&euro;',<br/> Unchecked: '&pound;9.99'.">
									Suffix
								</th>
								<th class="info_req tooltip_trigger"
									title="The character used to separate currencies in excess of a thousand.<br/> For example, the comma in '1,000'.">
									Thousand
								</th>
								<th class="info_req tooltip_trigger"
									title="The character used to separate a currencies decimal value.<br/> For example, the period in '99.99'.">
									Decimal
								</th>
								<th class="spacer_75 align_ctr tooltip_trigger" 
									title="If checked, the currency will be set as 'active'.">
									Status
								</th>
								<th class="spacer_75 align_ctr tooltip_trigger" 
									title="If checked, the row will be deleted upon the form being updated.">
									Delete
								</th>
							</tr>
						</thead>
					<?php if (! empty($currency_data)) { ?>	
						<tbody>
						<?php 
							foreach ($currency_data as $row) { 
								$currency_id = $row[$this->flexi_cart_admin->db_column('currency', 'id')];
						?>
							<tr>
								<td>
									<input type="hidden" name="update[<?php echo $currency_id; ?>][id]" value="<?php echo $currency_id; ?>"/>
									<input type="text" name="update[<?php echo $currency_id; ?>][name]" value="<?php echo set_value('update['.$currency_id.'][name]', $row[$this->flexi_cart_admin->db_column('currency', 'name')]); ?>" class="width_100"/>
								</td>
								<td>
									<input type="text" name="update[<?php echo $currency_id; ?>][exchange_rate]" value="<?php echo set_value('update['.$currency_id.'][exchange_rate]', round($row[$this->flexi_cart_admin->db_column('currency', 'exchange_rate')],4)); ?>" class="width_100 validate_decimal"/>
								</td>
								<td>
									<?php echo $row[$this->flexi_cart_admin->db_column('currency', 'symbol')]; ?>
								</td>
								<td>
									<input type="text" name="update[<?php echo $currency_id; ?>][symbol]" value="<?php echo set_value('update['.$currency_id.'][symbol]', $row[$this->flexi_cart_admin->db_column('currency', 'symbol')]); ?>" class="width_100 validate_alpha"/>
								</td>
								<td class="align_ctr">
									<?php $symbol_suffix = (bool)$row[$this->flexi_cart_admin->db_column('currency', 'symbol_suffix')]; ?>
									<input type="hidden" name="update[<?php echo $currency_id; ?>][symbol_suffix]" value="0"/>
									<input type="checkbox" name="update[<?php echo $currency_id; ?>][symbol_suffix]" value="1" <?php echo set_checkbox('update['.$currency_id.'][symbol_suffix]','1', $symbol_suffix); ?>/>
								</td>
								<td>
									<input type="text" name="update[<?php echo $currency_id; ?>][thousand]" value="<?php echo set_value('update['.$currency_id.'][thousand]', $row[$this->flexi_cart_admin->db_column('currency', 'thousand_separator')]); ?>" class="width_50 validate_alpha"/>
								</td>
								<td>
									<input type="text" name="update[<?php echo $currency_id; ?>][decimal]" value="<?php echo set_value('update['.$currency_id.'][decimal]', $row[$this->flexi_cart_admin->db_column('currency', 'decimal_separator')]); ?>" class="width_50 validate_alpha"/>
								</td>
								<td class="align_ctr">
									<?php $status = (bool)$row[$this->flexi_cart_admin->db_column('currency', 'status')]; ?>
									<input type="hidden" name="update[<?php echo $currency_id; ?>][status]" value="0"/>
									<input type="checkbox" name="update[<?php echo $currency_id; ?>][status]" value="1" <?php echo set_checkbox('update['.$currency_id.'][status]','1', $status); ?>/>
								</td>
								<td class="align_ctr">
									<input type="hidden" name="update[<?php echo $currency_id; ?>][delete]" value="0"/>
									<input type="checkbox" name="update[<?php echo $currency_id; ?>][delete]" value="1"/>
								</td>
							</tr>
						<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="9">
									<input type="submit" name="update_currency" value="Update Currencies" class="link_button btn btn-default large"/>
								</td>
							</tr>
						</tfoot>
					<?php } else { ?>
						<tbody>
							<tr>
								<td colspan="9">
									There are no currencies setup to view.<br/>
									<a href="<?php echo $base_url; ?>admin/shop/insert_currency">Insert New Currency</a>
								</td>
							</tr>
						</tbody>
					<?php } ?>
					</table>				
				<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


