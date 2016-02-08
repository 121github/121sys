  
    <div class="panel panel-primary">
        <div class="panel-heading">
            <i class="fa fa-calendar-o fa-fw"></i> Calendar
            <div class="pull-right">
           <a class="btn btn-xs btn-default" href="<?php echo base_url().'google/logout' ?>">Logout</a>
           </div>
            </div>
            <div class="panel-body" style="min-height:700px">
            <div id="login-prompt"<?php if($google){ echo 'style="display:none"'; } ?> >
<h3>Please sign in</h3>
<p>You must be logged into your google account to access the google calendar</p>            
         <a class="btn btn-info" href="<?php echo base_url() ?>google/authenticate">Sign in</a>


            </div>
            <?php if($google){ ?>
            <iframe src="https://calendar.google.com/calendar/embed?src=<?php echo $google ?>&ctz=Europe/London" style="border: 0; width:100%; min-height:700px; height:100%; <?php if(!$google){ echo 'display:none'; } ?>" id="cal-iframe" frameborder="0" scrolling="no"></iframe>
            <?php } ?>
            </div>
            </div>


  