<div class="panel panel-primary" id="branch-info">
    <div class="panel-heading clearfix">Branch Info
        <div class="pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" style="display: none" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">Hub <span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <?php foreach ($regions as $row) { ?>
                        <li><a class="region-select" data-branch-id="<?php echo $row['region_id'] ?>"
                               href="#"><?php echo $row['region_name'] ?></a></li>
                    <?php } ?>
                    <li role="separator" class="divider"></li>
                    <li><a class="filter" data-ref="distance" href="#" style="color: green;">Show all</a></li>
                </ul>
            </div>
            <div class="btn-group">
                <button class="pointer btn btn-xs btn-default" id="closest-branch">Find closest</button>
            </div>
        </div>
    </div>
    <div class="panel-body table-responsive" ><img
            src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></div>

</div>