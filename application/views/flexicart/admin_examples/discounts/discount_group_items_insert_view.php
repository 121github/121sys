			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>						
				<h1>Insert New Items to Discount Item Group</h1>
				<p>
					<a href="<?php echo $base_url; ?>admin/shop/discount_groups">Manage Discount Item Groups</a> | 
					<a href="<?php echo $base_url; ?>admin/shop/update_discount_group/<?php echo $group_data[$this->flexi_cart_admin->db_column('discount_groups', 'id')]; ?>">
						Manage <?php echo $group_data[$this->flexi_cart_admin->db_column('discount_groups', 'name')]; ?> Discount Item Group
					</a>
				</p>	
				
				<table class="table" >
					<caption>
						Insert Items to <?php echo $group_data[$this->flexi_cart_admin->db_column('discount_groups', 'name')]; ?>
						<small>
							Note: The demo contains 5 demo items with the ids: 1, 2, 3, 4 and 5, with demo category ids of 1 and 2. 
							<a href="<?php echo $base_url; ?>admin/shop/items">See more item details</a>
						</small>
					</caption>
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="Set the SQL WHERE operator used between each generated WHERE statement.<br/> Note: The operator of the first row is ignored.">
								Operator
							</th>
							<th class="info_req tooltip_trigger"
								title="Set the column that will be compared against the 'filter value'.">
								Filter Column
							</th>
							<th class="info_req tooltip_trigger"
								title="Set the match method used to compare the 'filter column' against the 'filter value'.">
								Filter Match Method
							</th>
							<th class="tooltip_trigger"
								title="Set the value to be compared against the 'filter column'.<br/> For methods requiring multiple values (WHERE x BETWEEN y AND z), separate values using a comma (y,z).">
								Filter Value
							</th>
							<th class="spacer_150 align_ctr tooltip_trigger" 
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
								<select name="insert_item[<?php echo $row_id; ?>][logic_operator]" class="width_100">
									<option value="AND">AND</option>
									<option value="OR" <?php echo set_select('insert_item['.$row_id.'][logic_operator]', 'OR'); ?>>OR</option>
								</select>
							</td>
							<td>
								<select name="insert_item[<?php echo $row_id; ?>][column_name]" class="width_150">
									<option value="item_id" <?php echo set_select('insert_item['.$row_id.'][column_name]', 'item_id'); ?>>Item ID</option>
									<option value="item_name" <?php echo set_select('insert_item['.$row_id.'][column_name]', 'item_name'); ?>>Item Name</option>
									<option value="item_price" <?php echo set_select('insert_item['.$row_id.'][column_name]', 'item_price'); ?>>Item Price</option>
									<option value="item_weight" <?php echo set_select('insert_item['.$row_id.'][column_name]', 'item_weight'); ?>>Item Weight</option>
									<option value="item_cat_fk" <?php echo set_select('insert_item['.$row_id.'][column_name]', 'item_cat_fk'); ?>>Category ID</option>
									<option value="item_category_name" <?php echo set_select('insert_item['.$row_id.'][column_name]', 'item_category_name'); ?>>Category Name</option>
								</select>
							</td>
							<td>
								<select name="insert_item[<?php echo $row_id; ?>][comparison_operator]" class="width_200">
									<option value="=">Is equal to ( = )</option>
									<option value="!=" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', '!='); ?>>Is not equal to ( != )</option>
									<option value="<" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', '<'); ?>>Is less than ( < )</option>
									<option value="<=" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', '<='); ?>>Is less than or equal to ( <= )</option>
									<option value=">" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', '>'); ?>>Is more than ( > )</option>
									<option value=">=" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', '>='); ?>>Is more than or equal to( >= )</option>
									<option value="like" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'like'); ?>>Contains ( LIKE '%xxx%' )</option>
									<option value="not_like" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'not_like'); ?>>Does not contain ( NOT LIKE '%xxx%' )</option>
									<option value="begin_lik" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'begin_lik'); ?>>Begins with ( LIKE 'xxx%' )</option>
									<option value="not_begin_like" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'not_begin_like'); ?>>Does not begin with ( NOT LIKE 'xxx%' )</option>
									<option value="end_like" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'end_like'); ?>>Ends with ( LIKE '%xxx' )</option>
									<option value="not_end_like" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'not_end_like'); ?>>Does not end with ( NOT LIKE '%xxx' )</option>
									<option value="null" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'null'); ?>>Is null ( IS NULL )</option>
									<option value="not_null" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'not_null'); ?>>Is not null ( IS NOT NULL )</option>
									<option value="empty" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'empty'); ?>>Is empty ( = '' )</option>
									<option value="not_empty" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'not_empty'); ?>>Is not empty ( != '' )</option>
									<option value="between" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'between'); ?>>Is between ( BETWEEN X AND X )</option>
									<option value="not_between" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'not_between'); ?>>Is not between ( NOT BETWEEN X AND X )</option>
									<option value="in" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'in'); ?>>Is in list ( IN ('x', 'xx', 'xxx') )</option>
									<option value="not_in" <?php echo set_select('insert_item['.$row_id.'][comparison_operator]', 'not_in'); ?>>Is not in list ( NOT IN ('x', 'xx', 'xxx') )</option>
								</select>
							</td>
							<td>
								<input type="text" name="insert_item[<?php echo $row_id; ?>][value]" value="<?php echo set_value('insert_item['.$row_id.'][value]');?>" class="width_150"/>
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
							<td colspan="3" style="border-right:none;">
								<strong>Insert Item Method:</strong><br/>
								<select name="insert_method" class="tooltip_trigger" 
									title="Set whether to append or replace all items returned by the SQL WHERE statement to existing items within the group.">
									<option value="append">Append New Items to Existing Group Items</option>
									<option value="replace" <?php echo set_select('insert_method', 'replace'); ?>>Replace Existing Group Items with New Items</option>
								</select>
							</td>
							<td colspan="2" class="align_r">
								<input type="submit" name="insert_discount_group_items" value="Insert Discount Item Group Items" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>						

		</div>
	</div>
	
	<!-- Footer -->  


