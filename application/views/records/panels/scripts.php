    <div id="script-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Script Notes</div>
      <div class="panel-body">
        <?php
		  if(isset($details['scripts'])){
		  foreach($details['scripts'] as $id=>$data): 
		  if($data['expandable']): ?>
        <p><a href="#" class="view-script" script-id="<?php echo $data['script_id'] ?>"><?php echo $data['name'] ?></a></p>
        <?php else: ?>
        <p><b><?php echo $data['name'] ?></b></p>
         <p><?php echo $data['script'] ?></p>
        <?php 
		  endif;
		  endforeach;
		  } else {
			?>
        <p>There are no scripts configured for this campaign</p>
        <?php } ?>
      </div>
    </div>