    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Transfer Daily Comparison Report</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary">
          <div class="panel-heading clearfix"> <i class="fa fa-bar-chart-o fa-fw"></i> Transfer Daily Comparison Report
            <div class="pull-right">
            <form class="filter-form">
              <div class="btn-group"> <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
<input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="team">
                  <input type="hidden" name="source">
                  <input type="hidden" name="view"> 
                  <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span></button></div>
                                    <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> View by</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                  
	                    <li><a href="#" class="view-filter" id="user_id">User</a> </li>
                        <li><a href="#" class="view-filter" id="team_id">Team</a> </li>
                        <li><a href="#" class="view-filter" id="campaign_id">Campaign</a> </li>
                        <li><a href="#" class="view-filter" id="source_id">Source</a> </li>
	                    <li class="divider"></li>
	                    <li><a class="view-filter" ref="#" style="color: green;">Date Only</a> </li>
	                  </ul>
                  </div>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Campaign <span class="caret"></span></button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="campaign-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="campaign-filter" ref="#" style="color: green;">All Campaigns</a> </li>
	                  </ul>
                  </div>
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Agent <span class="caret"></span></button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="agent-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="agent-filter" ref="#" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                  <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Team <span class="caret"></span></button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="team-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="team-filter" ref="#" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Source <span class="caret"></span></button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($sources as $row): ?>
	                    <li><a href="#" class="source-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="source-filter" ref="#" style="color: green;">All Sources</a> </li>
	                  </ul>
                  </div>
                </form>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body dailytransfer-data">
	          <table class="table ajax-table">
				<thead>
					<tr>
						<th>Date</th>
						<th>Name</th>
						<th>Transfers</th>
						<th>Cross Transfers</th>
						<th>Total Transfers</th>
						<th>Total Dials</th>
						<th>Duration</th>
						<th>Rate (per hour)</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="3"><img
							src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
					</tr>
				</tbody>
			  </table>
		  </div>
          <!-- /.panel-body --> 
        </div>
      </div>


