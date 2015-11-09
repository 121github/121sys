    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Productivity Report</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary">
          <div class="panel-heading clearfix"> <i class="fa fa-bar-chart-o fa-fw"></i> Productivity Report
            <div class="pull-right">
            <form class="filter-form">
            	<div class="btn-group">
            	  <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
                  <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                    <input type="hidden" name="outcome">
                    <input type="hidden" name="colname">

                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Transfer </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <?php foreach($outcomes as $row): ?>
                            <li><a href="#" class="outcome-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                        <?php endforeach ?>
                        <li class="divider"></li>
                    </ul>
                </div>
                <div class="btn-group">
			      <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span></button>
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
                </form>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body productivity-panel  table-responsive">
              <table class="table productivity-table">
                  <thead></thead>
                  <tbody>
                    <tr>
                        <td>
                            <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
                        </td>
                    </tr>
                  </tbody>
              </table>
          </div>
          <!-- /.panel-body --> 
        </div>
      </div>
      

