<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Campaign Performance Report</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
    <div style="padding:30px 20px 3px">
        <form class="filter-form">
            <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
            <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
            <input type="hidden" name="group" value="<?php echo $group ?>">

            <div style="margin-bottom: 5%;">
                <button type="button" class="daterange btn btn-default" data-width="100%">
                    <span class="glyphicon glyphicon-calendar"></span>
                    <span class="date-text"> <?php echo "Today"; ?> </span>
                </button>
            </div>

            <label style="margin-top: 5%;">Campaign</label>
            <select name="campaigns[]" class="selectpicker campaign-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($campaigns_by_group as $type => $data) { ?>
                    <optgroup label="<?php echo $type ?>">
                        <?php foreach ($data as $row) { ?>
                            <option <?php if (isset($_SESSION['current_campaign']) && $row['id'] == $_SESSION['current_campaign']) {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>

            <?php if (count($campaign_outcomes) > 0) { ?>
                <label style="margin-top: 5%;">Outcome</label>
                <select name="outcomes[]" class="selectpicker outcome-filter" id="outcome-filter" multiple
                        data-width="100%" data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true">
                    <?php foreach ($campaign_outcomes as $type => $data) { ?>
                        <optgroup label="<?php echo $type ?>">
                            <?php foreach ($data as $row) { ?>
                                <option <?php if ($type == "positive") {
                                    echo "Selected";
                                } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Team</label>
                <select name="teams[]" class="selectpicker team-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($team_managers as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Agent</label>
                <select name="agents[]" class="selectpicker agent-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($agents as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <label style="margin-top: 5%;">Source</label>
            <select name="sources[]" class="selectpicker source-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($sources as $row) { ?>
                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>

            <button id="filter-submit" class="btn btn-primary pull-right" style="margin-top: 5%;">Submit</button>
        </form>
    </div>
</nav>

<!-- /.row -->
<div class="panel panel-primary">
    <div class="panel-heading clearfix">
        <i class="fa fa-bar-chart-o fa-fw"></i>
        Campaign Performance Report
        <div class="pull-right" style="border:0px solid black;">
            <span class="btn btn-success btn-xs refresh-data">
                <span class="glyphicon glyphicon-refresh" style="padding-left:3px; color:black;"></span>
            </span>
            <span class="btn btn-danger btn-xs clear-filters">
                <span class="glyphicon glyphicon-remove" style="padding-left:3px; color:black;"></span>
            </span>
            <a href="#filter-right" class="btn btn-default btn-xs">
                <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
            </a>
        </div>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body outcome-data table-responsive">
        <div class="row">
            <div class="col-lg-8">
                <table class="table ajax-table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th id="outcome-name"></th>
                        <th>Total Dials</th>
                        <th>%</th>
                        <th>Duration</th>
                        <th>Rate (per hour)</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3"><img
                                src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-4">
                <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                    <li class="active"><a href="#chart_div" class="tab" data-toggle="tab">Graphs</a></li>
                    <li class="filters-tab"><a href="#filters" class="tab" data-toggle="tab">Filters</a></li>
                    <li class="searches-tab"><a href="#searches" class="tab" data-toggle="tab">Saved Searches</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="filters"></div>
                    <div class="tab-pane active" id="chart_div">
                        <div id="chart_div_1"></div>
                        <div id="chart_div_2"></div>
                    </div>
                    <div class="tab-pane" id="searches"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.panel-body -->
</div>
</div>