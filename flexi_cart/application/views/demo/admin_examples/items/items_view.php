
							
		<?php if (! empty($message)) { ?>
			<div id="message">
				<?php echo $message; ?>
			</div>
		<?php } ?>
		
			<?php echo form_open(current_url());?>		
            <div class="row">
    <div class="col-lg-12">				
				<div class="panel panel-primary">
                <div class="panel-heading">Manage Item Data
                
                
                <div class="pull-right">
				<a class="btn btn-xs btn-default" href="<?php echo $base_url; ?>admin_library/item_discounts">Manage Item Discounts</a>
				<a class="btn btn-xs btn-default" href="<?php echo $base_url; ?>admin_library/discount_groups">Manage Discount Groups</a>
</div>
</div>
<div class="panel-body">
				<table class="table">
					<thead>
						<tr>
							<th class="tooltip_trigger" 
								title="The name of the item that can then be viewed via the 'Add Database Items to Cart' examples page. <br/>Access this page via the 'Item Examples' on the nav menu.">
								Item Name
							</th>
							<th class="spacer_100 tooltip_trigger" 
								title="To emulate a real-world setup, an example 'Category' table is included to relate items to categories. <br/>The categories can be used when filtering items in discount groups.">
								Category
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="The weight of the item when added to the cart.">
								Weight
								(<?php echo $this->flexi_cart_admin->weight_symbol(); ?>)
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="The price of the item when added to the cart.">
								Price
								(<?php echo $this->flexi_cart_admin->currency_symbol(TRUE); ?>)
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="The current stock level of an item.">
								Stock Level
							</th>
							<th class="spacer_100 align_ctr tooltip_trigger" 
								title="If checked, the cart will auto allocate item stock when items are ordered or cancelled.">
								Auto Allocate Stock
							</th>
							<th class="spacer_125 align_ctr tooltip_trigger" 
								title="Manage specific shipping rules for the item.">
								Item Shipping Rules
							</th>
							<th class="spacer_125 align_ctr tooltip_trigger" 
								title="Manage specific tax rates for the item.">
								Item Taxes
							</th>
						</tr>
					</thead>
				<?php if (! empty($item_data)) { ?>	
					<tbody>
					<?php 
						foreach ($item_data as $row) { 
							$item_id = $row['item_id'];						
					?>
						<tr>
							<td>
								<input type="hidden" name="update[<?php echo $item_id; ?>][id]" value="<?php echo $item_id;?>"/>
								<?php echo $row['item_name']; ?>
							</td>
							<td>
								<small><?php echo $row['item_category_name']; ?></small>
							</td>
							<td class="align_ctr">
								<input type="text" name="update[<?php echo $item_id; ?>][weight]" value="<?php echo $row['item_weight']; ?>" class="width_50 align_ctr validate_decimal form-control"/>
							</td>
							<td class="align_ctr">
								<input type="text" name="update[<?php echo $item_id; ?>][price]" value="<?php echo $row['item_price']; ?>" class="width_50 align_ctr validate_decimal form-control"/>
							</td>
							<td class="align_ctr">
								<!-- 
									The item stock table setup is a little different from other tables.
									The table has a one-to-one relationship with the user defined item table (i.e. There can only be 1 stock record related per item record)
									This means that the stock data columns could in fact be included in the user defined item table.
								-->
								<input type="text" name="update[<?php echo $item_id; ?>][stock_quantity]" value="<?php echo $row[$this->flexi_cart_admin->db_column('item_stock', 'quantity')]; ?>" class="form-control width_50 align_ctr validate_integer"/>
							</td>
							<td class="align_ctr">
								<?php $auto_allocate_status = (bool)$row[$this->flexi_cart_admin->db_column('item_stock', 'auto_allocate_status')]; ?>
								<input type="hidden" name="update[<?php echo $item_id; ?>][auto_allocate_status]" value="0"/>
								<input type="checkbox" name="update[<?php echo $item_id; ?>][auto_allocate_status]" value="1" <?php echo set_checkbox('update[auto_allocate_status]','1', $auto_allocate_status); ?>/>
							</td>
							<td class="align_ctr">
								<a href="<?php echo $base_url; ?>admin_library/item_shipping/<?php echo $row['item_id']; ?>">Manage</a>
							</td>
							<td class="align_ctr">
								<a href="<?php echo $base_url; ?>admin_library/item_tax/<?php echo $row['item_id']; ?>">Manage</a>
							</td>
						</tr>
					<?php } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="8">
								<input type="submit" name="update_items" value="Update Items" class="link_button btn btn-default"/>
							</td>
						</tr>
					</tfoot>
				<?php } else { ?>
					<tbody>
						<tr>
							<td colspan="8">
								There are no items setup to view.
							</td>
						</tr>
					</tbody>
				<?php } ?>
				</table>				
			<?php echo form_close();?>						
</div>
</div>
</div>
</div>
	
<!-- Scripts -->  

