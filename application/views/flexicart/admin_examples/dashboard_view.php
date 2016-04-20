<?php if (! empty($message)) { ?>

<div id="message"> <?php echo $message; ?> </div>
<?php } ?>
<div class="panel panel-primary">
  <div class="panel-heading">Sales module configuration</div>
  <div class="panel-body" style="padding:0">
    <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
      <li class="items-tab active"><a href="#items" class="tab" data-toggle="tab">Items</a></li>
      <li class="orders-tab"><a href="#orders" class="tab" data-toggle="tab">Orders</a></li>
      <li class="locations-tab"><a href="#locations" class="tab" data-toggle="tab">Locations</a></li>
      <li class="shipping-tab"><a href="#shipping" class="tab" data-toggle="tab">Shipping</a></li>
      <li class="discounts-tab"><a href="#discounts" class="tab" data-toggle="tab">Discounts</a></li>
      <li class="rewards-tab"><a href="#rewards" class="tab" data-toggle="tab">Rewards</a></li>
      <li class="config-tab"><a href="#config" class="tab" data-toggle="tab">Configuration</a></li>
    </ul>
    
    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane active" id="items">
        <h4>Item Management</h4>
        <div class="frame_note">
          <p class="small"> Welcome to the 121system store admin area.<br/>
            From here you can manage stock, shipping rates, tax rates, discounts and currencies. </p>
          <ul>
            <li> <a href="<?php echo $base_url; ?>admin/shop/items">Manage Items</a> </li>
          </ul>
        </div>
      </div>
      <div class="tab-pane" id="orders">
        <h4>Orders</h4>
        <div class="frame_note"> <small>View and manage customer orders that have been saved by flexi cart.</small>
          <hr/>
          <ul>
            <li> <a href="<?php echo $base_url; ?>admin/shop/orders">Manage Orders</a> </li>
          </ul>
        </div>
      </div>
      <div class="tab-pane" id="locations">
        <h4>Locations and Zones</h4>
        <div class="frame_note"> <small>Locations and zones can be setup that allow custom shipping, tax and discount rules to be created that are then applied depending on the customers location.</small>
          <hr/>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/location_types">Manage Locations</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_location_type">Add New Location Type</a> </li>
          </ul>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/zones">Manage Zones</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_zone">Add New Zone</a> </li>
          </ul>
          <!--<ul class="spacer_250">
						<li>
							<a href="<?php echo $base_url; ?>admin/shop/demo_location_menus">Location Menu Demos</a>
						</li>
					</ul>--> 
        </div>
      </div>
      <div class="tab-pane" id="shipping">
        <h4>Shipping and Taxes</h4>
        <div class="frame_note"> <small>Shipping options and taxes can be setup to return an appropriate shipping and tax rate depending on the customers location.<br/>
          In addition, individual items can have specific shipping and tax rates applied to them.</small>
          <hr/>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/shipping">Manage Shipping Options</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_shipping">Add New Shipping Option</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/items">Add New Item Shipping Rule</a> </li>
          </ul>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/tax">Manage Taxes</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_tax">Add New Tax</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/items">Add New Item Tax Rate</a> </li>
          </ul>
        </div>
      </div>
      <div class="tab-pane" id="discounts">
        <h4>Discounts</h4>
        <div class="frame_note"> <small>Discounts can be setup with a wide range of rule conditions.<br/>
          The discounts can then be applied to specific items, groups of items or can be applied across the entire cart.</small>
          <hr/>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/item_discounts">Manage Item Discounts</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/summary_discounts">Manage Summary Discounts</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_discount">Add New Discount</a> </li>
          </ul>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/discount_groups">Manage Discount Groups</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_discount_group">Add New Discount Group</a> </li>
          </ul>
        </div>
      </div>
      <div class="tab-pane" id="rewards">
        <h4>Reward Points and Vouchers</h4>
        <div class="frame_note"> <small>Customers can earn reward points when purchasing cart items. The reward points can then be converted to vouchers that can be used to buy other items.</small>
          <hr/>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/user_reward_points">Manage User Reward Points</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/vouchers">Manage User Reward Vouchers</a> </li>
          </ul>
        </div>
      </div>
      <div class="tab-pane" id="config">
        <h4>Currency, Order Status and Cart Configuration</h4>
        <div class="frame_note"> <small>Many configuration options within the cart can be set via the database, eliminating the need to update settings via a config file.</small>
          <hr/>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/currency">Manage Currencies</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_currency">Add New Currency</a> </li>
          </ul>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/order_status">Manage Order Statuses</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/insert_order_status">Add New Order Status</a> </li>
          </ul>
          <ul class="spacer_250">
            <li> <a href="<?php echo $base_url; ?>admin/shop/config">Manage Cart Configuration</a> </li>
            <li> <a href="<?php echo $base_url; ?>admin/shop/defaults">Manage Cart Defaults</a> </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
