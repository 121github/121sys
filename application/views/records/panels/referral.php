 <?php if(isset($collapsable)){ ?>      
    <div class="panel panel-primary" id="referral-panel">
    <div class="panel-heading clearfix" role="button" data-toggle="collapse"  data-target="#referral-panel-slide" aria-expanded="true" aria-controls="referral-panel-slide">Referral Details<?php if (in_array("add referral", $_SESSION['permissions'])) { ?><button
                class="btn btn-default btn-xs pointer pull-right" data-modal="add-referral"
                data-urn="<?php echo $details['record']["urn"] ?>"><span
                class="glyphicon glyphicon-plus"></span> New</button><?php } ?>
    </div>
<div id="referral-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
    <ul class="list-group referral-list">
        <li class="list-group-item"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></li>
    </ul>
</div>
</div>
    <?php } else { ?>
<div class="panel panel-primary" id="referral-panel">
    <div class="panel-heading clearfix">
        <h4 class="panel-title"> Referral Details<?php if (in_array("add referral", $_SESSION['permissions'])) { ?><button
                class="btn btn-default btn-xs pointer pull-right" data-modal="add-referral"
                data-urn="<?php echo $details['record']["urn"] ?>"><span
                class="glyphicon glyphicon-plus"></span> New</button><?php } ?></h4>
    </div>

    <ul class="list-group referral-list">
        <li class="list-group-item"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></li>
    </ul>

</div>
 <?php } ?>