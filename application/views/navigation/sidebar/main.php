<nav id="menu" class="mm-menu mm--horizontal mm-offcanvas">
    <?php if (isset($_SESSION['permissions'])) { ?>
        <ul>
            <li><a class="mm-title">
                    <small><span class="text-primary"><?php echo date('l jS F') ?></span> -
                        Welcome <?php echo $_SESSION['name'] ?></small>
                </a></li>
            <?php if (isset($campaign_access) && count($_SESSION['campaign_access']['array']) > "2") { ?>
                <li style="padding:0;">
                    <select id="side-campaign-select" class="form-control">
                        <?php if ($_SESSION['data_access']['mix_campaigns'] || (!isset($_SESSION['current_campaign']) && !$_SESSION['data_access']['mix_campaigns'])) { ?>
                            <option
                                value=""><?php echo($_SESSION['data_access']['mix_campaigns'] ? "Campaign Filter" : "Select a campaign to begin"); ?></option>
                        <?php } ?>
                        <?php foreach ($campaign_access as $client => $camp_array) { ?>
                            <optgroup label="<?php echo $client ?>">
                                <?php foreach ($camp_array as $camp) { ?>
                                    <option <?php if (isset($_SESSION['current_campaign']) && $_SESSION['current_campaign'] == $camp['id']) {
                                        echo "Selected";
                                    } ?> value="<?php echo $camp['id'] ?>"><?php echo $camp['name'] ?></option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                    </select>
                </li>
            <?php } ?>
            <?php if (in_array("files only", $_SESSION['permissions'])) {
                $this->view('navigation/sidebar/files_only.php');
            } else if (in_array("survey only", $_SESSION['permissions'])) {
                $this->view('navigation/sidebar/survey_only.php');
            } else if (in_array("mix_campaigns", $_SESSION['data_access']) || isset($_SESSION['current_campaign'])) {
                ?>
                <?php
                //The system will give the agents the records that need dialing
                if (in_array("use callpot", $_SESSION['permissions'])) { ?>
                    <li><a href="<?php echo base_url(); ?>records/detail">Start Calling</a></li>
                <?php } ?>
                <?php if (!isset($page)) {
                    $page = "";
                }
				/* make the menu start on the first panel - ignore selected page BF march 2016*/
				$page = "";
				/* end */
                $this->view('navigation/sidebar/dashboards.php', $page);
                $this->view('navigation/sidebar/records.php', $page);
                $this->view('navigation/sidebar/files.php', $page);
                $this->view('navigation/sidebar/appointments.php', $page);
                $this->view('navigation/sidebar/planner.php', $page);
                $this->view('navigation/sidebar/surveys.php', $page);
                $this->view('navigation/sidebar/calendar.php', $page);
                $this->view('navigation/sidebar/admin.php', $page);
                $this->view('navigation/sidebar/reports.php', $page);
                $this->view('navigation/sidebar/search.php', $page);
            } else { ?>
                <li><a href="#" style="color:red">Please select a campaign to begin</a></li>
                <li>
                    <select id="side-campaign-select" class="selectpicker" data-width="100%">
                        <?php if ($_SESSION['data_access']['mix_campaigns'] || (!isset($_SESSION['current_campaign']) && !$_SESSION['data_access']['mix_campaigns'])) { ?>
                            <option
                                value=""><?php echo($_SESSION['data_access']['mix_campaigns'] ? "Campaign Filter" : "Select a campaign to begin"); ?></option>
                        <?php } ?>
                        <?php foreach ($campaign_access as $client => $camp_array) { ?>
                            <optgroup label="<?php echo $client ?>">
                                <?php foreach ($camp_array as $camp) { ?>
                                    <option <?php if (isset($_SESSION['current_campaign']) && $_SESSION['current_campaign'] == $camp['id']) {
                                        echo "Selected";
                                    } ?> value="<?php echo $camp['id'] ?>"><?php echo $camp['name'] ?></option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                    </select>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</nav>