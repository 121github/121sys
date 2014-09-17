    <div class="panel panel-primary ownership-panel">
      <div class="panel-heading">Ownership <span class="glyphicon glyphicon-pencil pull-right edit-owner"></span></div>
      <div class="panel-body">
        <?php $this->view('forms/edit_ownership_form.php',$users); ?>
        <div class="panel-content"> 
          <!-- This panel now loads via ajax --> 
        </div>
      </div>
    </div>