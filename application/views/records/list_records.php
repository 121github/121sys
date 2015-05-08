<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-bar-chart-o fa-fw"></i>
        Record List
        <div class="pull-right">
            <div class="btn-group">
                <input type="checkbox" id="map-view-toggle"
                       data-on="<i class='glyphicon glyphicon-map-marker'></i> Map View"
                       data-off="<i class='glyphicon glyphicon-map-marker'></i> Map View" data-toggle="toggle">
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-12 record-view">
                <table class="table table-striped table-bordered data-table">
                    <thead>
                    <tr>
                        <?php foreach ($columns as $col) { ?>
                            <th><?php echo $col['header'] ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <!--ajax processing puts data in -->
                    <tfoot>
                    <tr>
                        <?php foreach ($columns as $col) { ?>
                            <th><?php echo $col['header'] ?></th>
                        <?php } ?>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-lg-6 map-view" style="display: none">
                <h1 class="planner-map">
                    <div id="map-canvas"
                         style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
                    <div class="directions-btn">
                        <form class="form-horizontal map-form">
                            <div class="row">
                                <div class="col-lg-10 col-sm-10">
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
                                <div class="col-lg-1 col-sm-1">
                                    <span class='glyphicon glyphicon-exclamation-sign red pointer btn-sm'
                                          data-toggle='tooltip' data-placement='left'
                                          title='The records without postcode are no shown in the map!!'></span>
                                </div>
                            </div>
                            <div class="form-group input-group-sm directions-menu" style="display: none">
                                <div class="row" style="margin-top: 5px; text-align: right">
                                    <div class="col-lg-12 col-md-12" style="font-size: 11px;">
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn DRIVING pointer" item-mode="DRIVING"><img
                                            width="20px;" src="../assets/img/icons/car.png"/></span>
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn TRANSIT pointer" item-mode="TRANSIT"><img
                                            width="20px;" src="../assets/img/icons/train.png"/></span>
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn BICYCLING pointer" item-mode="BICYCLING"><img
                                            width="20px;" src="../assets/img/icons/cycle.png"/></span>
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn WALKING pointer" item-mode="WALKING"><img
                                            width="20px;" src="../assets/img/icons/walking.png"/></span>
                                        <span class="close-directions-btn glyphicon glyphicon-remove"></span>
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
                        <div class="panel-heading">Route<span
                                class="glyphicon glyphicon-remove pull-right close-directionsPanel"></span></div>
                        <div class="panel-body" style="font-size: 12px; overflow: scroll; height: 500px;">
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
        font-size: 10px;
        text-align: right;
        color: green;
    }
</style>
