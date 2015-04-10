<div class="row">
    <div class="col-lg-7">
        <h1 class="page-header">Planner</h1>
        <div id="planner-table" style="overflow-y: scroll; overflow-x: hidden; height: 650px;">
        </div>
    </div>
    <div class="col-lg-5">
        <h1 class="planner-map">
            <div id="map-canvas"
                 style="position: relative; overflow: hidden; transform: translateZ(0px); background-color: rgb(229, 227, 223);"></div>
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
</style>

<script>
    $(document).ready(function () {
        planner.init();
    });
</script>