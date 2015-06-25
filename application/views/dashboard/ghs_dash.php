<div class="row">

 
 <div class="col-md-6">  
    <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-search fa-fw"></i> Search</div>
              <div class="panel-body search-panel">


<div class="col-md-6">
<form class="form">
<div class="form-group">
<label>House Number</label>
<input type="text" class="form-control" placeholder="Enter the house number" name="add1"/>
</div>
<div class="form-group">
<label>Postcode</label>
<input type="text" class="form-control" placeholder="Enter the full postcode" name="postcode"/>
</div>
<div class="form-group">
<button class="btn btn-primary" id="search-address">Search by address</button>
</div>
</form>
</div>

<div class="col-md-6">
<form>
<div class="form-group">
<label>GHS Reference</label>
<input type="text" class="form-control" placeholder="Enter the GHS Reference eg. SW12345" name="ghs_ref"/>
</div>
<div class="form-group">
<button class="btn btn-primary" id="search-reference">Search by reference</button>
</div>
</form>
</div>
<hr>

<div class="row"></div>
<div class="col-md-12">
<div id="search-results">

</div>
</div>

</div>

<div class="col-md-6">
 <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-exclamation-triangle fa-fw"></i> Rebook (urgent)</div>
              <div class="panel-body urgent-panel">
<img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        <!-- /.col-lg-4 --> 
   </div>
 </div>

            </div>
        <!-- /.col-lg-4 --> 
   </div>
      <!-- /.row --> 

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- Page-Level Plugin Scripts - Dashboard --> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
	$(document).ready(function(){
		ghs.urgent_panel();
		ghs.init();
	});
	</script> 