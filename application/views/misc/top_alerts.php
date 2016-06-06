<div id="top-alerts">
<?php if(isset($_SESSION['flashdata'])){  ?>
<?php if(isset($_SESSION['flashdata']['success'])){ ?>
<div class="alert alert-success alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-ok"></span> <?php echo $_SESSION['flashdata']['success']  ?> </div>
<?php  unset($_SESSION['flashdata']['success']); } ?>
<?php if(isset($_SESSION['flashdata']['danger'])){ ?>
<div class="alert alert-danger alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-alert"></span> <?php echo $_SESSION['flashdata']['danger'] ?> </div>
<?php  unset($_SESSION['flashdata']['danger']); } ?>
<?php if(isset($_SESSION['flashdata']['info'])){ ?>
<div class="alert alert-info alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-info-sign"></span> <?php echo $_SESSION['flashdata']['info'] ?> </div>
<?php  unset($_SESSION['flashdata']['info']); } ?>
<?php if(isset($_SESSION['flashdata']['warning'])){ ?>
<div class="alert alert-warning alert-dismissable" style="margin-top:10px">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <span class="glyphicon glyphicon-exclamation-sign"></span> <?php echo $_SESSION['flashdata']['warning'] ?> </div>
<?php  unset($_SESSION['flashdata']['warning']); } ?>
<?php } ?>
</div>