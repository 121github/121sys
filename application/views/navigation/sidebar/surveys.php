<?php if (isset($_SESSION['campaign_features']) && @in_array('Surveys', $_SESSION['campaign_features']) && isset($_SESSION['permissions']) && in_array("view surveys", $_SESSION['permissions']) || in_array("view surveys", $_SESSION['permissions']) && $_SESSION['data_access']['mix_campaigns']) { ?>
                        <li <?php if ($this->uri->segment(1) == "survey") {
                            echo "Selected";
                        } ?>><a href="<?php echo base_url(); ?>survey/view">View Surveys</a></li>
                    <?php } ?>