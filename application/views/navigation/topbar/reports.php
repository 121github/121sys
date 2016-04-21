<?php if (in_array("reports menu", $_SESSION['permissions'])) { ?>

<li class="dropdown <?php if ($this->uri->segment(1) == "reports") { echo "active"; } ?>"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
  <ul class="dropdown-menu" id="report-dropdown">
    <?php if (in_array("survey answers", $_SESSION['permissions'])) { ?>
    <li <?php echo @($page == 'answers' ? "class='Selected'" : "") ?>> <a href="<?php echo base_url() ?>reports/answers">Survey
      Answers</a></li>
    <?php } ?>
    <?php if (in_array("activity", $_SESSION['permissions'])) { ?>
    <li <?php echo @($page == 'activity' ? "class='Selected'" : "") ?>> <a href="<?php echo base_url() ?>reports/activity">Activity</a> </li>
    <li <?php echo @($page == 'activity' ? "class='Selected'" : "") ?>> <a href="<?php echo base_url() ?>reports/overview">Activity Overview</a> </li>
    <?php } ?>
    <li> <a href="<?php echo base_url() ?>reports/outcomes/campaign/1">Campaign Performance</a> </li>
    <?php if (in_array("productivity", $_SESSION['permissions'])) { ?>
    <li <?php echo @($page == 'productivity' ? "class='Selected'" : "") ?>> <a href="<?php echo base_url() ?>reports/productivity"> Productivity </a> </li>
    <?php } ?>
    <?php if (in_array("client report", $_SESSION['permissions'])) { ?>
    <li <?php echo @($page == 'client_report_outcomes' ? "class='Selected'" : "") ?>> <a href="<?php echo base_url() ?>reports/last_outcomes">Last Outcomes</a></li>
    <li <?php echo @($page == 'client_report_dials' ? "class='Selected'" : "") ?>> <a href="<?php echo base_url() ?>reports/dials">Dials</a></li>
    <?php } ?>
    <?php if (in_array("data counts", $_SESSION['permissions'])) { ?>
    <li <?php echo @($page == 'data' ? "class='Selected'" : "") ?>> <a href="<?php echo base_url() ?>reports/data">Data Counts</a> </li>
    <?php } ?>
    <?php if (in_array("email", $_SESSION['permissions'])) { ?>
    <li> <a href="<?php echo base_url() ?>reports/email/campaign/1">Emails</a> </li>
    <?php } ?>
    <?php if (in_array("sms", $_SESSION['permissions'])) { ?>
    <li> <a href="<?php echo base_url() ?>reports/sms/campaign/1>SMS</a> </li>
    <?php } ?>
    <li <?php echo @($page == 'audit' ? "class='Selected'" : "") ?>><a
                    href="<?php echo base_url() ?>audit">Data Capture Logs</a></li>
                      <li role="separator" class="divider"></li>
  </ul>
</li>
<?php } ?>
