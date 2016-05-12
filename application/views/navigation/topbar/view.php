  <?php if (@in_array("files menu", $_SESSION['permissions'])||@in_array("list records", $_SESSION['permissions'])||in_array("view appointments", $_SESSION['permissions'])) { ?>
  <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">View <span class="caret"></span></a>
          <ul class="dropdown-menu">
  
            <?php if (in_array("view appointments", $_SESSION['permissions'])) { ?>
                          <?php if (@in_array("list records", $_SESSION['permissions'])){ ?>
            		  <li><a href="<?php echo base_url() ?>records/view">Records</a></li>
                      <?php } ?>
                      <li><a href="<?php echo base_url() ?>appointments">Appointments</a></li>
                     <?php } ?>
                     	<?php if (@in_array('full calendar', $_SESSION['permissions']) && @in_array('view appointments', $_SESSION['permissions'])) { ?>
           <li><a href="<?php echo base_url(); ?>booking/week">Calendar</a></li>
          <?php } ?>
                      <?php if (in_array("files menu", $_SESSION['permissions'])) { ?>
                      <li><a href="<?php echo base_url() ?>files/manager">Files</a></li>
                      <?php } ?>
                       <?php if (in_array("planner", $_SESSION['permissions'])) { ?>
                      <li><a href="<?php echo base_url() ?>planner">Planner</a></li>
                      <?php } ?>
        
<?php if (isset($_SESSION['campaign_features']) && @in_array('Surveys', $_SESSION['campaign_features']) && isset($_SESSION['permissions']) && in_array("view surveys", $_SESSION['permissions']) || in_array("view surveys", $_SESSION['permissions']) && in_array("mix campaigns", $_SESSION['permissions'])) { ?><li><a href="<?php echo base_url() ?>survey/view">Surveys</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>