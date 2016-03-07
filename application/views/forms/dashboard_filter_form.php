<div class="row">
    <form class="dashboard-filter-form" method="post">
        <input type="hidden" name="dashboard_id">
        <input type="hidden" name="date_from" value="<?php echo "2014-02-07" ?>">
        <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">

        <div class="col-lg-6">
            <button type="button" class="daterange btn btn-default" data-width="100%">
                <span class="glyphicon glyphicon-calendar"></span>
                <span class="date-text"> <?php echo "Any Time"; ?> </span>
            </button>
        </div>

        <div class="col-lg-6">
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
        </div>

        <div class="col-lg-6">
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
        </div>

        <div class="col-lg-6">
            <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Team</label>
                <select name="teams[]" class="selectpicker team-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($team_managers as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>

        <div class="col-lg-6">
            <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Agent</label>
                <select name="agents[]" class="selectpicker agent-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($agents as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>

        <div class="col-lg-6">
            <label style="margin-top: 5%;">Source</label>
            <select name="sources[]" class="selectpicker source-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($sources as $row) { ?>
                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-lg-6">
            <label style="margin-top: 5%;">Pot</label>
            <select name="pot[]" class="selectpicker pot-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($pots as $row) { ?>
                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-lg-6">
            <label style="margin-top: 5%;">User</label>
            <select name="user[]" class="selectpicker user-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($users as $type => $data) { ?>
                    <optgroup label="<?php echo $type ?>">
                        <?php foreach ($data as $row) { ?>
                            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
        </div>
    </form>
</div>