			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Insert New Currency</h1>
				<p><a href="<?php echo $base_url; ?>admin/shop/currency">Manage Currencies</a></p>
			
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
								<input type="text" name="insert[<?php echo $row_id; ?>][name]" value="<?php echo set_value('insert['.$row_id.'][name]');?>" class="width_100"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][exchange_rate]" value="<?php echo set_value('insert['.$row_id.'][exchange_rate]');?>" class="width_100 validate_decimal"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][symbol]" value="<?php echo set_value('insert['.$row_id.'][symbol]');?>" class="width_100 validate_alpha"/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[<?php echo $row_id; ?>][symbol_suffix]" value="0"/>
								<input type="checkbox" name="insert[<?php echo $row_id; ?>][symbol_suffix]" value="1" <?php echo set_checkbox('insert['.$row_id.'][symbol_suffix]', '1'); ?>/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][thousand]" value="<?php echo set_value('insert['.$row_id.'][thousand]');?>" class="width_50 validate_alpha"/>
							</td>
							<td>
								<input type="text" name="insert[<?php echo $row_id; ?>][decimal]" value="<?php echo set_value('insert['.$row_id.'][decimal]');?>" class="width_50 validate_alpha"/>
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
								<input type="submit" name="insert_currency" value="Insert New Currency" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


