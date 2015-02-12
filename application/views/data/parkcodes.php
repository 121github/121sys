<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Park Codes </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i>Outcomes
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle new-parkcode-btn"
                                            data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> New Park Code
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body parkcode-data">
                            <table class="table ajax-table table-hover" >
                                <thead>
                                    <th>Park Code</th>
                                    <th>Park Reason</th>
                                    <th style="text-align: right">Actions</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->
        </div>
    </div>

    <div class="panel panel-primary parkcode-container">
        <?php $this->view('forms/edit_parkcode_form.php'); ?>
    </div>

    <script>
        $(document).ready(function () {
            parkcode.init();
        });
    </script>