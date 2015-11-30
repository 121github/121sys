
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Default Hours Time <a class="btn btn-default pull-right marl" href="<?php echo base_url() ?>hour/hours">Manage Hours</a> <a class="btn btn-default pull-right" href="<?php echo base_url() ?>time/agent_time">Manage Time</a></h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">

					<div class="panel panel-primary groups-panel">
						<div class="panel-heading">
							Default Hours
							<div class="pull-right">
								<form class="filter-form" id="filter">
					                  <div class="btn-group"> 
						                <input type="hidden" name="campaign">
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
						<div class="panel-body hours-panel">
							 <table class="table ajax-table">
								<thead>
									<tr>
										<th>Name</th>
										<th>Campaign</th>
                                        <th colspan="2">Default Duration (minutes)</th>
									</tr>
								</thead>
								<tbody class="default-hours-body">
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

<script>
	$(document).ready(function(){
		hours_settings.init()
	});
</script>