  <div class="page-header">
  <h2>System Alert</h2>
</div>
<div class="row">
        <div class="col-lg-12">
          
           <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-exclamation-triangle fa-fw"></i> <?php echo $title ?>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
            <p><?php echo $msg ?></p>
            
            <?php if ($title == "Data error"&&!empty($_SESSION['filter']['values'])){ ?>
            <p>Click here to reset your filter options</p>
				<button class="btn btn-info" id="clear-filter">Reset filter</button>
                <script>
				$(document).ready(function(){
					$('#clear-filter').click(function(e){
						e.preventDefault();
						 modals.clear_filters();
					});
					
				});
				</script>
			<?php } ?>
            
            </div>
            <!-- /.panel-body --> 
          </div>
          
        </div>

      <!-- /.row --> 
    </div>
