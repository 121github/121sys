<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Outcomes </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i>Outcomes
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body outcome-data">
                            <table class="table ajax-table table-hover" >
                                <thead>
                                    <th style="display: none"></th>
                                    <th></th>
                                    <th>Outcome</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Positive</th>
                                    <th>Dm contact</th>
                                    <th>Sort</th>
                                    <th>Enable select</th>
                                    <th>Force comment</th>
                                    <th>Force nextcall</th>
                                    <th>Delay hours</th>
                                    <th>No history</th>
                                    <th>Keep record</th>
                                    <th></th>
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

    <div class="panel panel-primary outcome-container">
        <?php $this->view('forms/edit_outcome_form.php'); ?>
    </div>

    <script>
        $(document).ready(function () {
            outcomes.init();
        });
    </script>