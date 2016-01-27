

       


        <div class="row" style="padding:0; margin:0">
        <div class="col-lg-12 record-view" style="height:100%; overflow:auto">
              
            <form class="filter-form">
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-xs" data-modal="choose-columns"
                            data-table-id="1"><span
                            class="fa fa-table"></span> Columns
                    </button>
                </div>
                <input type="hidden" name="group"> 
                <div class="btn-group">
                    <input style="display:none" type="checkbox" id="map-view-toggle"
                           data-on="<i class='glyphicon glyphicon-map-marker'></i> Map View"
                           data-off="<i class='glyphicon glyphicon-map-marker'></i> Map View" data-toggle="toggle">
                </div>
            </form>

        
                    <div id="table-wrapper" style="overflow:auto">
                <img class="table-loading"
                     src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
            </div>
            </div>
            <div class="col-lg-6 map-view" style="display: none">
                <h1 class="planner-map">
                    <div class="map-wrapper">
                        <div id="map-canvas"
                             style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>

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

<style>
body { }
.container-fluid { padding:0 !important; }
.record-view { padding:60px 0 !important; overflow-y:auto;overflow-x:hidden }
.map-view { padding:50px 0 !important;}
.dataTables_scrollBody { overflow-y:auto;overflow-x:hidden; height:400px; }
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
	var table_id = 1; //the records table
	var table_columns = <?php echo json_encode($columns) ?>; //the columns in this view
    $(document).ready(function () {
        view_records.init();
        view_records.has_filter = "<?php echo ((isset($_SESSION['filter']['values']) && !empty($_SESSION['filter']['values']))?true:false) ?>";
    });

</script>