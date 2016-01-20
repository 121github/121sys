    <div id="ownership-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Ownership <?php if(in_array("change ownership",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-pencil pointer pull-right edit-owner"></span><?php } ?></div>
      <div class="panel-body">
        <?php $this->view('forms/edit_ownership_form.php',$users); ?>
        <div class="panel-content"> 
          <!-- This panel now loads via ajax --> 
        </div>
      </div>
    </div>