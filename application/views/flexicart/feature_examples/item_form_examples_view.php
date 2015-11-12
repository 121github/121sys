<?php echo form_open($base_url.'standard_library/insert_form_item_to_cart');?>

					<div class="frame_note">
						<small>This example allows a user to add multiple items to the cart at the same time just by checking each items checkbox.</small>
						<hr/>
						
						<ul class="inl_list">
							<li>
								<strong>Example #204</strong>
							</li>
							<li>
								Price: <?php echo $this->flexi_cart->get_taxed_currency_value(18.25);?>
							</li>
							<li>
								<label for="ex204_qty">Quantity:</label>
								<input type="text" id="ex204_qty" name="item[204][quantity]" value="1" class="width_50 validate_integer"/>
							</li>
							<li>
								<label for="ex204_check">Check to add:</label>
								<input type="checkbox" id="ex204_check" name="item[204][checked]" value="1"/>
								
								<input type="hidden" name="item[204][item_id]" value="204"/>
								<input type="hidden" name="item[204][name]" value="Item #204, added multiple items via form"/>
								<input type="hidden" name="item[204][price]" value="18.25"/>
							</li>
						</ul>

						<ul class="inl_list">
							<li>
								<strong>Example #205</strong>
							</li>
							<li>
								Price: <?php echo $this->flexi_cart->get_taxed_currency_value(39.95);?>
							</li>
							<li>
								<label for="ex205_qty">Quantity:</label>
								<input type="text" id="ex205_qty" name="item[205][quantity]" value="1" class="width_50 validate_integer"/>
							</li>
							<li>
								<label for="ex205_check">Check to add:</label>
								<input type="checkbox" id="ex205_check" name="item[205][checked]" value="1"/>
								
								<input type="hidden" name="item[205][item_id]" value="205"/>
								<input type="hidden" name="item[205][name]" value="Item #205, added multiple items via form"/>
								<input type="hidden" name="item[205][price]" value="39.95"/>
							</li>
						</ul>
						
						<ul class="inl_list">
							<li>
								<strong>Example #206</strong>
							</li>
							<li>
								Price: <?php echo $this->flexi_cart->get_taxed_currency_value(30);?>
							</li>
							<li>
								<label for="ex206_qty">Quantity:</label>
								<input type="text" id="ex206_qty" name="item[206][quantity]" value="1" class="width_50 validate_integer"/>
							</li>
							<li>
								<label for="ex206_check">Check to add:</label>
								<input type="checkbox" id="ex206_check" name="item[206][checked]" value="1"/>
								
								<input type="hidden" name="item[206][item_id]" value="206"/>
								<input type="hidden" name="item[206][name]" value="Item #206, added multiple items via form"/>
								<input type="hidden" name="item[206][price]" value="30"/>
							</li>
						</ul>
						<hr/>
						
						<p>
							<strong>Add checked items to cart</strong> : 
							<input type="submit" name="add_multiple_items" value="Submit" class="link_button btn btn-default"/>
						</p>
					</div>
					
				<?php echo form_close();?>
			</fieldset>

		</div>
	</div>
	
	<!-- Footer -->  


