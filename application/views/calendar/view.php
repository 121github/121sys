<div style="padding-bottom:50px">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <i class="fa fa-calendar-o fa-fw"></i> Calendar
            <div class="pull-right">
                <form class="filter-form">
                    <?php
                    $options = array();
                    if (in_array("import ics", $_SESSION['permissions'])) {
                        //array_push($options,'<li><a href="#" data-modal="import-appointment-btn" id="">Import appointments</a></li>');
                    }
                    if (in_array("export ics", $_SESSION['permissions'])) {
                        //array_push($options,'<li><a href="#" id="export-appointment-btn">Export appointments</a></li>');
                    }
                    ?>
                    <?php if (in_array("slot availability", $_SESSION['permissions'])) { ?>
                        <div class="btn-group">
                            <a class="btn btn-default btn-xs" href="<?php echo base_url() ?>admin/availability">
                                <span class="glyphicon glyphicon-user"></span> Manage availability
                            </a>
                        </div>
                    <?php } ?>

                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-filter"></span> Options
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <?php if (in_array("add appointments", $_SESSION['permissions'])) { ?>
                                <li class="pointer add-cal-appointment-btn">
                                    <a>Add Appointment</a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="<?php echo base_url() ?>calendar/google">Google Calendar</a>
                            </li>
                            <li>
                                <a id="switch-cal-view"
                                   data-cal-view="<?php echo $calendar_view == "combined" ? "2" : "1" ?>"
                                   href="#"><?php echo($calendar_view == "combined" ? "Seperate Events" : "Combine Events") ?></a>
                            </li>
                            <?php foreach ($options as $option): ?>
                                <?php echo $option; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </form>
            </div>
        </div>
        <div class="panel-body">
            <div class="page-header">
                <div class="pull-right form-inline">
                    <div class="form-inline pull-right" style="padding-left:20px">
                        <div class="form-group">
                            <select id="campaign-cal-select"
                                    <?php if ($disable_campaign || isset($_SESSION['current_campaign'])){ ?>disabled<?php } ?>
                                    name="campaigns[]" multiple class="selectpicker" data-width="100%"
                                    data-size="5" title="Select campaigns">
                                <?php
                                //this part select the global campaign if they have one selected, if not it will select any that has previously been selected in the filter
                                foreach ($campaigns as $row): ?>
                                    <option
                                        <?php if (!isset($_SESSION['current_campaign'])) {
                                            if (in_array($row['id'], $_SESSION['campaign_access']['array'])): ?>
                                                <?php if (@in_array($row['id'], $_SESSION['calendar-filter']['campaigns'])) {
                                                    echo "selected";
                                                } endif;
                                        } else {
                                            echo(@$_SESSION['current_campaign'] == $row['id'] ? "selected" : "");
                                        } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>

                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <select id="user-select" name="users[]" multiple class="selectpicker" data-width="100%"
                                    data-size="5" title="Select attendees">
                                <?php foreach ($users as $row): ?>
                                    <option <?php if (@in_array($row['id'], $_SESSION['calendar-filter']['users'])) {
                                        echo "selected";
                                    } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group location-form">
                            <button id="distance-cal-button" class="btn btn-default"><span
                                    class="glyphicon glyphicon-cog"></span> Distance
                            </button>
                            <div style="display:none" id="dist-form">
                                <p><label>Postcode</label>
                                <div class="input-group"><input type="text" class="form-control current_postcode_input"
                                                                placeholder="Enter a postcode..." name="postcode"
                                                                value="<?php echo @$_SESSION['calendar-filter']['postcode'] ?>">
                                    <div class="input-group-addon pointer btn locate-postcode"><span
                                            class="glyphicon glyphicon-map-marker"></span> Use my location
                                    </div>
                                </div>
                                <div class="error geolocation-error"></div>
                                </p>
                                <p>
                                    <label>Maximum Distance</label>
                                    <select name="distance" class="distance-select form-control">
                                        <option value="1" <?php if (@$_SESSION['calendar-filter']['distance'] == 1) {
                                            echo "selected";
                                        } ?> >1 Mile
                                        </option>
                                        <option value="5" <?php if (@$_SESSION['calendar-filter']['distance'] == 5) {
                                            echo "selected";
                                        } ?> >5 Mile
                                        </option>
                                        <option value="10" <?php if (@$_SESSION['calendar-filter']['distance'] == 10) {
                                            echo "selected";
                                        } ?> >10 Mile
                                        </option>
                                        <option value="15" <?php if (@$_SESSION['calendar-filter']['distance'] == 15) {
                                            echo "selected";
                                        } ?> >15 Mile
                                        </option>
                                        <option value="20" <?php if (@$_SESSION['calendar-filter']['distance'] == 20) {
                                            echo "selected";
                                        } ?> >20 Mile
                                        </option>
                                        <option value="30" <?php if (@$_SESSION['calendar-filter']['distance'] == 30) {
                                            echo "selected";
                                        } ?> >30 Mile
                                        </option>
                                        <option value="50" <?php if (@$_SESSION['calendar-filter']['distance'] == 50) {
                                            echo "selected";
                                        } ?>> 50 Mile
                                        </option>
                                        <option
                                            value="100" <?php if (@$_SESSION['calendar-filter']['distance'] == 100) {
                                            echo "selected";
                                        } ?> >100 Mile
                                        </option>
                                        <option
                                            value="200" <?php if (@$_SESSION['calendar-filter']['distance'] == 200) {
                                            echo "selected";
                                        } ?> >200 Mile
                                        </option>
                                        <option
                                            value="1500" <?php if (@$_SESSION['calendar-filter']['distance'] == 1500) {
                                            echo "selected";
                                        } ?> >Any Distance
                                        </option>
                                    </select>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <h3></h3>
                <div class="btn-group">
                    <button class="btn btn-success btn-xs" data-calendar-nav="prev" data-loading-text="Loading..."><<
                        Prev
                    </button>
                    <button class="btn btn-success btn-xs" data-calendar-nav="today" data-loading-text="Loading...">
                        Today
                    </button>
                    <button class="btn btn-success btn-xs" data-calendar-nav="next" data-loading-text="Loading...">Next
                        >>
                    </button>
                </div>
                <div class="btn-group">
                    <button class="btn btn-info btn-xs" data-calendar-view="year" data-loading-text="Loading...">Year
                    </button>
                    <button class="btn btn-info btn-xs active" data-calendar-view="month"
                            data-loading-text="Loading...">Month
                    </button>
                    <button class="btn btn-info btn-xs" data-calendar-view="week" data-loading-text="Loading...">Week
                    </button>
                    <button class="btn btn-info btn-xs" data-calendar-view="day" data-loading-text="Loading...">Day
                    </button>
                </div>
                <small></small>
            </div>
            <div id="calendar"></div>
        </div>
    </div>


</div>
<div class="modal fade" id="events-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">Event</h3>
            </div>
            <div class="modal-body" style="height: 400px"></div>
            <div class="modal-footer">
                <div role="tabpanel">
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        importer.init();
        calendar_view = "<?php echo($calendar_view ? $calendar_view : "combined") ?>";
    });

</script>
