<form class="dashboard-filter-form" method="post">
    <input type="hidden" name="dashboard_id">
    <div class="row">
        <div class="col-lg-6">
            <label style="margin-top: 5%; width: 100%">
                <div class="row">
                    <div class="col-lg-6">Date</div>
                    <div class="col-lg-6">
                        <input type='checkbox' id='dash-date-check' name='dash_date_check'
                               data-toggle='toggle' data-width='100' data-size="mini" data-onstyle='success'
                               data-offstyle='danger' data-on='Editable' data-off='Not Editable'
                               value=<?php echo((isset($filters['date'])) ? $filters['date']['editable'] : "1"); ?>
                            <?php echo(isset($filters['date']) ? ($filters['date']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                    </div>
                </div>
            </label>
            <select name="date" class="selectpicker date-filter" data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <option value="">Nothing selected</option>
                <?php foreach ($dates as $type => $value) { ?>
                    <option <?php if (isset($filters['date']) && (in_array($type, $filters['date']['values']))) {
                        echo "Selected";
                    } ?> value="<?php echo $type ?>"><?php echo $value ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <label style="margin-top: 5%; width: 100%">
                <div class="row">
                    <div class="col-lg-6">Campaign</div>
                    <div class="col-lg-6">
                        <input type='checkbox' id='dash-campaigns-check' name='dash_campaigns_check'
                               data-toggle='toggle' data-width='100' data-size="mini" data-onstyle='success'
                               data-offstyle='danger' data-on='Editable' data-off='Not Editable'
                               value=<?php echo((isset($filters['campaigns'])) ? $filters['campaigns']['editable'] : "1"); ?>
                            <?php echo(isset($filters['campaigns']) ? ($filters['campaigns']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                    </div>
                </div>
            </label>
            <select name="campaigns[]" class="selectpicker campaign-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($campaigns_by_group as $type => $data) { ?>
                    <optgroup label="<?php echo $type ?>">
                        <?php foreach ($data as $row) { ?>
                            <option <?php if (isset($filters['campaigns']) && (in_array($row['id'], $filters['campaigns']['values']))) {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>

            <?php if (count($campaign_outcomes) > 0) { ?>
                <label style="margin-top: 5%; width: 100%">
                    <div class="row">
                        <div class="col-lg-6">Outcome</div>
                        <div class="col-lg-6">
                            <input type='checkbox' id='dash-outcomes-check' name='dash_outcomes_check'
                                   data-toggle='toggle' data-width='100' data-size="mini" data-onstyle='success'
                                   data-offstyle='danger' data-on='Editable' data-off='Not Editable'
                                   value=<?php echo((isset($filters['outcomes'])) ? $filters['outcomes']['editable'] : "1"); ?>
                                <?php echo(isset($filters['outcomes']) ? ($filters['outcomes']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                        </div>
                    </div>
                </label>
                <select name="outcomes[]" class="selectpicker outcome-filter" id="outcome-filter" multiple
                        data-width="100%" data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true">
                    <?php foreach ($campaign_outcomes as $type => $data) { ?>
                        <optgroup label="<?php echo $type ?>">
                            <?php foreach ($data as $row) { ?>
                                <option <?php if ((isset($filters['outcomes'])) && (in_array($row['id'], $filters['outcomes']['values']))) {
                                    echo "Selected";
                                } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            <?php } ?>





            <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%; width: 100%">
                    <div class="row">
                        <div class="col-lg-6">Team</div>
                        <div class="col-lg-6">
                            <input type='checkbox' id='dash-teams-check' name='dash_teams_check' data-toggle='toggle'
                                   data-width='100' data-size="mini" data-onstyle='success' data-offstyle='danger'
                                   data-on='Editable' data-off='Not Editable'
                                   value=<?php echo((isset($filters['teams'])) ? $filters['teams']['editable'] : "1"); ?>
                                <?php echo(isset($filters['teams']) ? ($filters['teams']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                        </div>
                    </div>
                </label>
                <select name="teams[]" class="selectpicker team-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($team_managers as $row) { ?>
                        <option <?php if ((isset($filters['teams'])) && (in_array($row['id'], $filters['teams']['values']))) {
                            echo "Selected";
                        } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
            <label style="margin-top: 5%; width: 100%">
                <div class="row">
                    <div class="col-lg-6">Pot</div>
                    <div class="col-lg-6">
                        <input type='checkbox' id='dash-pot-check' name='dash_pot_check' data-toggle='toggle'
                               data-width='100' data-size="mini" data-onstyle='success' data-offstyle='danger'
                               data-on='Editable' data-off='Not Editable'
                               value=<?php echo((isset($filters['pot'])) ? $filters['pot']['editable'] : "1"); ?>
                            <?php echo(isset($filters['pot']) ? ($filters['pot']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                    </div>
                </div>
            </label>
            <select name="pot[]" class="selectpicker pot-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($pots as $row) { ?>
                    <option <?php if ((isset($filters['pot'])) && (in_array($row['id'], $filters['pot']['values']))) {
                        echo "Selected";
                    } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-lg-6">
            <label style="margin-top: 5%; width: 100%">
                <div class="row">
                    <div class="col-lg-6">User</div>
                    <div class="col-lg-6">
                        <input type='checkbox' id='dash-user-check' name='dash_user_check' data-toggle='toggle'
                               data-width='100' data-size="mini" data-onstyle='success' data-offstyle='danger'
                               data-on='Editable' data-off='Not Editable'
                               value=<?php echo((isset($filters['user'])) ? $filters['user']['editable'] : "1"); ?>
                            <?php echo(isset($filters['user']) ? ($filters['user']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                    </div>
                </div>
            </label>
            <select name="user[]" class="selectpicker user-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($users as $type => $data) { ?>
                    <optgroup label="<?php echo $type ?>">
                        <?php foreach ($data as $row) { ?>
                            <option <?php if ((isset($filters['user'])) && (in_array($row['id'], $filters['user']['values']))) {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>


            <label style="margin-top: 5%; width: 100%">
                <div class="row">
                    <div class="col-lg-6">Branch</div>
                    <div class="col-lg-6">
                        <input type='checkbox' id='dash-branches-check' name='dash_branches_check' data-toggle='toggle'
                               data-width='100' data-size="mini" data-onstyle='success' data-offstyle='danger'
                               data-on='Editable' data-off='Not Editable'
                               value=<?php echo((isset($filters['branches'])) ? $filters['branches']['editable'] : "1"); ?>
                            <?php echo(isset($filters['branches']) ? ($filters['branches']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                    </div>
                </div>
            </label>
            <select name="branches[]" class="selectpicker branch-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($branches as $row) { ?>
                    <option <?php if ((isset($filters['branches'])) && (in_array($row['id'], $filters['branches']['values']))) {
                        echo "Selected";
                    } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>

            <label style="margin-top: 5%; width: 100%">
                <div class="row">
                    <div class="col-lg-6">Source</div>
                    <div class="col-lg-6">
                        <input type='checkbox' id='dash-sources-check' name='dash_sources_check' data-toggle='toggle'
                               data-width='100' data-size="mini" data-onstyle='success' data-offstyle='danger'
                               data-on='Editable' data-off='Not Editable'
                               value=<?php echo((isset($filters['sources'])) ? $filters['sources']['editable'] : "1"); ?>
                            <?php echo(isset($filters['sources']) ? ($filters['sources']['editable'] == "1" ? "checked" : "") : "checked"); ?>>
                    </div>
                </div>
            </label>
            <select name="sources[]" class="selectpicker source-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($sources as $row) { ?>
                    <option <?php if ((isset($filters['sources'])) && (in_array($row['id'], $filters['sources']['values']))) {
                        echo "Selected";
                    } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>


        </div>


    </div>
</form>
<script>
    dashboard.init();
    modal_body.find('#dash-date-check, #dash-campaigns-check, #dash-outcomes-check, #dash-teams-check, #dash-branches-check, #dash-sources-check, #dash-pot-check, #dash-user-check').bootstrapToggle();

    $('#dash-date-check, #dash-campaigns-check, #dash-outcomes-check, #dash-teams-check, #dash-branches-check, #dash-sources-check, #dash-pot-check, #dash-user-check').on('change', function (e) {
        if ($(this).prop("checked")) {
            $(this).val("1");
        } else {
            $(this).val("0");
        }
    });

    var start = moment($('form').find('input[name="date_from"]').val(), "YYYY-MM-DD");
    var end = moment($('form').find('input[name="date_to"]').val(), "YYYY-MM-DD");

    $('.daterange').find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));


</script>