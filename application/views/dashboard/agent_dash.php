
<div id="wrapper">
  <div id="sidebar-wrapper">
    <?php  $this->view('dashboard/navigation.php',$page) ?>
  </div>
  <div id="page-content-wrapper">
    <div id="page-wrapper">
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Callback Dashboard</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-lg-12">
          <div class="panel panel-primary" id="a_favorites">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>My Favorites</div>
            <div class="panel-body favorites-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
          </div>
        </div>
        <!--
        <div class="col-lg-6">
          <div class="panel panel-primary" id="a_favorites">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>My Favorites</div>
            <div class="panel-body favorites-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
          </div>
        </div>
        -->
        </div>
        
          <div class="row">
            <div class="col-lg-12">
        <div class="panel panel-primary" id="a_callbacks">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Callbacks at around <?php echo date('g:i a'); ?>
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($campaigns as $row): ?>
                  <li><a href="#" class="tc-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="tc-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body timely-callbacks"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
          <!-- /.panel-body --> 
        </div>
        <div class="panel panel-primary" id="a_missed_callbacks">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Missed Callbacks
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($campaigns as $row): ?>
                  <li><a href="#" class="mc-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="mc-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body missed-callbacks"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
          <!-- /.panel-body --> 
        </div>
        <div class="panel panel-primary" id="a_upcoming_callbacks">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Upcoming Callbacks
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($campaigns as $row): ?>
                  <li><a href="#" class="uc-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="uc-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body upcoming-callbacks"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
          <!-- /.panel-body --> 
        </div>
      </div>
      
      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --> 
  </div>
</div>
<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- Page-Level Plugin Scripts - Dashboard --> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
	$(document).ready(function(){
		dashboard.urgent_panel();
		dashboard.favorites_panel();
		dashboard.missed_callbacks_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>);
		dashboard.upcoming_callbacks_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>);
		dashboard.timely_callbacks_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>);
			
			$(document).on("click",".tc-filter",function(e){
			e.preventDefault();
			dashboard.timely_callbacks_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>,$(this).attr('id'))
		});
			$(document).on("click",".uc-filter",function(e){
			e.preventDefault();
			dashboard.upcoming_callbacks_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>,$(this).attr('id'))
		});
			$(document).on("click",".mc-filter",function(e){
			e.preventDefault();
			dashboard.missed_callbacks_panel(<?php echo ($_SESSION['role']>1?$_SESSION['user_id']:"false") ?>,$(this).attr('id'))
		});
	});

	$("#my_favorites").on("click", function(){
		$("html,body").animate(
				{ scrollTop : $("#a_favorites").offset().top  },
				1500 
		);
	});
	$("#callbacks").on("click", function(){
		$("html,body").animate(
				{ scrollTop : $("#a_callbacks").offset().top  },
				1500 
		);
	});
	$("#missed_callbacks").on("click", function(){
		$("html,body").animate(
				{ scrollTop : $("#a_missed_callbacks").offset().top  },
				1500 
		);
	});
	$("#upcoming_callbacks").on("click", function(){
		$("html,body").animate(
				{ scrollTop : $("#a_upcoming_callbacks").offset().top  },
				1500 
		);
	});
	
	</script> 
