<div class="panel panel-primary" id="referral-panel">
    <div class="panel-heading clearfix">
        <h4 class="panel-title"> Referral Details<?php if (in_array("add referral", $_SESSION['permissions'])) { ?><span
                class="glyphicon glyphicon-plus pointer pull-right" data-modal="add-referral"
                data-urn="<?php echo $details['record']["urn"] ?>"></span><?php } ?></h4>
    </div>

    <ul class="list-group referral-list">
        <li class="list-group-item"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></li>
    </ul>

</div>