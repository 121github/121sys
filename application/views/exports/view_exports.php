<div id="wrapper">
  <div id="sidebar-wrapper">
 <?php  $this->view('dashboard/navigation.php',$page) ?>
  </div>
<div id="page-content-wrapper">
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Special Exports</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Available Exports
            <div class="pull-right">
              <div class="btn-group">
                <button class="daterange btn btn-default btn-xs" style="margin-right:5px"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "2nd Jul - Today"; ?> </span></button>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <table class="table">
              <tr>
                <td> Sample Export</td>
                <td><form method="post" action="<?php echo base_url()."exports/sample_export" ?>">
                    <input type="hidden" name="date_from">
                    <input type="hidden" name="date_to">
                    <button>Export Now</button>
                  </form></td>
              <tr>
            </table>
          </div>
          <!-- /.panel-body --> 
        </div>
      </div>
      
      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --></div>
</div>
<script>
	$(document).ready(function(){
	$('.daterange').daterangepicker({
	  opens:"left",
      ranges: {
         'Today': [moment(), moment()],
         'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
         'Last 7 Days': [moment().subtract('days', 6), moment()],
         'Last 30 Days': [moment().subtract('days', 29), moment()],
         'This Month': [moment().startOf('month'), moment().endOf('month')],
         'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
      },
	  format: 'DD/MM/YYYY',
	  minDate: "02/07/2014",
      startDate: "02/07/2014",
      endDate: moment()
    },
    function(start, end, element) {
	var $btn = this.element;
    $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
	$btn.closest('.panel').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
	$btn.closest('.panel').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
	});
	
	
    });
	</script>