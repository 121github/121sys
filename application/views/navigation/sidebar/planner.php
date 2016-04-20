 <?php if (in_array("planner", $_SESSION['permissions'])) { ?>
                        <li <?php echo @($page == 'planner' ? "class=Selected'" : "") ?>><a
                                href="<?php echo base_url() ?>planner">Planner</a></li>
                    <?php } ?>