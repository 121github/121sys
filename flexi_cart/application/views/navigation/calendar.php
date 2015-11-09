<?php if (@in_array('full calendar', $_SESSION['permissions']) && in_array("mix campaigns", $_SESSION['permissions']) || @in_array('Appointment Setting', $_SESSION['campaign_features'])) { ?>
                        <li <?php if ($this->uri->segment(1) == "calendar") {
                            echo "class='Selected'";
                        } ?>><a href="<?php echo base_url(); ?>calendar">Calendar</a></li>
                    <?php } ?>