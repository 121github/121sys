			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
					<h1>Manage Order Statuses</h1>
					<p><a href="<?php echo $base_url; ?>admin/shop/insert_order_status">Insert New Order Status</a></p>

					<table class="table" >
						<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="The name/description of the order status.">
								Description
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
								title="If checked, the row will be deleted upon the form being updated.">
								Delete
							</th>
						</tr>
						</thead>
					<?php if (! empty($order_status_data)) { ?>	
						<tbody>
						<?php 
							foreach ($order_status_data as $row) { 
								$status_id = $row[$this->flexi_cart_admin->db_column('order_status', 'id')];
						?>
							<tr>
								<td>
									<input type="hidden" name="update[<?php echo $status_id; ?>][id]" value="<?php echo $status_id; ?>"/>
									<input type="text" name="update[<?php echo $status_id; ?>][status]" value="<?php echo set_value('update['.$status_id.'][status]', $row[$this->flexi_cart_admin->db_column('order_status', 'status')]);?>" class="width_200"/>
								</td>
								<td class="align_ctr">
									<?php $cancelled = (bool)$row[$this->flexi_cart_admin->db_column('order_status', 'cancelled')]; ?>
									<input type="hidden" name="update[<?php echo $status_id; ?>][cancelled]" value="0"/>
									<input type="checkbox" name="update[<?php echo $status_id; ?>][cancelled]" value="1" <?php echo set_checkbox('update['.$status_id.'][cancelled]', '1', $cancelled); ?>/>
								</td>
								<td class="align_ctr">
									<?php $save_default = (bool)$row[$this->flexi_cart_admin->db_column('order_status', 'save_default')]; ?>
									<input type="hidden" name="update[<?php echo $status_id; ?>][save_default]" value="0"/>
									<input type="checkbox" name="update[<?php echo $status_id; ?>][save_default]" value="1" <?php echo set_checkbox('update['.$status_id.'][save_default]', '1', $save_default); ?>/>
								</td>
								<td class="align_ctr">
									<?php $resave_default = (bool)$row[$this->flexi_cart_admin->db_column('order_status', 'resave_default')]; ?>
									<input type="hidden" name="update[<?php echo $status_id; ?>][resave_default]" value="0"/>
									<input type="checkbox" name="update[<?php echo $status_id; ?>][resave_default]" value="1" <?php echo set_checkbox('update['.$status_id.'][resave_default]', '1', $resave_default); ?>/>
								</td>
								<td class="align_ctr">
									<input type="hidden" name="update[<?php echo $status_id; ?>][delete]" value="0"/>
									<input type="checkbox" name="update[<?php echo $status_id; ?>][delete]" value="1"/>
								</td>
							</tr>
						<?php } ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5">
									<input type="submit" name="update_order_status" value="Update Order Status" class="link_button btn btn-default large"/>
								</td>
							</tr>
						</tfoot>
					<?php } else { ?>
						<tbody>
							<tr>
								<td colspan="5">
									There are no order statuses setup to view.<br/>
									<a href="<?php echo $base_url; ?>admin/shop/insert_order_status">Insert New Order Status</a>
								</td>
							</tr>
						</tbody>
					<?php } ?>
					</table>				
				<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


