      <div class="row">
        <div class="col-lg-12">
          
           <div class="panel panel-primary">
            <div class="panel-heading clearfix"> <i class="fa fa-bar-chart-o fa-fw"></i>         <span class="mobile-only"><?php echo $title ?></span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body answers_panel  table-responsive">
            <?php if(count($answers)>0){ ?>
             <table class="table table-striped"><thead><th>Survey</th><th>Completed</th><th>Average NPS</th><th>Perfect 10</th><th>Below 7</th></thead><tbody>
             <?php 
			 foreach($answers as $row){
				//create the url for the click throughs
				$perfects = base_url()."search/custom/records/question/".$row['question_id']."/survey/".$row['survey_info_id']."/score/10";
				$lows = base_url()."search/custom/records/question/".$row['question_id']."/survey/".$row['survey_info_id']."/score/7:less";
				?>
                <tr><td><?php echo $row['survey_name'] ?></td><td><?php echo $row['count'] ?></td><td><?php echo $row['average_nps'] ?></td><td><a href="<?php echo $perfects ?>"><?php echo ($row['tens']?$row['tens']:"0") ?></a></td><td><a href="<?php echo $lows ?>"><?php echo ($row['low_score']?$row['low_score']:"0") ?></a></td></tr>
                <?php 
			 }
			 ?>
             </tbody></table>
             <?php } else { ?>
             <p>No surveys have been completed</p>
             <?php } ?>
            </div>
            <!-- /.panel-body --> 
          </div>
          
           <div class="panel panel-primary">
            <div class="panel-heading clearfix"> <i class="fa fa-bar-chart-o fa-fw"></i> <span class="panel-title">Answers Chart</span> 
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
            <div id="answers-chart">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
              </div>
            </div>
            <!-- /.panel-body --> 
          </div>
          
          
        </div>

      <!-- /.row --> 

<!-- Page-Level Plugin Scripts - Dashboard --> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script> 
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script> 
