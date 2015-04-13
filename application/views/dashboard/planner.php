<div class="row">
    <div class="col-lg-6">
        <div class="planner-filter">
            <div class="row">
                <div class="col-lg-2">
                    Journey
                </div>
                <div class="col-lg-3">
                    <form class="form-horizontal">
                        <div class="form-group input-group-sm">
                            <input type="text" class="form-control" name="postcode" placeholder="Postcode..." title="Enter the location" required/>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div id="planner-table" style="overflow-x: hidden;"></div>
    </div>
    <div class="col-lg-6">
        <h1 class="planner-map">
            <div id="map-canvas"
                 style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
            <div id="directionsPanel"></div>
        </h1>
    </div>
    <!-- /.col-lg-12 -->
</div>


<style>
    #map-canvas {
        height: 750px;
        margin: 0px;
        padding: 0px
    }
    #directionsPanel {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 50%;
        font-size: 9px;
        background-color: white;
    }
</style>

<script>
    $(document).ready(function () {
        planner.init();
    });
</script>