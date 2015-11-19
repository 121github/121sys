<div class="row">
    <div class="col-lg-8">
        <h1 class="page-header">Productivity Report</h1>
    </div>
    <div class="col-lg-4 page-header" style="text-align: right;">
        <ul class="nav">
            <li>
                <div class="btn-group">
                    <span class="btn btn-default btn show-charts" data-item="0" charts="chart_div" data="filters">
                        <span class="fa fa-bar-chart-o fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn refresh-data">
                        <span class="glyphicon glyphicon-refresh" style="padding-left:3px; color:black;"></span>
                    </span>
                    <a href="#filter-right" class="btn btn-default btn">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a class='view-filters' href="#">View current filters</a></li>
                        <li><a class='clear-filters' href="#">Set default filters</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <!-- /.col-lg-12 -->
</div>

<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
    <div style="padding:30px 20px 3px">
        <form class="filter-form">
            <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
            <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">

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
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading clearfix">
                <i class="fa fa-bar-chart-o fa-fw"></i>
                Productivity Report
                <div class="pull-right" style="border:0px solid black;">
                    <a href="#filter-right" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body productivity-panel  table-responsive" style="padding: 0px;">
                <div class="row">
                    <div class="col-lg-8">
                        <table class="table productivity-table" style="padding: 10px;">
                            <thead></thead>
                            <tbody>
                            <tr>
                                <td>
                                    <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4">
                        <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                            <li class="plots-tab"><a href="#chart_div" class="tab" data-toggle="tab">Graphs</a></li>
                            <li class="filters-tab active"><a href="#filters" class="tab" data-toggle="tab">Filters</a></li>
                            <li class="searches-tab"><a href="#searches" class="tab" data-toggle="tab">Saved
                                    Searches</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="filters"></div>
                            <div class="tab-pane" id="chart_div">
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
</div>
      

