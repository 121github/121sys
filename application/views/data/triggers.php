<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Triggers </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <!-- /.panel -->
                    <form class="email-filter-form">
                        <div class="panel panel-primary email-triggers">
                            <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Email Triggers
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="campaign">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                                data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>
                                            Campaign
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach ($campaigns as $row): ?>
                                                <li><a href="#" class="email-campaign-filter"
                                                       id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="email-campaign-filter" ref="#">All Campaigns</a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group">
                                        <input type="hidden" name="outcome">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                                data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>
                                            Outcome
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach ($outcomes as $row): ?>
                                                <li><a href="#" class="email-outcome-filter"
                                                       id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="email-outcome-filter" ref="#">All Outcomes</a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group">
                                        <input type="hidden" name="template">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                                data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>
                                            Template
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach ($templates as $row): ?>
                                                <li><a href="#" class="email-template-filter"
                                                       id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="email-template-filter" ref="#">All Templates</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table ajax-table table-hover table-responsive">
                                            <thead>
                                            <tr>
                                                <th>Campaign name</th>
                                                <th>Outcome</th>
                                                <th>Template</th>
                                                <th style="text-align: right">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <!--The contents of this table is loaded via an ajax function in data.js -->
                                            </tbody>
                                        </table>
                                        <!-- /.table-responsive -->
                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.panel-body -->
                        </div>
                    </form>
                    <!-- /.panel -->
                    <form class="ownership-filter-form">
                        <div class="panel panel-primary ownership-triggers">
                            <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Ownership Triggers
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="campaign">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                                data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>
                                            Campaign
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach ($campaigns as $row): ?>
                                                <li><a href="#" class="ownership-campaign-filter"
                                                       id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="ownership-campaign-filter" ref="#">All Campaigns</a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group">
                                        <input type="hidden" name="outcome">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                                data-toggle="dropdown"><span class="glyphicon glyphicon-filter"></span>
                                            Outcome
                                        </button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach ($outcomes as $row): ?>
                                                <li><a href="#" class="ownership-outcome-filter"
                                                       id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="ownership-outcome-filter" ref="#">All Outcomes</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <table class="table ajax-table table-hover table-responsive">
                                            <thead>
                                            <tr>
                                                <th>Campaign name</th>
                                                <th>Outcome</th>
                                                <th style="text-align: right">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <!--The contents of this table is loaded via an ajax function in data.js -->
                                            </tbody>
                                        </table>
                                        <!-- /.table-responsive -->
                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.panel-body -->
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.col-lg-8 -->

            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
</div>

<script>
    $(document).ready(function () {
        triggers.init();
    });
</script>