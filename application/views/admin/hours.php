
			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Hours</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">

					<div class="panel panel-primary groups-panel">
						<div class="panel-heading">
							<i class="fa fa-bar-chart-o fa-fw"></i>Hours list
							<div class="pull-right">
								<form class="filter-form" id="filter">
					                  <div class="btn-group"> 
						                <input type="hidden" name="date_from">
						                <input type="hidden" name="date_to">
						                <input type="hidden" name="campaign">
						                <input type="hidden" name="agent">
                                         <input type="hidden" name="team">
					              	  	<button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span></button>
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
							 <?php $this->view('forms/edit_hours_form.php'); ?>
							 <table class="table ajax-table">
								<thead>
									<tr>
										<th>Date</th>
										<th>Name</th>
										<th>Duration (minutes)</th>
										<th>Campaign</th>
										<th>Updated</th>
										<th>Updated Date</th>
										<th colspan="2">Options</th>
									</tr>
								</thead>
								<tbody class="hours-body">
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

<script>
	$(document).ready(function(){
		$('.selectpicker').selectpicker();
		hours.init()
	});
</script>