      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">My Callbacks</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-lg-12">
          <div class="panel panel-primary" id="a_favorites">
            <div class="panel-heading"> <i class="fa fa-star fa-fw"></i> Favorites</div>
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
          <div class="panel-heading"> <i class="fa fa-phone fa-fw"></i> Callbacks 
            <div class="pull-right">
               <form class="callbacks-filter" data-func="callbacks_panel">
            	<div class="btn-group">
            	  <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
                  <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
			      <input type="hidden" name="colname">
			      
			      <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span></button></div>
			      
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="campaign"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="campaign">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="agent"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="agent">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if(in_array("by team",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($team_managers as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="source"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="team">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Source</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($sources as $row): ?>
	                    <li><a href="#" class="filter" id="<?php echo $row['id'] ?>" data-ref="source"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" ref="#" style="color: green;" data-ref="source">All Sources</a> </li>
	                  </ul>
                  </div>
                </form>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body timely-callbacks"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
          <!-- /.panel-body --> 
        </div>

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- Page-Level Plugin Scripts - Dashboard --> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
	$(document).ready(function(){
		dashboard.init();
		dashboard.favorites_panel();
		dashboard.callbacks_panel();

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Todays': [moment(), moment()],
                    'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
                    'Missed': [moment('2014-01-01'), moment()],
                    'Upcoming': [moment().subtract('hours', 1),moment('2020-01-01')]
                },
                format: 'DD/MM/YYYY HH:mm',
                minDate: "02/07/2014",
                startDate: moment(),
				timePicker:true,
				timePickerSeconds:false
            },
            function(start, end, element) {
                var $btn = this.element;
			 var btntext = start.format('MMMM D') + ' - ' + end.format('MMMM D');
console.log(start.format('YYYY-MM-DD'));
			 if(start.format('YYYY-MM-DD')=='2014-07-02'){
			var btntext = "Missed";
			 }
			  if(end.format('YYYY-MM-DD')=='2020-01-01'){
			var btntext = "Upcoming";
			 }
                $btn.find('.date-text').html(btntext);
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD HH:mm'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD HH:mm'));
                dashboard.callbacks_panel();
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

	$("#my_favorites").on("click", function(){
		$("html,body").animate(
				{ scrollTop : $("#a_favorites").offset().top  },
				1500 
		);
	});
	});
	-->
	</script> 
