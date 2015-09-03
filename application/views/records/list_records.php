<div class="panel panel-primary">
    <div class="panel-heading clearfix">
        <i class="fa fa-list fa-fw"></i>
        Record List
        <div class="pull-right">
            <form class="filter-form">
                <!--<span class="btn btn-success btn-xs export-btn fa fa-file-excel-o"> Export</span>-->
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-xs" data-modal="choose-columns"
                            data-table-id="1"><span
                            class="fa fa-table"></span> Columns
                    </button>
                </div>
                <input type="hidden" name="group"> <!--<div class="btn-group">
                  
                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span
                            class="glyphicon glyphicon-filter"></span> Colour By
                    </button>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li><a href="#" class="group-filter" id="campaign_name">Campaign</a></li>
                        <li><a href="#" class="group-filter" id="company_name">Company</a></li>
                        <li><a href="#" class="group-filter" id="contact_name">Contact</a></li>
                        <li><a href="#" class="group-filter" id="outcome">Outcome</a></li>
                        <li><a href="#" class="group-filter" id="ownership">Ownership</a></li>
                        <li class="divider"></li>
                        <li><a class="group-filter" ref="#" style="color: green;">Colour by</a></li>
                    </ul>
                </div>-->
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
                        <div class="panel-heading clearfix">
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

<script type="text/javascript">
    table_columns = <?php echo json_encode($columns) ?>;

    $(document).ready(function () {
        maps.initialize("records");
        view_records.init();

        view_records.has_filter = "<?php echo ((isset($_SESSION['filter']['values']) && !empty($_SESSION['filter']['values']))?true:false) ?>";
    });

</script>