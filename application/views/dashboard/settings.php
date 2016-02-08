<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dashboard Settings
            <small><?php if (isset($_SESSION['current_campaign_name'])) {
                    echo @$_SESSION['current_campaign_name'];
                } ?></small>
        </h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary" id="settings">
            <div class="panel-heading clearfix">
                <i class="fa fa-dashboard fa-fw"></i> Custom Dashboards
                <div class="pull-right">
                    <button type="button" class="btn btn-default btn-xs new-dashboard-btn"><i class="fa fa-plus fa-fw"></i> New Dashboard</button>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="custom-dash"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></div>
            <!-- /.panel-body -->
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        dashboard.settings();
        dashboard.custom_dash_panel();
    });
</script>
