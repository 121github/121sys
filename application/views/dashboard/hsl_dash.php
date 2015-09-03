<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Hsl Dashboard</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-clock-o fa-fw"></i>
                Next 6 weeks appointments
            </div>
            <div class="panel-body" id="appointments-panel">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/filter.js"></script>


<script>
    $(document).ready(function () {
        hsl.init();
    });
</script>