<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div id="loading-overlay"></div>
                <div class="row" style="padding:0; margin:0;">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div id="view-container">
                            <img class="table-loading" src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
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
                                                <span
                                                    class="pointer btn-default input-group-addon get-current-location-btn"><span
                                                        class="glyphicon glyphicon-map-marker"></span></span>
                                    <input type="text" class="form-control input-sm" name="postcode"
                                           placeholder="Postcode..."
                                           title="Enter the location"/>
                                    <span class="pointer btn-default input-group-addon get-location-btn">Go</span>
                                </div>
                                <div class="route-mode">
                                                         <span class="planner-travel-mode DRIVING pointer"
                                                               item-mode="DRIVING"><img
                                                                 width="25px;"
                                                                 src="<?php echo base_url() ?>assets/img/icons/car.png"/></span>
                                                        <span
                                                            class="planner-travel-mode BICYCLING pointer"
                                                            item-mode="BICYCLING"><img
                                                                width="25px;"
                                                                src="<?php echo base_url() ?>assets/img/icons/cycle.png"/></span>
                                                        <span
                                                            class="planner-travel-mode WALKING pointer"
                                                            item-mode="WALKING"><img
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

<style>
    body {
        background-color: white;
    }

    .loading-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: #000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        z-index: 10;
        top: 0;
        left: 0
    }

    .container-fluid {
        /*padding: 100px 0px 60px !important;*/
    }

    .top-row {
        padding: 10px 10px 0;
    }

    .bottom-row {
        padding: 0px 10px 10px;
    }

    .panel-body {
        overflow: hidden;
        padding: 0px !important;
    }

    #view-container {
        overflow-y: auto;
        height: 100%;
        overflow-x: hidden;
    }

    #map-view {
        padding: 0 !important;
        height: 100%;
    }

    .dataTables_scrollBody {
        overflow-y: auto;
        overflow-x: hidden;
        height: 100%;
    }

    .dataTables_scroll table {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff
    }

    .dataTables_scroll {
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        background: #eee;
        margin-bottom: 10px
    }

    /*.dataTables_scrollHeadInner {  padding-right:17px; } */
    .planner-travel-mode {
        opacity: 0.4;
        filter: alpha(opacity=40);
    }

    #map-canvas {
        position: relative;
        overflow: visible;
        transform: translateZ(0px);
        background-color: rgb(229, 227, 223);
        min-height: 585px;
        width: 100%;
        margin: 0px;
        padding: 0px
    }

    #map-form .route-mode {
        float: right;
        margin-right: 50px;
    }

    #map-form .planner-travel-mode {
        display: none
    }

    #map-form {
        position: absolute;
        top: 8px;
        width: 100%;
    }

    #map-form .input-group {
        width: 250px;
        left: 10px;
    }

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
        display: none;

    }

    .route-info span {
        font-weight: bold;
        padding: 5px;
        font-size: 14px;
        text-align: center;
        color: #fff;
        line-height: 0.5
    }
</style>


<script type="text/javascript">
    var process_url = 'appointments/appointment_data';
    var page_name = "appointment";
    var table_id = 3; //the records table
    var table_columns = <?php echo json_encode($columns) ?>; //the columns in this view

    $(document).ready(function () {
        view.init();
        view.has_filter = "<?php echo((isset($_SESSION['filter']['values']) && !empty($_SESSION['filter']['values'])) ? true : false) ?>";
        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'All': ["02/07/2014", moment().add(50, 'years')],
                    'Last Month': [moment().add(-1, 'month').startOf('month'), moment().add(-1, 'month').endOf('month')],
                    'Last 7 Days': [moment().add(-7, 'days'), moment()],
                    'Yesterday': [moment().add(-1, 'days'), moment()],
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Next 7 Days': [moment(), moment().add(6, 'days')],
                    'Next 30 Days': [moment(), moment().add(29, 'days')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "02/07/2014",
                startDate: moment(),
                endDate: moment().add('days', 29)
            },
            function (start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('D/M/Y') + ' - ' + end.format('D/M/Y'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                view.reload_table();
            });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });
    });

</script>