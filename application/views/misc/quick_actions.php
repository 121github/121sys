   <div id="quick-actions">
                 <?php if (in_array("use callpot", $_SESSION['permissions'])) { ?>
                        <a type="button" <?php if (!isset($_SESSION['current_campaign'])) {
                            echo "disabled";
                        } ?> class="btn btn-default" href="records/detail" style="margin:0 3px 10px"><p>Start Calling</p>
                            <span class="fa fa-phone fa-3x"></span></a>
                        <script>$('[data-toggle="tooltip"]').tooltip();

                        </script>
                    <?php } ?>
                    <?php if (in_array("add records", $_SESSION['permissions']) && isset($_SESSION['current_campaign'])) { ?>
                        <a type="button" class="btn btn-default"
                           href="data/add_record<?php echo isset($_SESSION['current_campaign']) ? "/" . $_SESSION['current_campaign'] : "" ?>"
                           style="margin:0 3px 10px"><p>Create Record</p><span class="fa fa-plus fa-3x"></span></a>
                    <?php } ?>
                    <?php if (in_array("list records", $_SESSION['permissions'])) { ?>
                        <a type="button" class="btn btn-default" href="records/view" style="margin:0 3px 10px"><p>List
                                Records</p><span class="fa fa-table fa-3x"></span></a>
                    <?php } ?>
                    <?php if (in_array("view appointments", $_SESSION['permissions'])) { ?>
                        <a type="button" class="btn btn-default" href="appointments" style="margin:0 3px 10px"><p>Appointments</p><span class="fa fa-clock-o fa-3x"></span></a>
                    <?php } ?>
                    <?php if (in_array("full calendar", $_SESSION['permissions'])) { ?>
                        <a type="button" class="btn btn-default" href="booking" style="margin:0 3px 10px"><p>View
                                Calendar</p><span class="fa fa-calendar fa-3x"></span></a>
                    <?php } ?>
                    <?php if (in_array("view surveys", $_SESSION['permissions'])) { ?>
                        <a type="button" class="btn btn-default" href="survey/view" style="margin:0 3px 10px"><p>View
                                Surveys</p><span class="fa fa-clipboard fa-3x"></span></a>
                    <?php } ?>
                    <?php if (in_array("search records", $_SESSION['permissions'])) { ?>
                        <a type="button" class="btn btn-default" href="search" style="margin:0 3px 10px"><p>Search Records</p><span class="fa fa-search fa-3x"></span></a>
                    <?php } ?>
                    <?php if (in_array("search recordings", $_SESSION['permissions'])) { ?>
                        <a type="button" class="btn btn-default" href="search" style="margin:0 3px 10px"><p>Recordings</p><span class="fa fa-headphones fa-3x"></span></a>
                    <?php } ?>
    </div>