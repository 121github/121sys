<?php if (in_array("files menu", $_SESSION['permissions'])) { ?>
                        <li <?php echo @($page == 'files' ? "class=Selected'" : "") ?>><a
                                href="<?php echo base_url() ?>files/manager">File Storage</a></li>
                    <?php } ?>