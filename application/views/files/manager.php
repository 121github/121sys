 <div class="row">
 <div class="col-md-12 col-lg-12">
<h4>Select the storage folder</h4>
<select id="folderpicker" class="selectpicker" name="folder" title="Select a folder..">
<option value="">Select a folder...</option>
<?php foreach($user_folders as $row){ print_r($row);?>
<option <?php if($folder==$row['folder_id']){ echo "selected"; } ?> value="<?php echo $row['folder_id'] ?>"><?php echo $row['folder_name'] ?></option>
<?php } ?>
</select>
<hr />
</div>
</div>
 <div class="row">
<?php if($write){ ?>
<div class="col-md-12 col-lg-6">
<h3>File Upload</h3>
<form action="<?php echo base_url()."files/start_upload" ?>" id="mydropzone" class="dropzone" >
<input type="hidden" name="folder" value="<?php if(!empty($folder)){ echo $folder; } ?>" />
<input type="hidden" name="folder_name" value="<?php if(!empty($folder)){ echo $folder_name; } ?>" />
</form>
</div>
<?php //end if folder is empty 
} ?>

<?php if($read||$write){ ?>
            <div class="col-md-12 <?php if($write){ echo "col-lg-6"; } else { echo "col-lg-12"; } ?>">
            <h3>Folder Contents [<?php echo $folder_name ?>]</h3>
        <div class="panel panel-primary" id="files-panel">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Files 
            <div class="pull-right"><div class="btn-group"><button style="display:none" type="button" id="showall-files" class="btn btn-default btn-xs">Show All</button></div></div>
          
            </div>
              <div class="panel-body"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
            </div>
            
          </div>
           </div>
<?php } ?>

<script>
$(document).ready(function(){
files.init('<?php echo $folder ?>','<?php echo $folder_name ?>',<?php echo $write?"1":'0'; ?>);

<?php if($write) { ?>
Dropzone.options.mydropzone = {
	maxFilesize: 100,
        accept: function(file, done) {
            if (<?php echo $check_string ?>) {
                done("Only <?php echo $filetypes ?> files can be added to the <?php echo $folder_name ?> folder");
            }
            else { done(); 	
			}
        },
		success: function(file, response){
			files.reload_folder(<?php echo $folder ?>)
		}
    }
<?php } ?>
	
});
</script>