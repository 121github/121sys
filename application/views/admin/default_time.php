<div id="wrapper">
	<div id="sidebar-wrapper">
 <?php  $this->view('dashboard/navigation.php',$page) ?>
	</div>
	<div id="page-content-wrapper">
		<div id="page-wrapper">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Default Time</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">

					<div class="panel panel-primary groups-panel">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i>Default Time
							<div class="pull-right">
								<form class="filter-form" id="filter">
					                  <div class="btn-group">
						                <input type="hidden" name="agent">
                                         <input type="hidden" name="team">
					              	  </div>
					                  <div class="btn-group">
						                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
						                  <ul class="dropdown-menu pull-right" role="menu">
						                    <?php foreach($campaigns as $row): ?>
						                    <li><a href="#" class="campaign-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
						                    <?php endforeach ?>
						                    <li class="divider"></li>
						                    <li><a class="campaign-filter" ref="#" style="color: green;">Show All</a> </li>
						                  </ul>
					                  </div>
					                  <div class="btn-group">
						                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
						                  <ul class="dropdown-menu pull-right" role="menu">
						                    <?php foreach($team_managers as $row): ?>
						                    <li><a href="#" class="team-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
						                    <?php endforeach ?>
						                    <li class="divider"></li>
						                    <li><a class="team-filter" ref="#" style="color: green;">Show All</a> </li>
						                  </ul>
					                  </div>
					                  <div class="btn-group">
						                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Agent</button>
						                  <ul class="dropdown-menu pull-right" role="menu">
						                    <?php foreach($agents as $row): ?>
						                    <li><a href="#" class="agent-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
						                    <?php endforeach ?>
						                    <li class="divider"></li>
						                    <li><a class="agent-filter" ref="#" style="color: green;">Show All</a> </li>
						                  </ul>
					                  </div>
					            </form>
							</div>
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body time-panel">
							 <table class="table ajax-table">
								<thead>
									<tr>
										<th>Name</th>
                                        <th>Default Start Time</th>
                                        <th></th>
                                        <th>Default End Time</th>
                                        <th></th>
									</tr>
								</thead>
								<tbody class="default-time-body">
									<tr>
										<td colspan="3"><img
											src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
									</tr>
								</tbody>
							</table>
						</div>

					</div>

					<!-- /.row -->
				</div>
				<!-- /#page-wrapper -->
			</div>
		</div>
	</div>
<script>
	$(document).ready(function(){
        default_time.init()
	});
</script>