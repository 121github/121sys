<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Backup and Restore</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="data-form" class="backup-filter-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Backup Campaign
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="campaign">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach($campaigns as $row): ?>
                                                <li><a href="#" class="backup-campaign-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="backup-campaign-filter" ref="#" style="color: green;">All Campaigns</a> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body backup_data">
                                <table class="table ajax-table">
                                    <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Update Date From</th>
                                        <th>Update Date To</th>
                                        <th>Renewal Date From</th>
                                        <th>Renewal Date To</th>
                                        <th>Total Records</th>
                                        <th>Backup</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="3"><img
                                                src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.row -->

            <div class="panel panel-primary backup-container">
                <?php $this->view('forms/new_backup_form.php'); ?>
            </div>

            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="data-form" class="backup-history-filter-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Backup History (Last 12 backups)
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="campaign">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach($campaigns as $row): ?>
                                                <li><a href="#" class="backup-history-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="backup-history-filter" ref="#" style="color: green;">All Campaigns</a> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body backup_history_data">
                                <table class="table ajax-table">
                                    <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Update Date From</th>
                                        <th>Update Date To</th>
                                        <th>Renewal Date From</th>
                                        <th>Renewal Date To</th>
                                        <th>Total Records</th>
                                        <th>Restore</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="3"><img
                                                src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.row -->
        </div><!-- /#page-wrapper -->
    </div>
</div>
<script>
    $(document).ready(function () {
        backup_restore.init();
    });
</script>