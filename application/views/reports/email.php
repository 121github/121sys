
<div id="wrapper">
<div id="sidebar-wrapper">
 <?php  $this->view('dashboard/navigation.php',$page) ?>
</div>
<div id="page-content-wrapper">
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Emails Report</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Emails Report
            <div class="pull-right">
            <form class="filter-form">
              <div class="btn-group">
               <input type="hidden" name="group" value="<?php echo $group ?>">
                  <input type="hidden" name="date_from">
                  <input type="hidden" name="date_to">
                  <input type="hidden" name="template">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                   <input type="hidden" name="agent">
                  <input type="hidden" name="source">
                  <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "2nd Jul - Today"; ?> </span></button></div>
                  <div class="btn-group">
                      <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Template</button>
                      <ul class="dropdown-menu pull-right" role="menu">
                          <?php foreach($templates as $row): ?>
                              <li><a href="#" class="template-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                          <?php endforeach ?>
                          <li class="divider"></li>
                          <li><a class="template-filter" ref="#" style="color: green;">All Templates</a> </li>
                      </ul>
                  </div>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="campaign-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="campaign-filter" ref="#" style="color: green;">All Campaigns</a> </li>
	                  </ul>
                  </div>
                                    <?php if($all_emails){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="team-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="team-filter" ref="#" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                                   <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="agent-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="agent-filter" ref="#" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Source</button>
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
          <div class="panel-body email-data">
	          <table class="table ajax-table">
				<thead>
					<tr>
						<th class="success">#</th>
						<th class="success">Name</th>
                        <th class="success">Emails Sent</th>
						<th class="success">Emails Read</th>
                        <th class="success">Percent Read</th>
                        <th class="danger">Emails Unsent</th>
                        <th class="danger">Percent Unsent</th>

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
      <div class="panel panel-primary email-all-container">
          <?php $this->view('email/show_all_email.php'); ?>
      </div>
      <div class="panel panel-primary email-view-container">
          <?php $this->view('email/view_email.php'); ?>
      </div>
      
      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --></div>
</div>


