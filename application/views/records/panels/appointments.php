    <div class="panel panel-primary">
      <div class="panel-heading">Appointments<?php if(in_array("add appointments",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-plus pull-right new-appointment"></span><?php } ?></div>
      <div class="panel-body appointment-panel"> 
      
              <?php $this->view('forms/edit_appointment_form.php',array("users"=>$users,"addresses"=>$addresses)); ?>
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
    </div>