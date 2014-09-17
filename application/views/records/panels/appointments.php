    <div class="panel panel-primary">
      <div class="panel-heading">Appointments<span class="glyphicon glyphicon-plus pull-right new-appointment"></span></div>
      <div class="panel-body appointment-panel"> 
      
              <?php $this->view('forms/edit_appointment_form.php',$users); ?>
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
    </div>