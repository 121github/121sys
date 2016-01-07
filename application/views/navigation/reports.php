<?php if (in_array("reports menu", $_SESSION['permissions'])) { ?>
    <li><a href="#reports">Reports</a>
        <ul id="reports">
            <?php if (in_array("survey answers", $_SESSION['permissions'])) { ?>
                <li <?php echo @($page == 'answers' ? "class='Selected'" : "") ?>>
                    <a href="<?php echo base_url() ?>reports/answers">Survey
                        Answers</a></li>  <?php } ?>
            <?php if (in_array("activity", $_SESSION['permissions'])) { ?>
                <li <?php echo @($page == 'activity' ? "class='Selected'" : "") ?>>
                    <a href="<?php echo base_url() ?>reports/activity">Activity</a>
                </li>
                 <li <?php echo @($page == 'activity' ? "class='Selected'" : "") ?>>
                    <a href="<?php echo base_url() ?>reports/overview">Activity Overview</a>
                </li>
            <?php } ?>
            <!--<li <?php echo @($page == 'realtime' ? "class='Selected'" : "") ?>>
                                        <a href="<?php echo base_url() ?>reports/realtime">Realtime</a>
                                    </li>-->
            <li>
                <a href="#reports-outcomes">Campaign Performance</a>
                <ul id="reports-outcomes">
                    <li <?php echo @($page == 'outcome_report_campaign' ? "class='Selected'" : "") ?>>
                        <a href="<?php echo base_url() ?>reports/outcomes/campaign/1">By
                            Campaign</a></li>
                    <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                        <li <?php echo @($page == 'outcome_report_agent' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/outcomes/agent/1">By
                                Agent</a></li>
                    <?php } ?>
                    <li <?php echo @($page == 'outcome_report_date' ? "class='Selected'" : "") ?>>
                        <a href="<?php echo base_url() ?>reports/outcomes/date/1">By
                            Date</a></li>
                    <li <?php echo @($page == 'outcome_report_time' ? "class='Selected'" : "") ?>>
                        <a href="<?php echo base_url() ?>reports/outcomes/time/1">By
                            Time</a></li>
                    <li <?php echo @($page == 'outcome_report_reason' ? "class='Selected'" : "") ?>>
                        <a href="<?php echo base_url() ?>reports/outcomes/outcome_reason/1">By
                            Outcome Reason</a></li>
                </ul>
            </li>
            <?php if (in_array("productivity", $_SESSION['permissions'])) { ?>
                <li <?php echo @($page == 'productivity' ? "class='Selected'" : "") ?>>
                    <a href="<?php echo base_url() ?>reports/productivity">
                        Productivity
                    </a>
                </li>
            <?php } ?>
            <?php if (in_array("client report", $_SESSION['permissions'])) { ?>
                <li>
                    <a href="#reports-client">Client</a>
                    <ul id="reports-client">
                        <?php if (in_array("data counts", $_SESSION['permissions'])) { ?>
                            <li <?php echo @($page == 'data' ? "class='Selected'" : "") ?>>
                                <a href="<?php echo base_url() ?>reports/data">Data Counts</a>
                            </li>
                        <?php } ?>
                        <li <?php echo @($page == 'client_report_outcomes' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/last_outcomes">Last Outcomes</a></li>
                        <li <?php echo @($page == 'client_report_dials' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/dials">Dials</a></li>
                    </ul>
                </li>
            <?php } ?>
            <?php if (in_array("email", $_SESSION['permissions'])) { ?>
                <li>

                    <a href="#reports-emails">Emails</a>
                    <ul id="reports-emails">
                        <li <?php echo @($page == 'email_report_campaign' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/email/campaign/1">By
                                Campaign</a></li>
                        <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                            <li <?php echo @($page == 'email_report_agent' ? "class='Selected'" : "") ?>>
                                <a href="<?php echo base_url() ?>reports/email/agent/1">By
                                    Agent</a></li>
                        <?php } ?>
                        <li <?php echo @($page == 'email_report_date' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/email/date/1">By
                                Date</a></li>
                        <li <?php echo @($page == 'email_report_time' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/email/time/1">By
                                Time</a></li>
                    </ul>
                </li>
            <?php } ?>
            <?php if (in_array("sms", $_SESSION['permissions'])) { ?>
                <li>

                    <a href="#reports-sms">Sms</a>
                    <ul id="reports-sms">
                        <li <?php echo @($page == 'sms_report_campaign' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/sms/campaign/1">By
                                Campaign</a></li>
                        <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                            <li <?php echo @($page == 'sms_report_agent' ? "class='Selected'" : "") ?>>
                                <a href="<?php echo base_url() ?>reports/sms/agent/1">By
                                    Agent</a></li>
                        <?php } ?>
                        <li <?php echo @($page == 'sms_report_date' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/sms/date/1">By
                                Date</a></li>
                        <li <?php echo @($page == 'sms_report_time' ? "class='Selected'" : "") ?>>
                            <a href="<?php echo base_url() ?>reports/sms/time/1">By
                                Time</a></li>
                    </ul>
                </li>
            <?php } ?>
            <li <?php echo @($page == 'audit' ? "class='Selected'" : "") ?>><a
                    href="<?php echo base_url() ?>audit">Data Capture Logs</a></li>
        </ul>
    </li>
<?php } ?>