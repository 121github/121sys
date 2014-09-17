
<form style="display: none; padding: 10px 20px;" class="form-horizontal">
	<input type="hidden" name="template_id">
	<div class="form-group input-group-sm">
		<p>Please set the template name</p>
		<input type="text" class="form-control" name="template_name"
			title="Enter the template name" required />
	</div>
	<div class="form-group input-group-sm">
		<p>Campaigns</p>	
		<select  name="campaign_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
           <?php foreach($campaigns as $row): ?>
        	   	<option value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
           <?php endforeach; ?>
		</select>
	</div>
	<div class="form-group input-group-sm">		
		<p>From</p>
		<input type="text" class="form-control" name="template_from"
			title="Enter the sender" required />
	</div>
	<div class="form-group input-group-sm">	
		<p>To</p>
		<input type="text" class="form-control" name="template_to"
			title="Enter the destination" required />
	</div>
	<div class="form-group input-group-sm">		
		<p>CC</p>
		<input type="text" class="form-control" name="template_cc"
			title="Enter the CC" required />
	</div>
	<div class="form-group input-group-sm">	
		<p>BCC</p>
		<input type="text" class="form-control" name="template_bcc"
			title="Enter the BCC" required />
	</div>
	<div class="form-group input-group-sm">	
		<p>Subject</p>
		<input type="text" class="form-control" name="template_subject"
			title="Enter the template subject" required />
	</div>
	<div class="form-group input-group-sm">	
		<p>Body</p>
		<textarea class="form-control" id="summernote" name="template_body"
			title="Enter the template body" required ></textarea>
	</div>
	<div class="form-group input-group-sm">  
		<!-- ATTACHMENTS -->
		<input type="text" class="form-control" name="template_attachments" style="display: none"/>
		 <div class="form-actions pull-left">
			<span class="btn btn-default fileinput-button">
		        <i class="glyphicon glyphicon-plus"></i>
		        <span>Attach file...</span>
		        <!-- The file input field used as target for the file upload widget -->
		        <input id="fileupload" type="file" name="files[]"  data-url="<?php echo base_url()."templates/upload_template_attach"; ?>">
		    </span>
		    
		    <br /><br />
		    <div style="display:none" id="attachments">
				<table class="table attach_table">
					<thead>
					</thead>
					<tbody>
					</tbody>
				</table>
				
				<table class="table new_attach_table">
					<thead>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>	
		<div class="form-actions" id="upload-status" style="display: none;">
			<!-- The global progress bar -->
		    <div id="progress" class="progress">
		        <div class="progress-bar progress-bar-success"></div>
		    </div>
		    <div class="form-actions pull-right">
			    <!-- The container for the uploaded files -->
			    <div id="files" class="files pull-left">
				    <span id="filename"></span><br>
				    <span id="file-status"></span>
			    </div>
			</div>
		 </div>
		 		
	</div>
	<!-- SUBMIT AND CANCEL BUTTONS -->
    <div class="form-actions pull-right">
		<button class="marl btn btn-default close-btn">Cancel</button>
		<button type="submit" class="marl btn btn-primary save-btn">Save</button>
	</div>
</form>


<script>

	$(function () {
	    'use strict';
	
	    $('#fileupload').fileupload({
			maxNumberOfFiles: 2,
	        dataType: 'json',
			acceptFileTypes: /(\.|\/)(jpg)$/i,
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .progress-bar').css(
	                'width',
	                progress + '%'
	            );
	        },
			always:function(e,data){
				$('#files').find('#file-status').text("File uploaded").removeClass('text-danger').addClass('text-success');
			}
	    }).on('fileuploadadd', function (e, data) {
	    		var file = data.files[0];
	       		$('#files').find('#filename').text(file.name);
			    $('#files').find('#file-status').text('');
			    
	    }).on('fileuploaddone', function (e, data) {

	    	var file = data.result.files[0];
       		var path = "";
       		
	    	$.ajax({ url:helper.baseUrl+'templates/get_attachment_file_path',
		    	type:"POST",
		    	dataType:"JSON",
		    	data: { file: file.name }
		    	}).done(function(response){
		    		path = response.path;
		    		template.attach_new_file(file.name, path);
		    	});
        }).on('fileuploadprocessalways', function (e, data) {
            var file = data.files[0];
        	if (file.error) {
            	$('#files').find('#file-status').text(file.error).removeClass('text-success').addClass('text-danger');
        	}
		}).prop('disabled', !$.support.fileInput)
       	.parent().addClass($.support.fileInput ? undefined : 'disabled');
	});

</script>