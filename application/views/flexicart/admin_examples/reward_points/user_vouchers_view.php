			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1><?php echo $user_data['user_name'];?> : Reward Vouchers</h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/user_reward_points">Manage Reward Points</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/user_reward_point_history/<?php echo $user_data['user_id']; ?>">View Reward Point History</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/convert_reward_points/<?php echo $user_data['user_id'];?>">Convert Reward Points</a>
				</p>
				
				<table class="table" >
					<thead>
						<tr>
							<th class="tooltip_trigger"
								title="The code used to apply the reward voucher.">
								Voucher Code
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger"
								title="Indicates whether the reward voucher has been used or not.">
								Availability
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger"
								title="The currency value of the reward voucher.">
								Value
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger"
								title="The expiry date the voucher must be used by.">
								Expire Date
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="If checked, the reward voucher will be set as 'active'.">
								Status
							</th>
						</tr>
					</thead>
				<?php if (! empty($voucher_data)) { ?>	
					<tbody>
					<?php 
						foreach ($voucher_data as $row) {
							$voucher_id = $row[$this->flexi_cart_admin->db_column('discounts', 'id')];
					?>
						<tr>
							<td>
								<input type="hidden" name="update[<?php echo $voucher_id; ?>][id]" value="<?php echo $row[$this->flexi_cart_admin->db_column('discounts', 'id')]?>"/>
								<?php echo $row[$this->flexi_cart_admin->db_column('discounts', 'code')]; ?>
							</td>
							<td class="align_ctr">
							<?php if ($row[$this->flexi_cart_admin->db_column('discounts', 'usage_limit')] > 0) { ?>
								Available
							<?php } else { ?>
								Used
							<?php } ?>
							</td>
							<td class="align_ctr">
								&pound;<?php echo $row[$this->flexi_cart_admin->db_column('discounts', 'value_discounted')]; ?>
							</td>
							<td class="align_ctr">
								<?php echo date('jS M Y', strtotime($row[$this->flexi_cart_admin->db_column('discounts', 'expire_date')])); ?>
							</td>
							<td class="align_ctr">
								<?php $status = (bool)$row[$this->flexi_cart_admin->db_column('discounts', 'status')]; ?>
								<input type="hidden" name="update[<?php echo $voucher_id; ?>][status]" value="0"/>
								<input type="checkbox" name="update[<?php echo $voucher_id; ?>][status]" value="1" <?php echo set_checkbox('update['.$voucher_id.'][status]','1', $status); ?>/>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">
								<input type="submit" name="update_vouchers" value="Update Vouchers" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tfoot>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="5">
								There are no vouchers available to view for this user.
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>				
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


