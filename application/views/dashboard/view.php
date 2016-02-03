<div class="row">
    <div class="col-lg-8">
        <h1 class="page-header">
            <?php echo $dashboard['name']; ?>
            <small><?php if (isset($_SESSION['current_campaign_name'])) { echo @$_SESSION['current_campaign_name']; } ?></small>
        </h1>
    </div>
    <div class="col-lg-4 page-header" style="text-align: right;">
        <ul class="nav">
            <li>
                <div class="btn-group">
                    <span class="btn btn-default btn new-panel" data-item="<?php $dashboard['dashboard_id'] ?>">
                        <span class="fa fa-plus fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn show-charts" data-item="0" charts="chart-div-system,chart-div-email,chart-div-sms,chart-div-outcome" data="data-system,data-email,data-sms,data-outcome">
                        <span class="fa fa-bar-chart-o fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn refresh-overview-data">
                        <span class="glyphicon glyphicon-refresh" style="padding-left:3px; color:black;"></span>
                    </span>
                    <a href="#filter-right" class="btn btn-default btn">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#filter-view">View current filters</a></li>
                        <li><a class='clear-filters' href="#">Set default filters</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <!-- /.col-lg-12 -->
</div>

<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
    <div style="padding:30px 20px 3px">
        <form class="filter-form">
            <input type="hidden" name="comments">
            <label style="margin-top: 5%;">Campaign</label>
            <select name="campaigns[]" class="selectpicker campaign-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($campaigns_by_group as $type => $data) { ?>
                    <optgroup label="<?php echo $type ?>">
                        <?php foreach ($data as $row) { ?>
                            <option <?php if (isset($_SESSION['current_campaign']) && $row['id'] == $_SESSION['current_campaign']) {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>

            <?php if (count($campaign_outcomes) > 0) { ?>
                <label style="margin-top: 5%;">Outcome</label>
                <select name="outcomes[]" class="selectpicker outcome-filter" id="outcome-filter" multiple
                        data-width="100%" data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true">
                    <?php foreach ($campaign_outcomes as $type => $data) { ?>
                        <optgroup label="<?php echo $type ?>">
                            <?php foreach ($data as $row) { ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Team</label>
                <select name="teams[]" class="selectpicker team-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($team_managers as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Agent</label>
                <select name="agents[]" class="selectpicker agent-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($agents as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <label style="margin-top: 5%;">Source</label>
            <select name="sources[]" class="selectpicker source-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($sources as $row) { ?>
                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>

            <button id="filter-overview-submit" class="btn btn-primary pull-right" style="margin-top: 5%;">Submit</button>
        </form>
    </div>
</nav>

<nav id="filter-view" class="mm-menu mm--horizontal mm-offcanvas">
    <div id="filters"></div>
</nav>

<!-- /.row -->
<!--<div class="row">-->
<!--    <div class="box">-->
<!--        <div class="boxheader">Header</div>-->
<!--        <div class="boxbody">-->
<!--            Resize me-->
<!--        </div>-->
<!--        <div class="win-size-grip"></div>-->
<!--    </div>-->
<!--</div>-->




<div id="yes-drop" class="draggable drag-drop"> #yes-drop </div>
<div id="yes-drop" class="draggable drag-drop"> #yes-drop </div>
<div id="yes-drop" class="draggable drag-drop"> #yes-drop </div>
<div id="yes-drop" class="draggable drag-drop"> #yes-drop </div>
<div id="yes-drop" class="draggable drag-drop"> #yes-drop </div>

<div id="threecol-dropzone" class="dropzone">
    <div class="row">
        <div class="col-lg-4">
            <div id="onecol-dropzone" class="dropzone one"></div>
        </div>
        <div class="col-lg-4">
            <div id="onecol-dropzone" class="dropzone two"></div>
        </div>
        <div class="col-lg-4">
            <div id="onecol-dropzone" class="dropzone"></div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
        <div class="col-lg-6">
            <div id="onecol-dropzone" class="dropzone"></div>
        </div>
        <div class="col-lg-6">
            <div id="onecol-dropzone" class="dropzone"></div>
        </div>
    </div>

</div>



<!-- /.row -->

<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
<!--<script src="--><?php //echo base_url(); ?><!--assets/js/plugins/jquery-resizable/jquery-resizable.min.js"></script>-->
<!--<script>-->
<!--    $(document).ready(function () {-->
<!--        dashboard.init();-->
<!--        //dashboard.load_dash(--><?php //$dashboard_id ?><!--);-->
<!---->
<!--        $(".box").resizable({-->
<!--            handleSelector: ".win-size-grip"-->
<!--        });-->
<!--    });-->
<!--</script> -->


<!--<script src="--><?php //echo base_url(); ?><!--assets/js/plugins/interact/interact.min.js"></script>-->
<!--<script src="//cdn.jsdelivr.net/interact.js/1.2.6/interact.min.js"></script>-->
<script src="//cdnjs.cloudflare.com/ajax/libs/interact.js/1.2.6/interact.min.js"></script>
<script>
    $(document).ready(function () {


        /* The dragging code for '.draggable' from the demo above
         * applies to this demo as well so it doesn't have to be repeated. */
        // Initialize Interact.js Drag & Drop
        interact('.draggable').draggable({
            inertia: true,
            restrict: {
                restriction: "#visualize",
                endOnly: true,
                elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
            },
            onmove: function (event) {
                event.target.style.width =  '30%';
                var target = event.target,
                // keep the dragged position in the data-x/data-y attributes
                    x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
                    y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

                // translate the element
                target.style.webkitTransform =
                    target.style.transform =
                        'translate(' + x + 'px, ' + y + 'px)';

                // update the posiion attributes
                target.setAttribute('data-x', x);
                target.setAttribute('data-y', y);
            },
            onend: function(event) {
                //console.log(event);
            }
        });

// enable draggables to be dropped into this
        interact('.dropzone').dropzone({
            // only accept elements matching this CSS selector
            accept: '#yes-drop',
            // Require a 75% element overlap for a drop to be possible
            overlap: 0.75,

            // listen for drop related events:

            ondropactivate: function (event) {
                // add active dropzone feedback
                event.target.classList.add('drop-active');
            },
            ondragenter: function (event) {
                var draggableElement = event.relatedTarget,
                    dropzoneElement = event.target;

                // feedback the possibility of a drop
                dropzoneElement.classList.add('drop-target');
                draggableElement.classList.add('can-drop');
                draggableElement.textContent = 'Dragged in';
            },
            ondragleave: function (event) {
                // remove the drop feedback style
                event.target.classList.remove('drop-target');
                event.relatedTarget.classList.remove('can-drop');
                event.relatedTarget.textContent = 'Dragged out';
            },
            ondrop: function (event) {
                event.relatedTarget.textContent = 'Dropped';
                var rect = event.target.getBoundingClientRect();
                event.relatedTarget.removeAttribute('style');
                event.relatedTarget.removeAttribute('data-x');
                event.relatedTarget.removeAttribute('data-y');

                event.relatedTarget.style.width =  rect.width + 'px';

                event.target.appendChild(event.relatedTarget);
                event.target.insertBefore(event.relatedTarget, event.target.firstChild);

            },
            ondropdeactivate: function (event) {
                // remove active dropzone feedback
                event.target.classList.remove('drop-active');
                event.target.classList.remove('drop-target');
            }
        });


    });
</script>

