  <?php if (in_array("view appointments", $_SESSION['permissions'])) { ?>
                        <li <?php echo @($page == 'appointments' ? "class=Selected'" : "") ?>><a
                                href="<?php echo base_url() ?>appointments">Appointments</a></li>
                    <?php } ?>