<div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
        <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?>
          <small>URN: <a href="<?php echo base_url()."records/detail/". $urn ?>"><?php echo $urn ?></a> <?php echo (!empty($campaign['campaign_name'])?" [". $campaign['campaign_name']."]":"") ?>
          / <?php echo $survey_name ?> / <?php echo $contact ?>
          </small>
        </p>
       </ul>
       <ul class="pull-right nav navbar-nav desktop-only">
       <li>
        <div class="navbar-btn form-actions">
         <button class="marl btn btn-default close-survey">Back</button>
         <?php if(!$locked){ ?>
         <button type="submit" class="marl btn btn-primary save-survey">Save</button>
         <button name="complete" value="complete" type="submit" class="btn marl btn-danger complete-survey">Complete</button>
         <?php } ?>
        </div>
        </li>
       </ul>
    </div>
