            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Suppresion Numbers </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form class="filter-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <i class="fa fa-bar-chart-o fa-fw"></i>Suppresion Numbers
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="date_from">
                                        <input type="hidden" name="date_to">

                                        <button type="button" class="daterange btn btn-default btn-xs">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                            <span class="date-text"> <?php echo "Date Range"; ?> </span>
                                        </button>
                                    </div>
                                    <div class="btn-group">
                                        <input type="hidden" name="campaign">
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
                            <div class="panel-body">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-xs dropdown-toggle new-suppression-btn"
                                            data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> New Suppression Number
                                    </button>
                                </div>
                                <div class="filter-table">
                                    <table class="table ajax-table" >
                                        <thead>
                                            <th>Telephone number</th>
                                            <th>Date Added</th>
                                            <th>Date Updated</th>
                                            <th>Campaigns</th>
                                            <th>Reason</th>
                                        </thead>
                                        <tbody>
                                            <td colspan="3"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    </form>
                </div>



    <div class="panel panel-primary suppression-container">
        <?php $this->view('forms/new_suppression_form.php'); ?>
    </div>

    <script>
        $(document).ready(function () {
            suppression.init();
        });
    </script>