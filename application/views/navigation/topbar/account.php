<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span
            class="fa fa-user"></span> Account</a>
    <ul class="dropdown-menu">
        <li><a href="<?php echo base_url() ?>user/account"><span class="fa fa-cog"></span> Preferences</a></li>
        <li role="separator" class="divider"></li>
        <li><a onclick="alert('121 Customer Insight. Version: <?php echo $this->config->item('project_version');?>')" href='#'><span class='fa fa-book'></span> About</a></li>
        <li><a data-modal='contact-us' href='#'><span class='fa fa-phone'></span> Contact</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="<?php echo base_url() ?>user/logout"><span class="fa fa-sign-out"></span> Logout</a></li>
    </ul>
</li>
       