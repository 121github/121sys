			<div class="row">
				<div class="col-lg-12">
					<h1 class="page-header">Templates</h1>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->
			<div class="row">
				<div class="col-lg-12">

					<div class="panel panel-primary groups-panel">
						<div class="panel-heading">
							<i class="fa fa-envelope-o fa-fw"></i> Template list
							<div class="pull-right">
								<div class="btn-group">
									<button type="button"
										class="btn btn-default btn-xs dropdown-toggle add-btn"
										data-toggle="dropdown">
										<span class="glyphicon glyphicon-plus"></span> Add Template
									</button>
								</div>
							</div>
						</div>
						<!-- /.panel-heading -->
						<div class="panel-body template-data">
							 <?php $this->view('forms/edit_templates_form.php'); ?>
							 <table class="table ajax-table">
								<thead>
									<tr>
										<th>ID</th>
										<th>Name</th>
										<th>Options</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td colspan="3"><img
											src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
									</tr>
								</tbody>
							</table>
						</div>

					</div>

					<!-- /.row -->
				</div>
				<!-- /#page-wrapper -->


<script src="<?php echo base_url() ?>assets/js/summernote-customize.js"></script>
<script>
	$(document).ready(function(){
		$('.selectpicker').selectpicker();
		template.init()
	});
</script>