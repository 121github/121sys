<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Dashboard |Error|
            <small><?php if (isset($_SESSION['current_campaign_name'])) { echo @$_SESSION['current_campaign_name']; } ?></small>
        </h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">

        <!-- /.panel -->
        <div class="panel panel-primary">
            <div class="panel-heading clearfix"><i class="fa fa-ban fa-fw"></i> Error</div>
            <!-- /.panel-heading -->
            <div class="panel-body" style="padding: 30px;">
                <?php echo $error; ?>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel .chat-panel -->
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->


<script>
    $(document).ready(function () {
        dashboard.init();
    });
</script>
