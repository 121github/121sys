<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Daily Ration</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="data-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Daily Ration
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach($campaigns as $row): ?>
                                                <li><a href="#" class="campaign-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="campaign-filter" ref="#" style="color: green;">All Campaigns</a> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body daily_ration_data">
                                <table class="table ajax-table">
                                    <thead>
                                    <tr>
                                        <th>Campaign</th>
                                        <th>Total Records</th>
                                        <th>Packed Records</th>
                                        <th>Daily Data</th>
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
                    </form>
                    <!-- /.row -->
                </div>
                <!-- /#page-wrapper --></div>
        </div>
        <script>
            $(document).ready(function () {
                daily_ration.init();
            });
        </script>