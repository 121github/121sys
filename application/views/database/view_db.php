
<div id="wrapper">
<div id="sidebar-wrapper">
  <?php  $this->view('dashboard/navigation.php',$page) ?>
</div>
<div id="page-content-wrapper">
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Database Admin</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary groups-panel">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Database Admin
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <?php $this->view('forms/edit_groups_form.php'); ?>
            <table class="table ajax-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Current Version</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><?php echo date('d/m/Y'); ?></td>
                  <td class="current-version"><?php echo $version ?></td>
                </tr>
              </tbody>
            </table>
            <ul class="list-group">
              <li class="list-group-item">
                <button class="btn drop-tables btn-danger" style="width:130px">Drop tables</button>
                Drop all tables in the database. This will destroy the current databse <img style="display:none" class="pull-right" src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
              <li class="list-group-item">
                <button class="btn create-tables btn-info" style="width:130px">Update Database</button>
                Updates the database to the latest version<img class="pull-right" style="display:none" src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
              <li class="list-group-item">
                <button class="btn btn-info add-data" style="width:130px">Load demo data</button>
                Adds some dummy client data <img class="pull-right" style="display:none" src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
            </ul>
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
	function drop_tables($btn,create){
		$.ajax({
		url:helper.baseUrl+'database/drop_tables',
		type:"POST",
		dataType:"JSON",
		beforeSend: function(){
			$btn.parent().find('img').show();
		}
		}).done(function(){
			$('.current-version').text("No Database");
				$btn.parent().find('img').hide();	
		});
	}
	
	function create_tables($btn){
		$.ajax({url:helper.baseUrl+'migrate',
		dataType:"JSON",
		beforeSend: function(){ 
		$btn.parent().find('img').show();
		}
		}).done(function(response){
			$btn.parent().find('img').hide();
			if(response.success){
			$('.current-version').text(response.version);
			flashalert.success("The tables have been created");
			} else {
			flashalert.warning(response);
			}
		});
		
	}
	
		function add_data($btn){
		$.ajax({url:helper.baseUrl+'database/add_data',
		dataType:"JSON",
		beforeSend: function(){
		$btn.parent().find('img').show();	
		}
		}).done(function(response){
			$btn.parent().find('img').hide();
			if(response.success){
			$('.current-version').text(response.version);
			flashalert.success("Sample data was added");
			} else {
			flashalert.warning(response);
			}
		});
		
	}
	
		$(document).on("click",".create-tables",function(){
		create_tables($(this));
	});
	
	$(document).on("click",".drop-tables",function(){
		drop_table_confirmation($(this),false);
	});
	
	$(document).on("click",".add-data",function(){
		add_data($(this));
	});
		 function drop_table_confirmation($btn) {
        $('.modal-title').text('Please confirm');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html('Are you sure you want to drop all the tables in the database?<br><b class="red">This will erase all the data in the system</b>');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            drop_tables($btn);
            $('#modal').modal('toggle');
        });
		 }
		
});
</script> 
