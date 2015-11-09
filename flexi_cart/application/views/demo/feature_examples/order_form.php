			<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Order</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<div class="panel panel-primary" id="order-panel">
  <div class="panel-heading">Order</div>
  <div class="panel-body" style="padding:0">
    <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
  <li class="items-tab active"><a href="#items" id="order-items-tab" class="tab" data-toggle="tab">Order form</a></li>
  <li class="orders-tab"><a href="#order-summary" id="order-summary-tab" class="tab" data-toggle="tab">Order Summary</a></li>
  </ul>
  <div class="tab-content">
  <div class="tab-pane active" id="items">
				<h4>Items</h4>
            <fieldset>
            <div class="form-group">
            <label>Category</label><br>
				<select class="selectpicker" id="category-select" title="select category">
                <option value="">--Select category--</option>
                <?php foreach($categories as $row){ ?>
					<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
					<?php } ?>
                </select>
                </div>
                 <div class="form-group">
                  <label>Subcategory</label><br>
                <select class="selectpicker" id="subcategory-select" title="select a category first"></select>
                </div>
					<div id="items-container">
                    
                    </div>	

						<hr/>
						
						<p>
			 <button id="confirm-order" disabled class="btn btn-primary">Proceed to summary</button>
						</p>
			</fieldset>
            </div>
              <div class="tab-pane" id="order-summary">
              
              
              
              </div>
            
            
            </div>
            
            </div>
            </div>
            
            <script>
			$(document).ready(function(){
				orders.init();
			});
			</script>