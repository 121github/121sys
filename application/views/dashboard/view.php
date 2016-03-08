<div class="row">
    <div class="col-lg-8 col-sm-6">
        <h1 class="page-header">
            <?php echo $dashboard['name']; ?>
            <small><?php if (isset($_SESSION['current_campaign_name'])) { echo @$_SESSION['current_campaign_name']; } ?></small>
        </h1>
    </div>
    <div class="col-lg-4 col-sm-6 page-header" style="text-align: right;">
        <ul class="nav">
            <li>
                <div class="btn-group">
                    <span class="btn btn-default btn new-report" data-item="<?php echo $dashboard['dashboard_id'] ?>">
                        <span class="fa fa-plus fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn show-charts" data-item="0" charts="" data="">
                        <span class="fa fa-bar-chart-o fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn refresh-dashboard-data" dashboard-id="<?php echo $dashboard['dashboard_id'] ?>">
                        <span class="glyphicon glyphicon-refresh" style="padding-left:3px; color:black;"></span>
                    </span>
                    <a href="#filter-right" class="btn btn-default btn">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#filter-view">View current filters</a></li>
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
        <form class="filter-form" method="post">
            <input type="hidden" name="export_forms_id">
            <input type="hidden" name="date_from" value="<?php echo ((isset($filters['date_from']))?$filters['date_from']['values'][0]:"2014-02-07"); ?>">
            <input type="hidden" name="date_to" value="<?php echo ((isset($filters['date_to']))?$filters['date_to']['values'][0]:date('Y-m-d')); ?>">

            <div style="margin-bottom: 5%;">
                <button type="button" class="daterange btn btn-default" data-width="100%">
                    <span class="glyphicon glyphicon-calendar"></span>
                    <span class="date-text"> <?php echo "Any Time"; ?> </span>
                </button>
            </div>

            <div style="display: <?php echo (isset($filters['campaigns'])?($filters['campaigns']['editable'] == "1"?"":"none"):""); ?>">
                <label style="margin-top: 5%;">Campaign</label>
                <select name="campaigns[]" class="selectpicker campaign-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($campaigns_by_group as $type => $data) { ?>
                        <optgroup label="<?php echo $type ?>">
                            <?php foreach ($data as $row) { ?>
                                <option <?php if ((isset($_SESSION['current_campaign']) && $row['id'] == $_SESSION['current_campaign']) || (isset($filters['campaigns']) && (in_array($row['id'],$filters['campaigns']['values'])))) {
                                    echo "Selected";
                                } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            </div>

            <div style="display: <?php echo (isset($filters['outcomes'])?($filters['outcomes']['editable'] == "1"?"":"none"):""); ?>">
                <?php if (count($campaign_outcomes) > 0) { ?>
                    <label style="margin-top: 5%;">Outcome</label>
                    <select name="outcomes[]" class="selectpicker outcome-filter" id="outcome-filter" multiple
                            data-width="100%" data-live-search="true" data-live-search-placeholder="Search"
                            data-actions-box="true">
                        <?php foreach ($campaign_outcomes as $type => $data) { ?>
                            <optgroup label="<?php echo $type ?>">
                                <?php foreach ($data as $row) { ?>
                                    <option <?php if ((isset($filters['outcomes'])) && (in_array($row['id'],$filters['outcomes']['values'])))  {
                                        echo "Selected";
                                    } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>

            <div style="display: <?php echo (isset($filters['teams'])?($filters['teams']['editable'] == "1"?"":"none"):""); ?>">
                <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                    <label style="margin-top: 5%;">Team</label>
                    <select name="teams[]" class="selectpicker team-filter" multiple data-width="100%"
                            data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                        <?php foreach ($team_managers as $row) { ?>
                            <option <?php if ((isset($filters['teams'])) && (in_array($row['id'],$filters['teams']['values'])))  {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>

            <div style="display: <?php echo (isset($filters['agents'])?($filters['agents']['editable'] == "1"?"":"none"):""); ?>">
                <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                    <label style="margin-top: 5%;">Agent</label>
                    <select name="agents[]" class="selectpicker agent-filter" multiple data-width="100%"
                            data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                        <?php foreach ($agents as $row) { ?>
                            <option <?php if ((isset($filters['agents'])) && (in_array($row['id'],$filters['agents']['values'])))  {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </select>
                <?php } ?>
            </div>

            <div style="display: <?php echo (isset($filters['sources'])?($filters['sources']['editable'] == "1"?"":"none"):""); ?>">
                <label style="margin-top: 5%;">Source</label>
                <select name="sources[]" class="selectpicker source-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($sources as $row) { ?>
                        <option <?php if ((isset($filters['sources'])) && (in_array($row['id'],$filters['sources']['values'])))  {
                            echo "Selected";
                        } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div style="display: <?php echo (isset($filters['pot'])?($filters['pot']['editable'] == "1"?"":"none"):""); ?>">
                <label style="margin-top: 5%;">Pot</label>
                <select name="pot[]" class="selectpicker pot-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($pots as $row) { ?>
                        <option <?php if ((isset($filters['pot'])) && (in_array($row['id'],$filters['pot']['values'])))  {
                            echo "Selected";
                        } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div style="display: <?php echo (isset($filters['user'])?($filters['user']['editable'] == "1"?"":"none"):""); ?>">
                <label style="margin-top: 5%;">User</label>
                <select name="user[]" class="selectpicker user-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($users as $type => $data) { ?>
                        <optgroup label="<?php echo $type ?>">
                            <?php foreach ($data as $row) { ?>
    <!--                            <option value="--><?php //echo $row['id'] ?><!--">--><?php //echo $row['name'] ?><!--</option>-->
                                <option <?php if ((isset($filters['user'])) && (in_array($row['id'],$filters['user']['values'])))  {
                                    echo "Selected";
                                } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            </div>

            <button id="filter-submit" class="btn btn-primary pull-right" item-id="<?php echo $dashboard['dashboard_id']; ?>" style="margin-top: 5%;">Submit</button>
        </form>
    </div>
</nav>

<nav id="filter-view" class="mm-menu mm--horizontal mm-offcanvas">
    <div id="filters"></div>
</nav>

<!--<div class="row">-->

<div class="row dashboard-area">

</div>
<!-- /.row -->

<script>
    $(document).ready(function () {
        dashboard.init();
        dashboard.load_dash(<?php echo $dashboard['dashboard_id']; ?>);

        var start = moment($('form').find('input[name="date_from"]').val(),"YYYY-MM-DD");
        var end = moment($('form').find('input[name="date_to"]').val(),"YYYY-MM-DD");

        $('.daterange').find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
    });
</script>