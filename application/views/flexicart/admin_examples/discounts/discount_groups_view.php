			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Discount Item Groups</h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/insert_discount_group">Insert New Group</a><br/>
					<a href="<?php echo $base_url; ?>admin/shop/item_discounts">Manage Item Discounts</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/summary_discounts">Manage Summary Discounts</a> 
				</p>

				<table class="table" >
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/>Set the name of the discount item group.">
								Group Name
							</th>
							<th class="spacer_175 align_ctr tooltip_trigger"
								title="Manage items within the discount item group.">
								Manage
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="If checked, the discount item group will be set as 'active'.">
								Status
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="If checked, the row will be deleted upon the form being updated.">
								Delete
							</th>
						</tr>
					</thead>
				<?php if (! empty($discount_group_data)) { ?>	
					<tbody>
					<?php 
						foreach ($discount_group_data as $row) {
							$disc_group_id = $row[$this->flexi_cart_admin->db_column('discount_groups', 'id')];
					?>
						<tr>
							<td>
								<input type="hidden" name="update[<?php echo $disc_group_id; ?>][id]" value="<?php echo $disc_group_id; ?>"/>
								<input type="text" name="update[<?php echo $disc_group_id; ?>][name]" value="<?php echo set_value('update['.$disc_group_id.'][name]', $row[$this->flexi_cart_admin->db_column('discount_groups', 'name')]); ?>" class="width_250"/>
							</td>
							<td class="align_ctr">
								<a href="<?php echo $base_url; ?>admin/shop/update_discount_group/<?php echo $disc_group_id; ?>">Manage Items in Group</a><br/>
								<a href="<?php echo $base_url; ?>admin/shop/insert_discount_group_items/<?php echo $disc_group_id; ?>">Insert New Items to Group</a>
							</td>
							<td class="align_ctr">
								<?php $status = (bool)$row[$this->flexi_cart_admin->db_column('discount_groups', 'status')]; ?>
								<input type="hidden" name="update[<?php echo $disc_group_id; ?>][status]" value="0"/>
								<input type="checkbox" name="update[<?php echo $disc_group_id; ?>][status]" value="1" <?php echo set_checkbox('update['.$disc_group_id.'][status]','1', $status); ?>/>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="update[<?php echo $disc_group_id; ?>][delete]" value="0"/>
								<input type="checkbox" name="update[<?php echo $disc_group_id; ?>][delete]" value="1"/>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4">
								<input type="submit" name="update_discount_groups" value="Update Discount Item Groups" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tfoot>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="4">
								There are no discount item groups setup to view.<br/>
								<a href="<?php echo $base_url; ?>admin/shop/insert_discount_group">Insert New Discount Item Group</a>
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


