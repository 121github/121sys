<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-map-marker fa-fw"></i> Journey planner
        <div class="pull-right">
            <form class="filter-form">
                <div class="btn-group">
                    <input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>">
                    <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span></button>
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
                                <div class="col-lg-6 col-sm-12" style="margin-bottom: 10px;">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default get-origin-location-btn" type="button">
                                                <span style="font-size: 15px" class="glyphicon glyphicon-map-marker"></span>
                                            </button>
                                        </span>
                                            <input type="text" class="form-control" name="origin"
                                                   placeholder="Origin..."
                                                   title="Enter the origin"/>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12" style="margin-bottom: 10px;">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default get-destination-location-btn" type="button">
                                                <span style="font-size: 15px" class="glyphicon glyphicon-map-marker"></span>
                                            </button>
                                        </span>
                                            <input type="text" class="form-control" name="destination"
                                                   placeholder="Destination..."
                                                   title="Enter the destination"/>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <input type="checkbox" id="optimized" name="optimized"
                                               data-on="<i class='glyphicon glyphicon-ok'></i> Optimized route"
                                               data-off="<i class='glyphicon glyphicon-remove'></i> Optimize route?" data-toggle="toggle">
                                    </div>
                                </div>
                                <div class="col-lg-6" style="text-align: right">
                                    <button class="btn btn-success btn-sm calc-route-btn">Get Route!</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="panel panel-default planner-data">
                    <div class="panel-body blocktext" style="max-height: 650px;">
                        <!-- Bootstrap 3 panel list. -->
                        <ul id="draggablePanelList" class="list-unstyled" style="height: 650px; overflow: auto;">
                                <img src=' <?php echo base_url(); ?>assets/img/ajax-loader-bar.gif'>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 map-view" style="display: none">
                <h1 class="planner-map">
                    <div class="map-wrapper">
                        <div id="map-canvas" style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
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
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <div class="input-group">
                                        <input hidden name="travel-mode">
                                        <input hidden name="destination">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default get-current-location-btn" type="button">
                                                <span style="font-size: 15px" class="glyphicon glyphicon-map-marker"></span>
                                            </button>
                                        </span>
                                        <input type="text" class="form-control" name="postcode"
                                               placeholder="Postcode..."
                                               title="Enter the location"/>
                                        <span class="input-group-btn">
                                            <button class="btn btn-default get-location-btn" type="button">Go!</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group input-group-sm directions-menu" style="display: none">
                                <div class="row" style="margin-top: 5px; text-align: right">
                                    <div class="col-lg-12 col-md-12" style="font-size: 11px;">
                                        <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                              class="change-directions-btn DRIVING pointer" item-mode="DRIVING"><img
                                                width="25px;" src="assets/img/icons/car.png"/></span>
                                        <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                              class="change-directions-btn BICYCLING pointer" item-mode="BICYCLING"><img
                                                width="25px;" src="assets/img/icons/cycle.png"/></span>
                                        <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                              class="change-directions-btn WALKING pointer" item-mode="WALKING"><img
                                                width="25px;" src="assets/img/icons/walking.png"/></span>
                                        <span class="close-directions-btn btn-lg glyphicon glyphicon-remove pointer"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="route-info"></div>
                                    </div>
                                </div>
                            </div>
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
        top: 28px;
        right: 55px;
        width: 50%;
    }

    .route-info {
        font-weight: bold;
        font-size: 14px;
        text-align: right;
        color: green;
    }
</style>