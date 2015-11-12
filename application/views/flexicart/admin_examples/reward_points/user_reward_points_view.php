			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Manage User Reward Points</h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/vouchers">Manage Reward Vouchers</a>
				</p>

				<table class="table" >
					<thead>
						<tr>
							<th>User Name</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="The number of reward points that have been earnt by the user. Any cancelled or refunded items are not included in the total.">
								Total
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="The number of reward points that are pending activation. Once an ordered item has been 'Completed' (Shipped), the earnt points will be enabled after a set number of days.">
								Pending
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="The number of reward points that have been earnt by the user, which are active and can be converted to vouchers.">
								Active
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="The number of reward points that have expired before they were converted to a reward voucher.">
								Expired
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="The number of reward points that have been converted to reward vouchers by the user.">
								Converted
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger"
								title="The number of reward points that have been cancelled due to an ordered item being cancelled or refunded.">
								Cancelled
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger"
								title="View the customers history of reward point earnings and conversions.">
								History
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger"
								title="View and manage the customers reward points vouchers.">
								Vouchers
							</th>
						</tr>
					</thead>
				<?php if (! empty($user_data)) { ?>
					<tbody>
					<?php foreach ($user_data as $row) { ?>
						<tr>
							<td>
								<?php echo $row['user_name']; ?>
							</td>
							<td class="align_ctr">
								<?php echo $row['total_points']; ?>
							</td>
							<td class="align_ctr">
								<?php echo $row['total_points_pending']; ?>
							</td>
							<td class="align_ctr">
								<?php echo $row['total_points_active']; ?>
							</td>
							<td class="align_ctr">
								<?php echo $row['total_points_expired']; ?>
							</td>
							<td class="align_ctr">
								<?php echo $row['total_points_converted']; ?>
							</td>
							<td class="align_ctr">
								<?php echo $row['total_points_cancelled']; ?>
							</td>
							<td class="align_ctr">
								<a href="<?php echo $base_url; ?>admin/shop/user_reward_point_history/<?php echo $row['user_id']; ?>">View</a>
							</td>
							<td class="align_ctr">
								<a href="<?php echo $base_url; ?>admin/shop/user_vouchers/<?php echo $row['user_id']; ?>">View</a> | 
								<a href="<?php echo $base_url; ?>admin/shop/convert_reward_points/<?php echo $row['user_id']; ?>">Convert</a>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="6">
								There are no users available to view.
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>				
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


