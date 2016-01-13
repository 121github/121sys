 <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Slots 
          <?php if(in_array("slot availability",$_SESSION['permissions'])){ ?>
          <a class="pull-right btn btn-default" href="availability">Edit Availability</a>
          <?php } ?></h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      
       <div class="panel panel-primary" id="slots-panel">
            <div class="panel-heading">Slots     <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle add-btn" data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> Add New</button>
              </div>
            </div>        
            </div>
 <div class="panel-body">
<div id="form-container">
<?php $this->view('forms/edit_slots.php',$options); ?>
</div>
<div id="table-container">
</div>
</div>
</div>
<script>
$(document).ready(function(){
	admin.init();
	admin.slots.init();
});
</script>