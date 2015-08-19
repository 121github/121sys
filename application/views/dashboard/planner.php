<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-map-marker fa-fw"></i> Journey planner
        <div class="pull-right">
            <form class="filter-form">
                <div class="btn-group">
                    <!--                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>-->
                    <!--                    <ul class="dropdown-menu pull-right" role="menu">-->
                    <!--                        --><?php //foreach($campaign_branch_users as $campaign => $branches): ?>
                    <!--                            --><?php //if (!empty($branches)) { ?>
                    <!--                                <li class="dropdown-submenu">-->
                    <!--                                    <a class="right-caret" href="#">-->
                    <?php //echo $campaign ?><!--</a>-->
                    <!--                                    <ul class="dropdown-menu">-->
                    <!--                                        --><?php //foreach($branches as $branch => $user): ?>
                    <!--                                        <li class="dropdown-submenu">-->
                    <!--                                            <a class="right-caret" href="#">-->
                    <?php //echo $branch ?><!--</a>-->
                    <!--                                            <ul class="dropdown-menu">-->
                    <!--                                                <li><a href="#">-->
                    <?php //echo $user ?><!--</a> </li>-->
                    <!--                                            </ul>-->
                    <!--                                        </li>-->
                    <!--                                        --><?php //endforeach ?>
                    <!--                                    </ul>-->
                    <!--                                </li>-->
                    <!--                            --><?php //} else { ?>
                    <!--                                <li><a href="#">--><?php //echo $campaign ?><!--</a> </li>-->
                    <!--                            --><?php //} ?>
                    <!---->
                    <!--                        --><?php //endforeach ?>
                    <!--                        <li class="divider"></li>-->
                    <!--                        <li><a class="campaign-filter" ref="#" style="color: green;">All Campaigns</a> </li>-->
                    <!--                    </ul>-->

                    <input type="hidden" name="user" value="<?php echo $user_id ?>">
                    <?php if(in_array("admin planner", $_SESSION['permissions'])) { ?>
                        <!--<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-filter"></span>
                            <span class="user-filter-name">User</span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php if (count($campaign_branch_users['Campaigns']) == 1 && isset($_SESSION['current_campaign'])) { ?>
                                <?php foreach ($campaign_branch_users['Campaigns'] as $campaign => $branches): ?>
                                    <?php if (!empty($branches)) { ?>
                                        <?php foreach ($branches as $branch => $user): ?>
                                            <li>
                                                <a class="trigger right-caret"><?php echo $branch ?></a>
                                                <ul class="dropdown-menu sub-menu-left">
                                                    <?php foreach ($user as $id => $user_name): ?>
                                                        <li><a href="#" class="user-filter"
                                                               id="<?php echo $id ?>"><?php echo $user_name ?></a></li>
                                                    <?php endforeach ?>
                                                </ul>
                                            </li>
                                        <?php endforeach ?>
                                    <?php } else { ?>
                                        <li><a disabeld href="#">No Branches for this campaign</a></li>
                                    <?php } ?>
                                <?php endforeach ?>
                            <?php } else { ?>
                                <?php foreach ($campaign_branch_users['Campaigns'] as $campaign => $branches): ?>
                                    <?php if (!empty($branches)) { ?>
                                        <li>
                                            <a class="trigger right-caret"><?php echo $campaign ?></a>
                                            <ul class="dropdown-menu sub-menu-left">
                                                <?php foreach ($branches as $branch => $user): ?>
                                                    <li>
                                                        <a class="trigger right-caret"><?php echo $branch ?></a>
                                                        <ul class="dropdown-menu sub-menu-left">
                                                            <?php foreach ($user as $id => $user_name): ?>
                                                                <li><a href="#" class="user-filter"
                                                                       id="<?php echo $id ?>"><?php echo $user_name ?></a>
                                                                </li>
                                                            <?php endforeach ?>
                                                        </ul>
                                                    </li>
                                                <?php endforeach ?>
                                            </ul>
                                        </li>
                                    <?php } else { ?>
                                        <li><a disabled href="#"><?php echo $campaign ?></a></li>
                                    <?php } ?>
                                <?php endforeach ?>
                            <?php } ?>
                            <?php if (count($campaign_branch_users['Campaigns']) > 0) { ?>
                                <li>
                                    <a class="trigger right-caret">Others</a>
                                    <ul class="dropdown-menu sub-menu-left">
                                        <?php foreach ($campaign_branch_users['Others'] as $id => $user_name): ?>
                                            <li><a href="#" class="user-filter"
                                                   id="<?php echo $id ?>"><?php echo $user_name ?></a></li>
                                        <?php endforeach ?>
                                    </ul>
                                </li>
                            <?php } else { ?>
                                <?php foreach ($campaign_branch_users['Others'] as $id => $user_name): ?>
                                    <li><a href="#" class="user-filter" id="<?php echo $id ?>"><?php echo $user_name ?></a>
                                    </li>
                                <?php endforeach ?>
                            <?php } ?>
                        </ul>-->
                        
                        
                        
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span id="user-filter-name">Driver</span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                        <?php foreach($planner_users as $row){ ?>
                         <li><a href="#" class="user-filter" data-id="<?php echo $row['user_id'] ?>"><?php echo $row['region_name'] ?>: <?php echo $row['name'] ?> </a>
                         <?php } ?>
                        </ul>
                        
                        
                        
                    <?php } ?>

                </div>
                <div class="btn-group">
                    <input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>">
                    <button type="button" class="daterange btn btn-default btn-xs"><span
                            class="glyphicon glyphicon-calendar"></span> <span
                            class="date-text"> <?php echo "Today"; ?> </span></button>
                </div>
                <div class="btn-group">
                    <input type="checkbox" id="map-view-toggle"
                           data-on="<i class='glyphicon glyphicon-map-marker'></i> Map View"
                           data-off="<i class='glyphicon glyphicon-map-marker'></i> Map View" data-toggle="toggle">
                </div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12 planner-view" id="table-wrapper">
                <div class="panel panel-default planner-options">
                    <div class="panel-body">
                        <form class="directions-form">
                            <div class="row">
                                <div class="col-lg-4 col-sm-12" style="margin-bottom: 10px;">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default get-origin-location-btn" type="button">
                                                <span style="font-size: 15px"
                                                      class="glyphicon glyphicon-map-marker"></span>
                                            </button>
                                        </span>
                                        <input type="text" class="form-control" name="origin"
                                               placeholder="Origin..."
                                               title="Enter the origin"/>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-12" style="margin-bottom: 10px;">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default get-destination-location-btn" type="button">
                                                <span style="font-size: 15px"
                                                      class="glyphicon glyphicon-map-marker"></span>
                                            </button>
                                        </span>
                                        <input type="text" class="form-control" name="destination"
                                               placeholder="Destination..."
                                               title="Enter the destination"/>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <input type="checkbox" id="optimized" name="optimized"
                                           data-on="<i class='glyphicon glyphicon-ok'></i> Fastest Route"
                                           data-off="<i class='glyphicon glyphicon-remove'></i> Fastest Route"
                                           data-toggle="toggle">
                                    <button style="margin:3px 0px 3px" class="btn btn-success btn calc-route-btn">Get
                                        Route!
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="panel panel-default planner-data">
                    <div class="panel-body">
                        <!-- Bootstrap 3 panel list. -->
                        <div id="draggablePanelList">
                            <img src=' <?php echo base_url(); ?>assets/img/ajax-loader-bar.gif'>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 map-view" style="display: none">
                <h1 class="planner-map">
                    <div class="map-wrapper">
                        <div id="map-canvas"
                             style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
                        <i class="top"></i>
                        <i class="right"></i>
                        <i class="bottom"></i>
                        <i class="left"></i>
                        <i class="top left"></i>
                        <i class="top right"></i>
                        <i class="bottom left"></i>
                        <i class="bottom right"></i>
                    </div>
                    <div class="directions-btn">
                        <form class="form-horizontal map-form">
                            <div class="input-group" style="float:left;">
                                <input hidden name="travel-mode">
                                <input hidden name="destination"><span class="pointer btn-default input-group-addon"
                                                                       id="show-uk"><span
                                        class="fa fa-globe"></span></span>
                                <span class="pointer btn-default input-group-addon get-current-location-btn"><span
                                        class="glyphicon glyphicon-map-marker"></span></span>
                                <input type="text" class="form-control input-sm" name="postcode"
                                       placeholder="Postcode..."
                                       title="Enter the location"/>
                                <span class="pointer btn-default input-group-addon get-location-btn">Go</span>
                            </div>
                            <div style="float:right;margin-right:50px;">
                                <span style="opacity: 0.4; filter: alpha(opacity=40);" class="planner-travel-mode DRIVING pointer" item-mode="DRIVING"><img
                                         width="25px;" src="<?php echo base_url() ?>assets/img/icons/car.png"/></span>
                                <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                      class="planner-travel-mode BICYCLING pointer" item-mode="BICYCLING"><img
                                        width="25px;" src="<?php echo base_url() ?>assets/img/icons/cycle.png"/></span>
                                <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                      class="planner-travel-mode WALKING pointer" item-mode="WALKING"><img
                                        width="25px;"
                                        src="<?php echo base_url() ?>assets/img/icons/walking.png"/></span>
                            </div>
                            <div style="clear:both"></div>
                            <div class="route-info"></div>
                            <div style="clear:both"></div>
                            <div id="show-directions"></div>
                        </form>
                    </div>
                    <div class="panel panel-primary directionsPanel-container">
                        <div class="panel-heading" style="font-size: 20px">
                            Route
                        </div>
                        <div class="panel-body" style="font-size: 12px; overflow: auto; height: 500px;">
                            <div class="directionsPanel-panel">
                                <div class="directionsPanel-content">
                                    <div id="directionsPanel"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </h1>
            </div>
        </div>
    </div>
</div>

<style>
    #map-canvas {
        min-height: 750px;
        margin: 0px;
        padding: 0px
    }

    .map-form {
        position: absolute;
        top: 8px;
        width: 100%;
    }

    .map-form .input-group {
        width: 250px;
        left: 50px;
    }

    #show-directions {
        margin-right: 50px;
    }

    .route-info {
        background-color: rgba(000, 000, 000, 0.4);
        float: right;
        margin-right: 50px;
    }
</style>
