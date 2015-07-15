<div class="row">
            <div class="col-md-12 col-lg-12">
            <h3 id="panel-title">Showing all files...</h3>
        <div class="panel panel-primary" id="files-panel">
          <div class="panel-heading"> <i class="fa fa-files-o fa-fw"></i> Files 
            <div class="pull-right">
              <div class="btn-group">
               <button type="button" id="upload-btn" class="btn btn-default btn-xs disabled"> <span class="glyphicon glyphicon-file"></span> Upload</button>
              </div>
            <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"  id="folder-filter-text"> <span class="glyphicon glyphicon-filter"></span> All Folders</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                   <?php foreach($user_folders as $row):?>
	                    <li><a href="#" class="folder-filter" data-id="<?php echo $row['folder_id'] ?>" data-ref="folder_id"><?php echo $row['folder_name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="folder-filter" ref="#" data-ref="folder_id">All folders</a> </li>
	                  </ul>
                  </div>
            </div>
          
            </div>
              <div class="panel-body"> 
            <div id="dropzone-holder" style="display:none; position:relative">
            <span style="position:absolute; top:10px; right:10px; z-index:99999" class="close-upload glyphicon glyphicon-remove"></span>
              <form action="<?php echo base_url()."files/start_upload" ?>" class="dropzone" id="mydropzone">
<input type="hidden" id="dropzone-folder-id" name="folder" value="" />
<input type="hidden" id="dropzone-folder-name" name="folder_name" value="" />
</form>

</div>
          
              <div id="table-holder">
              <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
              </div>
            </div>
            
          </div>
           </div>
           </div>


<script>
$(document).ready(function(){
files.init();
});
</script>