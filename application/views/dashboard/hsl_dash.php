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
                <div class="pull-right">
                    <form id="appointments-filter" data-func="appointments_panel">
                        <input type="hidden" name="date_from" value="">

                        <div class="btn-group">
                            <button type="button" class="daterange btn btn-default btn-xs" style="display: none"><span
                                    class="glyphicon glyphicon-calendar"></span> <span
                                    class="date-text"> Date from </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel-body" id="appointments-panel">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Completed Webform
            </div>
            <div class="panel-body" id="completed-panel">
                <table class='table table-striped table-condensed'>
                    <thead>
                    <tr>
                        <th>Completed</th>
                        <th>Uncompleted</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $webform_completed['completed'] . " (" . round(($webform_completed['completed'] * 100) / $webform_completed['total'], 2) . "%)"; ?></td>
                        <td><?php echo $webform_completed['uncompleted'] . " (" . round(($webform_completed['uncompleted'] * 100) / $webform_completed['total'], 2) . "%)"; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Where did you hear about us?
            </div>
            <div class="panel-body" id="hear-panel">
                <table class='table table-striped table-condensed'>
                    <?php foreach ($webform_hear as $key => $val) { ?>
                        <tr>
                            <th><?php echo $key; ?></th>
                            <td><?php echo $val . " (" . round(($val * 100) / $webform_completed['total'], 2) . "%)"; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Origin/source of the lead
            </div>
            <div class="panel-body" id="source-panel">
                <table class='table table-striped table-condensed'>
                    <?php foreach ($webform_source as $key => $val) { ?>
                        <tr>
                            <th><?php echo $key; ?></th>
                            <td><?php echo $val . " (" . round(($val * 100) / $webform_completed['total'], 2) . "%)"; ?></td>
                        </tr>
                    <?php } ?>
                </table>
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