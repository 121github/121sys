<div style="padding:0 50px 50px">
    <div class="page-header">
        <div class="pull-right form-inline">
            <div class="form-inline pull-right" style="padding-left:20px">
                <div class="form-group" <?php if ($disable_campaign){ ?>style="display:none"<?php } ?>>
                    <select id="campaign-cal-select" name="campaigns[]" multiple class="selectpicker" data-width="100%"
                            data-size="5" title="Select campaigns">
                        <?php foreach ($campaigns as $row): ?>
                            <?php if (in_array($row['id'], $_SESSION['campaign_access']['array'])): ?>
                                <option <?php if (@in_array($row['id'], $_SESSION['calendar-filter']['campaigns'])) {
                                    echo "selected";
                                } else {
                                    echo(@$_SESSION['current_campaign'] == $row['id'] ? "selected" : "");
                                } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php endif ?>
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
                            <input name="postcode" value="<?php echo @$_SESSION['calendar-filter']['postcode'] ?>"
                                   class="form-control current_postcode_input"/>
                            <a id="use-my-location" href="#" class="locate-postcode" type="button" data-icon="location"
                               data-iconpos="right">Find my location</a>

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
                                <option value="100" <?php if (@$_SESSION['calendar-filter']['distance'] == 100) {
                                    echo "selected";
                                } ?> >100 Mile
                                </option>
                                <option value="200" <?php if (@$_SESSION['calendar-filter']['distance'] == 200) {
                                    echo "selected";
                                } ?> >200 Mile
                                </option>
                                <option value="1500" <?php if (@$_SESSION['calendar-filter']['distance'] == 1500) {
                                    echo "selected";
                                } ?> >Any Distance
                                </option>
                            </select>
                        </p>
                    </div>
                </div>
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" data-calendar-nav="prev" data-loading-text="Loading..."><< Prev</button>
                <button class="btn" data-calendar-nav="today" data-loading-text="Loading...">Today</button>
                <button class="btn btn-primary" data-calendar-nav="next" data-loading-text="Loading...">Next >></button>
            </div>
            <div class="btn-group">
                <button class="btn btn-info" data-calendar-view="year" data-loading-text="Loading...">Year</button>
                <button class="btn btn-info active" data-calendar-view="month" data-loading-text="Loading...">Month
                </button>
                <button class="btn btn-info" data-calendar-view="week" data-loading-text="Loading...">Week</button>
                <button class="btn btn-info" data-calendar-view="day" data-loading-text="Loading...">Day</button>
            </div>
        </div>
        <h3></h3>
        <small></small>
    </div>
    <div id="calendar"></div>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div style="display:none" id="appointment-rule-modal">
    <div role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation"><a href="#rules" aria-controls="rules" role="tab" data-toggle="tab">Rules</a></li>
            <li role="presentation" class="active"><a href="#add-rule" aria-controls="add-rule" role="tab" data-toggle="tab">Add Rule</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane" id="rules">

            </div>
            <div role="tabpanel" class="tab-pane active" id="add-rule">
                <form class="appointment-rule-form form-horizontal">
                    <p><label>Block Day<span class="block-day-error" style="color: red; display: none"> Select a block day</span></label>
                        <input name="block_day" value="" placeholder="Add the block day..." class="form-control block-day"/>
                    </p>
                    <p><label>Reason<span class="reason-error" style="color: red; display: none"> Select a reason</span></label>
                        <select name="reason_id" class="reason-select" title="Choose the reason..." data-width="100%" required>
                            <option value="">Choose a reason...</option>
                            <?php foreach($appointment_rule_reasons as $appointment_rule_reason): ?>
                                <option value="<?php echo $appointment_rule_reason['id'] ?>"><?php echo $appointment_rule_reason['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p class="other_reason"><label>Other Reason</label>
                        <input name="other_reason" placeholder="Other reason..." value="" class="form-control"/>
                    </p>
                    <p>
                        <label>Attendees<span class="attendee-error" style="color: red; display: none"> Select an agent</span></label>
                        <select name="attendees[]" class="attendee-select" title="Select attendees" multiple data-width="100%" required>
                            <?php foreach($users as $attendee): ?>
                                <option value="<?php echo $attendee['id'] ?>"><?php echo $attendee['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>