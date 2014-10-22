
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
                  <th class="db_operation" style="width: 25%;"></th>
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
              <li class="list-group-item" style="font-weight: bold;">SCHEMA</li>
              <li class="list-group-item">
                <button class="btn drop-tables btn-danger" style="width:130px">Drop tables</button>
                Drop all tables in the database. This will destroy the current databse <img style="display:none" class="pull-right" src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
              <li class="list-group-item">
                <button class="btn create-tables btn-success" style="width:130px">Update Schema</button>
                Updates the database to the latest version<img class="pull-right" style="display:none" src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
               
              <li class="list-group-item" style="font-weight: bold;">DATA</li>
              <li class="list-group-item">
                <button class="btn reset-data btn-danger" style="width:130px">Reset default data</button>
                Reset the data to the default version. This will destroy the current data <img style="display:none" class="pull-right" src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
              <li class="list-group-item">
                <button class="btn btn-success add-real-data" style="width:130px">Load real data</button>
                Adds real data (Users) <img class="pull-right" style="display:none" src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
              <li class="list-group-item">
                <button class="btn btn-warning add-data" style="width:130px">Load demo data</button>
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

	$(document).on("click",".create-tables",function(){
		create_tables($(this));
	});
	
	$(document).on("click",".drop-tables",function(){
		drop_table_confirmation($(this),false);
	});
	
	$(document).on("click",".add-data",function(){
		add_demo_data_confirmation($(this),false);
	});

	$(document).on("click",".add-real-data",function(){
		add_real_data_confirmation($(this),false);
	});

	$(document).on("click",".reset-data",function(){
		reset_data_confirmation($(this),false);
	});
	
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
	
	function add_demo_data($btn, $reset){
		if ($reset) {
			$(".db_operation").html("<span class='red'>Resetting the default data...</span>");
			$.ajax({url:helper.baseUrl+'database/reset_data',
				dataType:"JSON",
				beforeSend: function(){
				$btn.parent().find('img').show();	
				}
				}).done(function(response){
					$btn.parent().find('img').hide();
					if(response.success){
						flashalert.success("The default data was restored");
						add_demo_data($btn, false);
					} else {
						flashalert.warning(response);
					}
				});
		}
		else {
			$(".db_operation").html("<span class='red'>Loading the demo data...</span>");
			$.ajax({url:helper.baseUrl+'database/add_data',
				dataType:"JSON",
				beforeSend: function(){
				$btn.parent().find('img').show();	
				}
				}).done(function(response){
					$btn.parent().find('img').hide();
					if(response.success){
						flashalert.success("Sample data was added");
						$(".db_operation").html("<span class='green'>Demo data loaded successfully!</span>");
					} else {
						flashalert.warning(response);
					}
				});
		}
		
		
	}

	function add_real_data($btn, $sample){
		if ($sample) {
			$(".db_operation").html("<span class='red'>Loading the real data...</span>");
			$.ajax({url:helper.baseUrl+'database/add_real_data',
			dataType:"JSON",
			beforeSend: function(){
			$btn.parent().find('img').show();	
			}
			}).done(function(response){
				$btn.parent().find('img').hide();
				if(response.success){
					flashalert.success("Real data was added");
					$(".db_operation").html("<span class='green'>Real data loaded successfully!</span>");
					add_demo_data($btn, false);
				} else {
					flashalert.warning(response);
				}
			});
		}
		else {
			$.ajax({url:helper.baseUrl+'database/add_real_data',
				dataType:"JSON",
				beforeSend: function(){
				$btn.parent().find('img').show();	
				}
				}).done(function(response){
					$btn.parent().find('img').hide();
					if(response.success){
						flashalert.success("Real data was added");
						$(".db_operation").html("<span class='green'>Real data loaded successfully!</span>");
					} else {
						flashalert.warning(response);
					}
				});
		}
		
	}

	function reset_data($btn){
		$(".db_operation").html("<span class='red'>Resetting the default data...</span>");
		$.ajax({url:helper.baseUrl+'database/reset_data',
		dataType:"JSON",
		beforeSend: function(){
		$btn.parent().find('img').show();	
		}
		}).done(function(response){
			$btn.parent().find('img').hide();
			if(response.success){
				flashalert.success("The default data was restored");
				$(".db_operation").html("<span class='green'>Default data loaded successfully!</span>");
			} else {
				flashalert.warning(response);
			}
		});
	}

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

	function add_demo_data_confirmation($btn) {
        $('.modal-title').text('Please confirm');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html('This action will truncate all the tables before add the sample data. Are you sure you want to continue?<br><b class="red">This will erase all the data in the system (Press NO if you want to load the demo data and mantain the default data)</b>');
        $(".discard-modal").off('click').show();
        $('.discard-modal').on('click', function(e) {
        	add_demo_data($btn,false);
        	$('#modal').modal('toggle');
        	$('.discard-modal').hide();
        });
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
        	add_demo_data($btn,true);
            $('#modal').modal('toggle');
            $('.discard-modal').hide();
        });
        $('.close-modal').on('click', function(e) {
        	$('.discard-modal').hide();
        });
	}

	function add_real_data_confirmation($btn) {
        $('.modal-title').text('Please confirm');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html('Do you want to add some sample data after the real data?<br><b class="red">Press NO if you want to load the real data without sample data</b>');
        $(".discard-modal").off('click').show();
        $('.discard-modal').on('click', function(e) {
        	add_real_data($btn,false);
        	$('#modal').modal('toggle');
        	$('.discard-modal').hide();
        });
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
        	add_real_data($btn,true);
            $('#modal').modal('toggle');
            $('.discard-modal').hide();
        });
        $('.close-modal').on('click', function(e) {
        	$('.discard-modal').hide();
        });
	}

	function reset_data_confirmation($btn) {
        $('.modal-title').text('Please confirm');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html('This action will truncate all the tables and then restore the data as the default version. Are you sure you want to continue?<br><b class="red">This will erase all the data in the system</b>');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
        	reset_data($btn);
            $('#modal').modal('toggle');
        });
	}
		
});
</script> 
