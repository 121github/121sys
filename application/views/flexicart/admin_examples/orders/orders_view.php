				<div class="panel panel-primary">
		<div class="panel-heading">Manage Orders</div>
        <div class="panel-body" style="padding:0">
										
			
			
			<table class="table" >
				<thead>
					<tr>
						<th class="spacer_125">Order Number</th>
						<th>Customer Name</th>
						<th class="spacer_100 align_ctr">Total Items</th>
						<th class="spacer_100 align_ctr">Total Value</th>
						<th class="spacer_100 align_ctr">Date</th>
						<th class="spacer_125 align_ctr">Status</th>
					</tr>
				</thead>
				<tbody>
			<?php if (! empty($order_data)) { ?>	
				<?php 
					foreach ($order_data as $row) { 
						$order_number = $row[$this->flexi_cart_admin->db_column('order_summary', 'order_number')];
				?>
					<tr>
						<td>
							<a href="<?php echo $base_url; ?>admin/shop/order_details/<?php echo $order_number; ?>"><?php echo $order_number; ?></a>
						</td>
						<td>
							<?php echo $row['ord_demo_bill_name']; ?>
						</td>
						<td class="align_ctr">
							<?php echo number_format($row[$this->flexi_cart_admin->db_column('order_summary', 'total_items')]); ?>
						</td>
						<td class="align_ctr">
							<?php echo '&pound;'.$row[$this->flexi_cart_admin->db_column('order_summary', 'total')]; ?>
						</td>
						<td class="align_ctr">
							<?php echo date('jS M Y', strtotime($row[$this->flexi_cart_admin->db_column('order_summary', 'date')])); ?>
						</td>
						<td class="align_ctr">
							<?php echo $row[$this->flexi_cart_admin->db_column('order_status', 'status')]; ?>
						</td>
					</tr>
				<?php } } else { ?>
					<tr>
						<td colspan="6">
							There are no orders available to view.
						</td>
					</tr>
				<?php } ?>
				</tbody>
			</table>				

		</div>
	</div>
