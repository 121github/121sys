<div class="navbar navbar-inverse navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
        <p class="navbar-text" style="color:#fff; font-weight:700">
            <?php echo $title ?>
            <small><?php if (isset($_SESSION['current_campaign_name'])) { echo @$_SESSION['current_campaign_name']; } ?></small>
        </p>
    </ul>
    <?php if (!isset($hide_filter)) { ?>
        <ul class="nav navbar-nav pull-right" id="submenu-filters">
            <li>
                <div class="navbar-btn">
                    <div class="btn-group">
                        <?php if (isset($options['add_button'])) {?>
                            <span class="btn btn-default btn <?php echo $options['add_button'];?>" data-item="<?php echo $dashboard['dashboard_id'] ?>">
                                <span class="fa fa-plus fa-fw" style="color:black;"></span>
                            </span>
                        <?php }?>
                        <?php if (isset($options['chart_button'])) {?>
                            <span class="btn btn-default btn show-charts" data-item="0" charts="<?php echo $options['chart_button']['chart'];?>" data="<?php echo $options['chart_button']['data'];?>">
                                <span class="fa fa-bar-chart-o fa-fw" style="color:black;"></span>
                            </span>
                        <?php }?>
                        <span class="btn btn-default btn refresh-data <?php echo $options['refresh_button'];?>" dashboard-id="<?php echo (isset($dashboard['dashboard_id'])?$dashboard['dashboard_id']:"") ?>">
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
                </div>
            </li>
        </ul>
    <?php } ?>
</div>


<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
    <div style="padding:30px 20px 3px">
        <form class="filter-form" method="post">
            <input type="hidden" name="export_forms_id">
            <input type="hidden" name="comments">

            <?php if (isset($options['date']) && ($options['date'])) { ?>
                <input type="hidden" name="date_from" value="">
                <input type="hidden" name="date_to" value="">
                <div style="margin-bottom: 5%;">
                    <?php if (!isset($filters['date']) || (isset($filters['date']) && ($filters['date']['editable']))) { ?>
                        <button type="button" class="daterange btn btn-default" data-width="100%">
                            <span class="glyphicon glyphicon-calendar"></span>
                            <span class="date-text"> <?php echo "Any Time"; ?> </span>
                        </button>
                    <?php } else { ?>
                        <span class="date-filter"><span style="font-weight: bold">Date Filter: </span><?php echo ((isset($filters['date']))?$filters['date']['values'][0]:"Any Time"); ?></span>
                    <?php } ?>
                </div>
            <?php } ?>


            <?php if (isset($campaigns_by_group) && !empty($campaigns_by_group)) { ?>
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
            <?php } ?>

            <?php if (isset($campaign_outcomes) && !empty($campaign_outcomes)) { ?>
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
            <?php } ?>

            <?php if (isset($branches) && !empty($branches)) { ?>
                <div style="display: <?php echo (isset($filters['branches'])?($filters['branches']['editable'] == "1"?"":"none"):""); ?>">
                    <label style="margin-top: 5%;">Branches</label>
                    <select name="branches[]" class="selectpicker branch-filter" multiple data-width="100%"
                            data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                        <?php foreach ($branches as $row) { ?>
                            <option <?php if ((isset($filters['branches'])) && (in_array($row['id'],$filters['branches']['values'])))  {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>

            <?php if (isset($team_managers) && !empty($team_managers)) { ?>
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
            <?php } ?>

            <?php if (isset($sources) && !empty($sources)) { ?>
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
            <?php } ?>

            <?php if (isset($pots) && !empty($pots)) { ?>
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
            <?php } ?>

            <?php if (isset($users) && !empty($users)) { ?>
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
            <?php } ?>

            <button id="<?php echo (isset($options['submit_button'])?$options['submit_button']:''); ?>" class="btn btn-primary pull-right" item-id="<?php echo (isset($dashboard['dashboard_id'])?$dashboard['dashboard_id']:""); ?>" style="margin-top: 5%;">Submit</button>
        </form>
    </div>
</nav>

<nav id="filter-view" class="mm-menu mm--horizontal mm-offcanvas">
    <div id="filters"></div>
</nav>