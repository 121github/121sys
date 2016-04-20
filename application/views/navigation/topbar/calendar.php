<?php if (@in_array('full calendar', $_SESSION['permissions']) && @in_array('view appointments', $_SESSION['permissions'])) { ?>
                       
   <li class="dropdown <?php if ($this->uri->segment(1) == "calendar") { echo "active"; } ?>">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Calendar <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo base_url(); ?>booking/day">Today</a></li>
            <li><a href="<?php echo base_url(); ?>booking/week">This Week</a></li>
            <li><a href="<?php echo base_url(); ?>booking/month">This Month</a></li>
          </ul>
        </li>
                    <?php } ?>
					
                  