

       

<div class="row">
<div class="col-xs-12">
<div class="panel panel-primary">
<div class="panel-heading"> <i class="fa fa-map-marker fa-fw"></i> Journey planner
<div class="pull-right">
            <form class="filter-form">
                <div class="btn-group">
                    <input type="hidden" name="user" value="<?php echo $user_id ?>">
                    <?php if(in_array("admin planner", $_SESSION['permissions'])) { ?>
                        <?php if(!empty($drivers)){ ?>
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span id="user-filter-name">Drivers </span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" class="user-filter" data-id="<?php echo $_SESSION['user_id'] ?>">My Planner</a></li>      <li class="divider"></li>
                        <?php foreach($drivers as $row){ ?>
                      
                         <li><a href="#" class="user-filter" data-id="<?php echo $row['user_id'] ?>"><?php echo $row['region_name'] ?>: <?php echo $row['name'] ?> </a></li>
                         <?php } ?>
                                  
                        </ul>
                        <?php } else { ?>
                         <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span id="user-filter-name">Users </span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                                 <li><a href="#" class="user-filter" data-id="<?php echo $_SESSION['user_id'] ?>">My Planner</a></li>    <li class="divider"></li>
                        <?php foreach($attendees as $row){ ?>
            
                         <li><a href="#" class="user-filter" data-id="<?php echo $row['user_id'] ?>"><?php echo $row['name'] ?> </a></li><?php } ?>  
                        </ul>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="btn-group">
                    <input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>">
                    <button type="button" class="daterange btn btn-default btn-xs"><span
                            class="glyphicon glyphicon-calendar"></span> <span
                            class="date-text"> <?php echo "Today"; ?> </span></button>
                </div>
            
            </form>
            </div>
</div>
<div class="panel-body" style="padding:0; margin:0; position:relative">
        <div class="row" style="padding:0; margin:0;">
        <div class="col-xs-12 col-sm-6 col-md-6" id="view-container">
   <form class="directions-form">
                            <div class="row">
                                <div class="col-sm-6 col-xs-12" style="margin-bottom: 10px;">
                                    <div class="input-group" style="margin-bottom: 5px;">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-sm get-origin-location-btn" type="button">
                                                <span style=""
                                                      class="glyphicon glyphicon-map-marker"></span> Start
                                            </button>
                                        </span>
                                        <input type="text" class="form-control input-sm" name="origin"
                                               placeholder="Enter starting postcode"
                                               title="Enter starting postcode"/>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-sm get-destination-location-btn" type="button">
                                                <span
                                                      class="glyphicon glyphicon-map-marker"></span> End&nbsp;
                                            </button>
                                        </span>
                                        <input type="text" class="form-control input-sm" name="destination"
                                               placeholder="Enter final postcode"
                                               title="Enter final postcode"/>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-xs-12">
                                    <input type="checkbox" id="optimized" name="optimized"
                                           data-on="<i class='glyphicon glyphicon-ok'></i> Best Route"
                                           data-off="<i class='glyphicon glyphicon-remove'></i> Best Route"
                                           data-toggle="toggle" data-size="small">
                                    <button style="margin:3px 0px 3px" class="btn btn-sm btn-success btn calc-route-btn">Get
                                        Route!
                                    </button>
                                </div>
                            </div>
                        </form>
                         <div id="draggablePanelList">
                            <img src=' <?php echo base_url(); ?>assets/img/ajax-loader-bar.gif'>
                        </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-6" id="map-view">
                        <div id="map-canvas"></div>
                  <div class="directions-btn">
                        <form class="form-horizontal" id="map-form">
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
                            <div class="route-mode">
 <span class="planner-travel-mode DRIVING pointer" item-mode="DRIVING"><img
         width="25px;" src="<?php echo base_url() ?>assets/img/icons/car.png"/></span>
                                        <span 
                                              class="planner-travel-mode BICYCLING pointer" item-mode="BICYCLING"><img
                                                width="25px;" src="<?php echo base_url() ?>assets/img/icons/cycle.png"/></span>
                                        <span 
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

            </div>
        </div>
    </div>

</div>
</div>
</div>
</div>
<style>
body { }
.container-fluid { }
.top-row { padding:10px 10px 0; }
.bottom-row { padding:0px 10px 10px; }
.panel-body { overflow:hidden; }
#view-container { margin:0; padding:20px 10px; overflow-y:auto;overflow-x:hidden }
#map-view { padding:0 !important; height:100%; }
.dataTables_scrollBody { overflow-y:auto;overflow-x:hidden; height:100%; }
.planner-travel-mode {opacity: 0.4; filter: alpha(opacity=40);}
    #map-canvas {
		position: relative; 
		overflow: visible; 
		transform: translateZ(0px); 
		background-color: rgb(229, 227, 223);
        min-height: 570px;
		width:100%;
        margin: 0px;
        padding: 0px
    }
	#map-form .route-mode {
	float:right;margin-right:50px;
	}
	#map-form .planner-travel-mode { display:none }
    #map-form {
        position: absolute;
        top: 8px;
        width: 100%;
    }

    #map-form .input-group {
        width: 250px;
        left: 50px;
    }

    #show-directions {
        margin-right: 50px;
    }

	.planner-travel-mode img {width: 25px }
	.planner-title { display:table-cell; width:60% }
	.record-planner-item { margin:10px 0 }
	
	    #show-directions {
        margin-right: 50px;
    }

    .route-info {
        background-color: rgba(000, 000, 000, 0.4);
        float: right;
        margin-right: 50px;
                    line-height: 0.3;
                   padding: 10px;
                    margin-bottom: 5px;
                    border-radius: 4px;
					display:none;

		
    }
	.route-info span {font-weight: bold; padding:5px; font-size: 14px; text-align: center;color: #fff; line-height:0.5	}
</style>

<script type="text/javascript">
var page_name ="planner";
var process_url = "planner/planner_data";
    $(document).ready(function () {
        view.init();
    });

</script>