			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Insert New Order Status</h1>
				<p><a href="<?php echo $base_url; ?>admin/shop/order_status">Manage Order Status</a></p>
			
				<table class="table" >
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="The name/description of the order status.">
								Status Description
							</th>
							<th class="spacer_125 align_ctr tooltip_trigger" 
								title="If checked, it indicates that the order status 'Cancels' the order.">
								Cancel Order
							</th>
							<th class="spacer_125 align_ctr tooltip_trigger" 
								title="If checked, it indicates that the order status is the default status that is applied to a 'saved' order.">
								Save Default
							</th>
							<th class="spacer_125 align_ctr tooltip_trigger" 
								title="If checked, it indicates that the order status is the default status that is applied to a 'resaved' order.">
								Resave Default
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
								<input type="text" name="insert[<?php echo $row_id; ?>][status]" value="<?php echo set_value('insert['.$row_id.'][status]');?>" class="width_200"/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[<?php echo $row_id; ?>][cancelled]" value="0"/>
								<input type="checkbox" name="insert[<?php echo $row_id; ?>][cancelled]" value="1" <?php echo set_checkbox('insert['.$row_id.'][cancelled]', '1', FALSE); ?>/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[<?php echo $row_id; ?>][save_default]" value="0"/>
								<input type="checkbox" name="insert[<?php echo $row_id; ?>][save_default]" value="1" <?php echo set_checkbox('insert['.$row_id.'][save_default]', '1', FALSE); ?>/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="insert[<?php echo $row_id; ?>][resave_default]" value="0"/>
								<input type="checkbox" name="insert[<?php echo $row_id; ?>][resave_default]" value="1" <?php echo set_checkbox('insert['.$row_id.'][resave_default]', '1', FALSE); ?>/>
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
							<td colspan="5">
								<input type="submit" name="insert_order_status" value="Insert Order Status" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


