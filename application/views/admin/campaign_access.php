 <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Campaign Admin</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      
        <div class="panel panel-primary campaign-access">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Campaign User Access           
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
<table class="table">
<tr>
<td colspan="4">
<select class="selectpicker campaign-select">
<option value="">Select a campaign</option>
<?php foreach($options['campaigns'] as $row){ ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
</select>
</td>
</tr>
<tr>
<td>
Group Filter</td>
<td>
Users</td><td></td><td>Campaign Access</td></tr>
<tr>
<td><select disabled style="height:200px;" class="form-control group-select" size="20">
<?php foreach($options['groups'] as $row){ ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
</select></td>
<td><select disabled multiple style="height:200px" class="form-control user-select"><option value="">Select a group first</option></select></td>
<td width="110">
<div class="btn-group-vertical">
<button  type="button" disabled class="btn btn-default access-add">Add <span class="glyphicon glyphicon-chevron-right"></span></button><br>
<button  type="button" disabled class="btn btn-default access-del"><span class="glyphicon glyphicon-chevron-left"></span> Remove </button>
</div>
</td>
<td><select disabled style="height:200px" multiple class="form-control access-select">
<?php foreach($options['access'] as $row){ ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php } ?>
</select></td>
</tr>
</table>

</div>
</div>