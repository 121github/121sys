<?php if (@in_array("view dashboard", $_SESSION['permissions'])) { ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dashboards <span class="caret"></span></a>
          <ul class="dropdown-menu" id="dashboard-dropdown">
            <?php if ($_SESSION['sn'] == 'eldon.121system.com') { ?>
                <li <?php echo @($page == 'eldon_dash' ? "class='Selected'" : "") ?>><a
                        href="<?php echo base_url() ?>dashboard/eldon">Eldon Dash</a></li>
            <?php } ?>
            <?php if (in_array("29", $_SESSION['campaign_access']['array'])) { ?>
                <li <?php echo @($page == 'ghs_dash' ? "class='Selected'" : "") ?>><a
                        href="<?php echo base_url() ?>dashboard/ghs">GHS Dash</a></li>
            <?php } ?>
            <?php if ($_SESSION['sn'] == 'hsl.121system.com' || $_SESSION['sn'] == 'hcs.hslchairs.com') { ?>
                <li <?php echo @($page == 'hsl_dash' ? "class='Selected'" : "") ?>><a
                        href="<?php echo base_url() ?>dashboard/hsl">Hsl Dash</a></li>
            <?php } ?>
                <?php if (in_array("favourites dash", $_SESSION['permissions'])) { ?>
            <li <?php echo @($page == 'favorites_dash' ? "class='Selected'" : "") ?>><a
                    href="<?php echo base_url() ?>dashboard/favorites">Favourites</a></li>
                    <?php } ?>
              <?php if (in_array("overview dash", $_SESSION['permissions'])) { ?>
            <li <?php echo @($page == 'overview' ? "class='Selected'" : "") ?>><a
                    href="<?php echo base_url() ?>dashboard">Overview</a></li>
                    <?php } ?>
            <?php if (in_array("client dash", $_SESSION['permissions'])) { ?>
                <li <?php echo @($page == 'client_dash' ? "class='Selected'" : "") ?>><a
                        href="<?php echo base_url() ?>dashboard/client">Client Dashboard</a></li>
            <?php } ?>

            <?php if (in_array("nbf dash", $_SESSION['permissions'])) { ?>
                <li <?php echo @($page == 'nbf_dash' ? "class='Selected'" : "") ?>><a
                        href="<?php echo base_url() ?>dashboard/nbf">New Business</a></li>
            <?php } ?>
            <?php if (in_array("callback dash", $_SESSION['permissions'])||in_array("agent dash", $_SESSION['permissions'])) { ?>
                <li <?php echo @($page == 'callback_dash' ? "class='Selected'" : "") ?>><a
                        href="<?php echo base_url() ?>dashboard/callbacks">Callbacks</a></li>
            <?php } ?>
            <?php if (in_array("management dash", $_SESSION['permissions'])) { ?>
                <li <?php echo @($page == 'management_dash' ? "class='Selected'" : "") ?>><a
                        href="<?php echo base_url() ?>dashboard/management">Management Dash</a></li>
            <?php } ?>
                 <li role="separator" class="divider"></li>
        </ul>
    </li>
<?php } ?>

