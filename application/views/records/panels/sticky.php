 <?php if(isset($collapsable)){ ?>  
    
     <div id="sticky-panel" class="panel panel-primary">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#sticky-panel-slide" aria-expanded="true" aria-controls="sticky-panel-slide">Record Notes</div>
       <div id="sticky-panel-slide" class="panel-collapse collapse <?php if(!empty($details['record']['sticky_note'])){ echo "on"; } ?>" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
        <p>
          <textarea rows="<?php echo isset($stretch)?1:3 ?>" id="sticky-notes" class="form-control <?php if(isset($stretch)){ echo "stretch-element"; } ?>" placeholder="You can enter important notes here so they get seen. Eg. Do not call the customer before 3pm as they work night shifts!"><?php echo $details['record']['sticky_note'] ?></textarea>
        </p>
        <span class="alert-success hidden">Notes saved</span>
        <button class="btn btn-default pull-right" id="save-sticky">Save Notes</button>
      </div>
      </div>
    </div>
    
    <?php } else { ?>   <div id="sticky-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Record Notes</div>
      <div class="panel-body">
        <p>
          <textarea rows="<?php echo isset($stretch)?1:3 ?>" id="sticky-notes" class="form-control <?php if(isset($stretch)){ echo "stretch-element"; } ?>" placeholder="You can enter important notes here so they get seen. Eg. Do not call the customer before 3pm as they work night shifts!"><?php echo $details['record']['sticky_note'] ?></textarea>
        </p>
        <span class="alert-success hidden">Notes saved</span>
        <button class="btn btn-default pull-right" id="save-sticky">Save Notes</button>
      </div>
    </div>
    <?php } ?> 