			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Discount Item Group and Items</h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/discount_groups">Manage Discount Item Groups</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/insert_discount_group_items/<?php echo $group_data[$this->flexi_cart_admin->db_column('discount_groups', 'id')]; ?>">Insert Items to Discount Item Group</a>
				</p>	
				
				<table class="table" >
					<caption>Discount Item Group</caption>
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/>Set the name of the discount item group.">
								Group Name
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="If checked, the discount item group will be set as 'active'.">
								Status
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<input type="text" name="update_group[name]" value="<?php echo set_value('update_group[name]', $group_data[$this->flexi_cart_admin->db_column('discount_groups', 'name')]); ?>" class="width_250"/>
							</td>
							<td class="align_ctr">
								<?php $status = (bool) $group_data[$this->flexi_cart_admin->db_column('discount_groups', 'status')]; ?>
								<input type="hidden" name="update_group[status]" value="0"/>
								<input type="checkbox" name="update_group[status]" value="1" <?php echo set_checkbox('update_group[status]','1', $status); ?>/>
							</td>
						</tr>
					</tbody>
				</table>
				
				<table class="table" >
					<caption>Current Items in Group</caption>
					<thead>
						<tr>
							<th>Item Name</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="If checked, the row will be deleted upon the form being updated.">
								Delete
							</th>
						</tr>
					</thead>
				<?php if (! empty($group_item_data)) { ?>	
					<tbody>
					<?php 
						foreach($group_item_data as $item) { 
							$item_id = $item['item_id'];
					?>
						<tr>
							<td>
								<input type="hidden" name="delete_item[<?php echo $item_id;?>][id]" value="<?php echo $item[$this->flexi_cart_admin->db_column('discount_group_items', 'id')]; ?>"/>
								<?php echo $item['item_name']; ?>
							</td>
							<td class="align_ctr">
								<input type="hidden" name="delete_item[<?php echo $item_id;?>][delete]" value="0"/>
								<input type="checkbox" name="delete_item[<?php echo $item_id;?>][delete]" value="1"/>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2">
								<input type="submit" name="update_discount_group_items" value="Update Discount Item Group and Items" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tfoot>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="2">
								There are no items in this discount item group that are setup to view.<br/>
								<a href="<?php echo $base_url; ?>admin/shop/insert_discount_group_items/<?php echo $group_data[$this->flexi_cart_admin->db_column('discount_groups', 'id')]; ?>">Insert Items to Discount Item Group</a>								
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


