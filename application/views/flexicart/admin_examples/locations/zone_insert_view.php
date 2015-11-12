			<div class="panel panel-primary">
            <div class="panel-heading"><?php echo $title ?></div>
<div class="panel-body">
<?php echo form_open(current_url());?>
				<h1>Insert New Location Zone</h1>
				<p><a href="<?php echo $base_url; ?>admin/shop/zones">Manage Zones</a></p>

				<table class="table" >
					<thead>
						<tr>
							<th class="info_req tooltip_trigger"
								title="<strong>Field Required</strong><br/>The name of the zone.">
								Name
							</th>
							<th class="tooltip_trigger"
								title="A brief description of the purpose of the zone and the regions covered.">
								Description
							</th>
							<th class="spacer_75 align_ctr tooltip_trigger" 
								title="If checked, the zone will be set as 'active'.">
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
								<input type="text" name="insert[<?php echo $row_id; ?>][name]" value="<?php echo set_value('insert['.$row_id.'][name]');?>" class="width_175"/>
							</td>
							<td>
								<textarea name="insert[<?php echo $row_id; ?>][description]" class="width_400"><?php echo set_value('insert['.$row_id.'][description]');?></textarea>
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
							<td colspan="4">
								<input type="submit" name="insert_zone" value="Insert New Location Zones" class="link_button btn btn-default large"/>
							</td>
						</tr>
					</tbody>
				</table>
			<?php echo form_close();?>

		</div>
	</div>
	
	<!-- Footer -->  


