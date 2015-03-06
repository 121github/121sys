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
          <div class="panel-heading"> 
          <i class="fa fa-bar-chart-o fa-fw"></i>Latest Call History
            <div class="pull-right">
                         <form class="history-filter" data-func="history_panel">
            	  <input type="hidden" name="date_from" value="">
                  <input type="hidden" name="date_to" value="">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" > <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="filter" data-ref="campaign" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 </form>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <div class="row">
              <div class="col-lg-12">
              <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
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
                </div>
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
            <div class="pull-right">
                         <form class="comments-filter" data-func="comments_panel">
            	  <input type="hidden" name="date_from" value="">
                  <input type="hidden" name="date_to" value="">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
                  <input type="hidden" name="comments">
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" > <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="filter" data-ref="campaign" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 <div class="btn-group">
              <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <i class="glyphicon glyphicon-comment"></i> </button>
              <ul class="dropdown-menu slidedown">
                <li> <a href="#" id="1" class="filter" data-ref="comments"> <i class="fa fa-refresh fa-fw"></i> Survey Comments </a> </li>
                <li> <a href="#" id="2" class="filter" data-ref="comments"> <i class="fa fa-check-circle fa-fw"></i> Record Comments </a> </li>
                <li> <a href="#" id="3" class="filter" data-ref="comments"> <i class="fa fa-times fa-fw"></i> Sticky Notes </a> </li>
                <li class="divider"></li>
                <li><a class="filter" data-ref="comments">Show All</a> </li>
              </ul>
              </div>
                </form>
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
        <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
        <div class="panel panel-primary">
          <div class="panel-heading"> <i class="fa fa-clock-o fa-fw"></i> System Statistics
            <div class="pull-right">
  <form class="stats-filter" data-func="system_stats">
            	  <input type="hidden" name="date_from" value="">
                  <input type="hidden" name="date_to" value="">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" > <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($email_campaigns as $row): ?>
	                    <li><a href="#" class="filter" data-ref="campaign" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <!-- doesnt fit on panel
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 -->
                 </form>
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
             <form class="emails-filter" data-func="emails_panel">
            	  <input type="hidden" name="date_from" value="">
                  <input type="hidden" name="date_to" value="">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" > <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($email_campaigns as $row): ?>
	                    <li><a href="#" class="filter" data-ref="campaign" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <!-- doesnt fit on panel
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 -->
                 </form>
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
     <form class="outcomes-filter" data-func="outcomes_panel">
            	  <input type="hidden" name="date_from" value="">
                  <input type="hidden" name="date_to" value="">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" > <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="filter" data-ref="campaign" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a> </li>
	                  </ul>
                  </div>
                   <!-- doesnt fit on panel
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 -->
                 </form>
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
		dashboard.init();
		dashboard.history_panel();
		dashboard.outcomes_panel();
		dashboard.system_stats();
		dashboard.comments_panel();
		dashboard.emails_panel();
	});
	</script> 
