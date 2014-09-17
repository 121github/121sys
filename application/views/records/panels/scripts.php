    <div class="panel panel-primary">
      <div class="panel-heading">Script Notes</div>
      <div class="panel-body">
        <?php
		  if(isset($details['scripts'])){
		  foreach($details['scripts'] as $id=>$data): 
		  if($data['expandable']): ?>
        <p><a href="#" class="view-script" script-id="<?php echo $data['script_id'] ?>"><?php echo $data['name'] ?></a></p>
        <?php else: ?>
        <p><?php echo $data['name'] ?></p>
        <?php 
		  endif;
		  endforeach;
		  } else {
			?>
        <p>There are no scripts configured for this campaign</p>
        <?php } ?>
      </div>
    </div>