 <div class="panel panel-primary" id="company-panel" data-toggle="collapse">
   <div class="panel-heading clearfix">
  <h4 class="panel-title"> Company Details<?php if(in_array("add companies",$_SESSION['permissions'])){ ?><!--<span class="glyphicon glyphicon-plus pointer pull-right" data-modal="add-company" data-urn="<?php echo $details['record']["urn"] ?>"></span>--><?php } ?></h4>
      </div>

      <ul class="list-group companies-list">
     <li class="list-group-item"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></li>
      </ul>

    </div>