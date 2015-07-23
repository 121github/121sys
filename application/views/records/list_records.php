<div class="panel panel-primary">
    <div class="panel-heading">
        <i class="fa fa-list fa-fw"></i>
        Record List
        <div class="pull-right">
            <form class="filter-form">
                <div class="btn-group">
                    <input type="hidden" name="group">
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span
                            class="glyphicon glyphicon-filter"></span> Colour By
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li><a href="#" class="group-filter" id="campaign_name">Campaign</a></li>
                        <li><a href="#" class="group-filter" id="name">Company</a></li>
                        <li><a href="#" class="group-filter" id="fullname">Contact</a></li>
                        <li><a href="#" class="group-filter" id="outcome">Outcome</a></li>
                        <li><a href="#" class="group-filter" id="ownership">Ownership</a></li>
                        <li class="divider"></li>
                        <li><a class="group-filter" ref="#" style="color: green;">Colour by</a></li>
                    </ul>
                </div>
                <div class="btn-group">
                    <input style="display:none" type="checkbox" id="map-view-toggle"
                           data-on="<i class='glyphicon glyphicon-map-marker'></i> Map View"
                           data-off="<i class='glyphicon glyphicon-map-marker'></i> Map View" data-toggle="toggle">
                </div>
            </form>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div id="table-wrapper" class="col-lg-12 record-view" style="overflow:visible">
                <img class="table-loading"
                     src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
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
                            <div class="row">
                                <div class="col-lg-12 col-sm-12">
                                    <span style="position:absolute;top:0px;right:-20px; padding:5px; font-size:14px"
                                          class='glyphicon glyphicon-exclamation-sign red pointer' data-toggle='tooltip'
                                          data-placement='left'
                                          title='Records without a postcode are not shown on the map'></span>
<div class="input-group">
             <input hidden name="travel-mode">
                                        <input hidden name="destination">       <span class="pointer btn-default input-group-addon"><span id="show-uk" class="fa fa-globe"></span></span>
  <span class="pointer btn-default input-group-addon get-current-location-btn"><span class="glyphicon glyphicon-map-marker"></span></span>
  <input type="text" class="form-control input-sm" name="postcode"
                                               placeholder="Postcode..."
                                               title="Enter the location" />
  <span class="pointer btn-default input-group-addon get-location-btn">Go</span>
</div>
                                </div>
                            </div>
                            <div class="form-group input-group-sm directions-menu" style="display: none">
                                <div class="row" style="margin-top: 5px; text-align: right">
                                    <div class="col-lg-12 col-md-12" style="font-size: 11px;">
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn DRIVING pointer" item-mode="DRIVING"><img
                                            width="25px;" src="../assets/img/icons/car.png"/></span>
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn TRANSIT pointer" item-mode="TRANSIT"><img
                                            width="25px;" src="../assets/img/icons/train.png"/></span>
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn BICYCLING pointer" item-mode="BICYCLING"><img
                                            width="25px;" src="../assets/img/icons/cycle.png"/></span>
                                    <span style="opacity: 0.4; filter: alpha(opacity=40);"
                                          class="change-directions-btn WALKING pointer" item-mode="WALKING"><img
                                            width="25px;" src="../assets/img/icons/walking.png"/></span>
                                        <span
                                            class="close-directions-btn btn-lg glyphicon glyphicon-remove pointer"></span>
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
                        <div class="panel-heading">
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


<script type="text/javascript">
 table_columns = <?php echo json_encode($columns) ?>;

	$(document).ready(function() {
    maps.initialize("records");
    view_records.init();

        view_records.has_filter = "<?php echo ((isset($_SESSION['filter']['values']) && !empty($_SESSION['filter']['values']))?true:false) ?>";
    });

</script>