      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Target Report</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-lg-12">
          
           <div class="panel panel-primary">
            <div class="panel-heading clearfix"> <i class="fa fa-bar-chart-o fa-fw"></i> Target Report
             <div class="pull-right">
              <div class="btn-group">
                      <form class="filter-form"> <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
<input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
   <button class="daterange btn btn-default btn-xs" style="margin-right:5px"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span></button>
   </form>
   </div></div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body target-data">
              This report is under construction
            </div>
            <!-- /.panel-body --> 
          </div>
          
        </div>

      <!-- /.row --> 


<!-- Page-Level Plugin Scripts - Dashboard --> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script> 


<script>
$(document).ready(function(){
	  targets.init();
});
</script>