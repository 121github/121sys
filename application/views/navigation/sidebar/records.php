<?php if(@in_array("advanced search", $_SESSION['permissions'])||in_array("add records", $_SESSION['permissions'])||in_array("list records", $_SESSION['permissions'])){ ?> <li><a href="#records">Records</a>
                        <ul id="records">
                            <?php if (@in_array("advanced search", $_SESSION['permissions']) || isset($_SESSION['current_campaign']) && isset($_SESSION['advanced search'])) { ?>
                                <!--<li <?php if (@$page == "search") {
                                    echo "class='Selected'";
                                } ?>><a href="<?php echo base_url(); ?>search" class="hreflink">Search Records</a></li>-->
                            <?php } ?>
                                 <?php if (@in_array("list records", $_SESSION['permissions'])){ ?>
                            <li <?php if (@$page == "list_records") {
                                echo "class='Selected'";
                            } ?>><a href="<?php echo base_url(); ?>records/view">List Records</a></li>
                            <?php } ?>
                            <?php if (in_array("add records", $_SESSION['permissions'])) { ?>
                                <li <?php echo @($page == 'add_record' ? "class='Selected'" : "") ?>>
                                    <a href="<?php echo base_url() ?>data/add_record<?php echo isset($_SESSION['current_campaign']) ? "/" . $_SESSION['current_campaign'] : ""; ?>">Create
                                        Record</a></li>
                            <?php } ?>

                        </ul>
                    </li>
                    <?php } ?>