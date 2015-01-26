
<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php  $this->view('dashboard/navigation.php',$page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Special Exports</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                   <form name="myform" method="post" class="filter-form" onsubmit="return export_data.onsubmitform();">
                        <div class="panel panel-primary">
                            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Available Exports
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="date_from">
                                        <input type="hidden" name="date_to">
                                        <input type="hidden" name="campaign">
                                        <input type="hidden" name="campaign_name">
                                        <input type="hidden" name="campaign_name">
                                        <input type="hidden" name="export_forms_id">

                                        <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "2nd Jul - Today"; ?> </span></button></div>
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
                            <div class="panel-body export-data">
                                <table class="table ajax-table">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="pull-right">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3"><img
                                                    src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <!-- /.panel-body -->
                        </div>
                    </form>
                </div>
            <!-- /.row -->
            </div>
            <div class="row custom-exports" style="display: none">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Custom Exports

                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>
            <!-- /.row -->
            </div>
        <!-- /#page-wrapper -->
        </div>
    </div>

    <script>
        $(document).ready(function () {
            export_data.init();
        });
    </script>

