
<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Store Categories</h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<div class="panel panel-primary" id="categories-panel">
  <div class="panel-heading">Categories
    <div class="pull-right">
      <div class="btn-group">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle add-btn" data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> Add Category</button>
      </div>
    </div>
  </div>
  <div class="panel-body">
    <div id="form-container">
      <?php $this->view('forms/edit_categories_form.php',$subcategories); ?>
    </div>
    <div id="table-container"> </div>
  </div>
</div>
<script>
$(document).ready(function(){
	admin.init();
	admin.categories.init();
});
</script> 
