<?php if (@in_array('full calendar', $_SESSION['permissions']) && @in_array('view appointments', $_SESSION['permissions'])) { ?>
                        <li <?php if ($this->uri->segment(1) == "calendar") {
                            echo "class='Selected'";
                        } ?>><a href="<?php echo base_url(); ?>calendar">Calendar</a></li>
                    <?php } ?>