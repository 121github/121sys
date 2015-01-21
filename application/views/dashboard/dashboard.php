<div id="wrapper">
<div id="sidebar-wrapper">
 <?php  $this->view('dashboard/navigation.php',$page) ?>
</div>
<div id="page-content-wrapper">
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Dashboard</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-8">
    
        <!-- /.panel -->
        <div class="panel panel-primary call-history">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Latest Call History
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>  Filter</button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($campaigns as $row): ?>
                  <li><a href="#" class="history-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="history-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-12">
                <table class="table table-bordered table-hover table-striped table-responsive">
                  <thead>
                    <tr>
                      <th>Campaign name</th>
                      <th>Date</th>
                      <th>Time</th>
                      <th>User</th>
                      <th>Outcome</th>
                      <th>View</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!--The contents of this table is loaded via an ajax function in dashboard.js -->
                  </tbody>
                </table>
                
                <!-- /.table-responsive --> 
              </div>
              <!-- /.col-lg-4 (nested) -->
              <div class="col-lg-8">
                <div id="morris-bar-chart"></div>
              </div>
              <!-- /.col-lg-8 (nested) --> 
            </div>
            <!-- /.row --> 
          </div>
          <!-- /.panel-body --> 
        </div>
        <!-- /.panel -->
        <div class="panel panel-primary">
          <div class="panel-heading"> <i class="fa fa-comments fa-fw"></i> Latest Comments
            <div class="btn-group pull-right">
              <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-comment"></i> </button>
              <ul class="dropdown-menu slidedown">
                <li> <a href="#" id="1" class="comment-filter"> <i class="fa fa-refresh fa-fw"></i> Survey Comments </a> </li>
                <li> <a href="#" id="2" class="comment-filter"> <i class="fa fa-check-circle fa-fw"></i> Record Comments </a> </li>
                <li> <a href="#" id="3" class="comment-filter"> <i class="fa fa-times fa-fw"></i> Sticky Notes </a> </li>
                <li class="divider"></li>
                <li><a class="comment-filter" ref="#">Show All</a> </li>
              </ul>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <ul class="chat">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </ul>
          </div>
        </div>
      </div>
      <!-- /.col-lg-8 -->
    
      <div class="col-lg-4">
        <?php if(!in_array("set call outcomes",$_SESSION['permissions'])){ ?>
        <div class="panel panel-primary">
          <div class="panel-heading"> <i class="fa fa-clock-o fa-fw"></i> System Statistics
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>  Filter</button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($campaigns as $row): ?>
                  <li><a href="#" class="stats-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="stats-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <ul class="timeline">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </ul>
          </div>
          <!-- /.panel-body -->
           
        </div>
       <?php } ?> 
       
        <div class="panel panel-primary">
          <div class="panel-heading"> <i class="fa fa-clock-o fa-fw"></i>Email Statistics
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>  Filter</button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($email_campaigns as $row): ?>
                  <li><a href="#" class="email-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="email-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="email-stats">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
          </div>
          <!-- /.panel-body -->
           
        </div>
       
       
        <div class="panel panel-primary">
          <div class="panel-heading"> <i class="fa fa-bell fa-fw"></i> Todays Outcomes
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>  Filter</button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($campaigns as $row): ?>
                  <li><a href="#" class="outcome-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="outcome-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body"
            >
            <div class="outcome-stats"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
            <!--<a href="#" class="btn btn-default btn-block">View Details</a> </div>--></div>
          <!-- /.list-group --> 
          <!-- /.panel-body --> 
        </div>
        <!-- /.panel -->
    
          <!-- /.panel --> 
          
          <!-- /.panel --> 
          <!-- /.panel .chat-panel --> 
        </div>
        <!-- /.col-lg-4 --> 
      </div>
      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --></div>
</div>

<script>
	$(document).ready(function(){

	
		/*first load the charts for this dash board */

		/* load any other panels on this dashboard */
		dashboard.history_panel();
		dashboard.outcomes_panel();
		dashboard.system_stats();
		dashboard.comments_panel();
		dashboard.emails_panel();
		
		/* initialize any click listeners - mainly the filters on the panels*/
		$(document).on("click",".history-filter",function(e){
			e.preventDefault();
			dashboard.history_panel($(this).attr('id'))
		});
		$(document).on("click",".outcome-filter",function(e){
			e.preventDefault();
			dashboard.outcomes_panel($(this).attr('id'))
		});
		$(document).on("click",".stats-filter",function(e){
			e.preventDefault();
			dashboard.system_stats($(this).attr('id'))
		});
		$(document).on("click",".comment-filter",function(e){
			e.preventDefault();
			dashboard.comments_panel($(this).attr('id'))
		});
		$(document).on("click",".email-filter",function(e){
			e.preventDefault();
			dashboard.emails_panel($(this).attr('id'))
		});

	});
	</script> 
