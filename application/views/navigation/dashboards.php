 <li><a href="#mm-1">Dashboard</a>
                        <ul>
                            <?php if ($_SESSION['sn'] == 'eldon.121system.com') { ?>
                                <li <?php echo @($page == 'eldon_dash' ? "class=Selected'" : "") ?>><a
                                        href="<?php echo base_url() ?>dashboard/eldon">Eldon Dash</a></li>
                            <?php } ?>
                            <?php if (in_array("29", $_SESSION['campaign_access']['array'])) { ?>
                                <li <?php echo @($page == 'ghs_dash' ? "class=Selected'" : "") ?>><a
                                        href="<?php echo base_url() ?>dashboard/ghs">GHS Dash</a></li>
                            <?php } ?>
                            <li <?php echo @($page == 'favorites_dash' ? "class=Selected'" : "") ?>><a
                                    href="<?php echo base_url() ?>dashboard/favorites">Favorites</a></li>
                            <li <?php echo @($page == 'overview' ? "class=Selected'" : "") ?>><a
                                    href="<?php echo base_url() ?>dashboard/">Overview</a></li>
                            <?php if (in_array("client dash", $_SESSION['permissions'])) { ?>
                                <li <?php echo @($page == 'client_dash' ? "class=Selected'" : "") ?>><a
                                        href="<?php echo base_url() ?>dashboard/client">Client Dashboard</a></li>
                            <?php } ?>

                            <?php if (in_array("nbf dash", $_SESSION['permissions'])) { ?>
                                <li <?php echo @($page == 'nbf_dash' ? "class=Selected'" : "") ?>><a
                                        href="<?php echo base_url() ?>dashboard/nbf">New Business</a></li>
                            <?php } ?>
                            <?php if (in_array("agent dash", $_SESSION['permissions'])) { ?>
                                <li <?php echo @($page == 'callback_dash' ? "class=Selected'" : "") ?>><a
                                        href="<?php echo base_url() ?>dashboard/callbacks">Callbacks</a></li>
                            <?php } ?>
                            <?php if (in_array("management dash", $_SESSION['permissions'])) { ?>
                                <li <?php echo @($page == 'management_dash' ? "class=Selected'" : "") ?>><a
                                        href="<?php echo base_url() ?>dashboard/management">Management Dash</a></li>
                            <?php } ?>
                        </ul>
                    </li>