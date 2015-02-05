<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Duplicates </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form class="filter-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <i class="fa fa-bar-chart-o fa-fw"></i>Search duplicate records
                                <div class="pull-right">
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
                                <div class="row">
                                    <div class="col-lg-4">
                                        <span class='marl btn btn-danger del-duplicates-btn btn-sm' style="display: none">Remove duplicates</span>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="pull-right" style="margin-bottom: 10px;">
                                            <select class="selectpicker duplicates-filter" name="field[]" multiple>
                                                <?php foreach($filter as $row): ?>
                                                    <option value="<?php echo $row['field'] ?>"><?php echo $row['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="text" name="filter_input" class="form-control" placeholder="Search..." disabled/>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-table">
                                    <table class="table ajax-table" >
                                        <thead>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td colspan="3">Please, select a filter in order to search the duplicates records</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    </form>
                </div>

                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->
        </div>
    </div>
    <script>
        $(document).ready(function () {
            duplicates.init();
        });
    </script>