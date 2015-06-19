<div class="page-header">
	<h2>
		New SMS <small>URN: <?php echo $urn ?></small>
	</h2>
</div>

<div class="panel panel-primary contact-panel">
	<!-- Default panel contents -->
	<div class="panel-heading">
		<h4 class="panel-title">
			SMS
			<span class="glyphicon glyphicon-question-sign pull-right tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Please complete all the fields. When you are finished click the send button below"></span>
		</h4>
	</div>
    
	<div class="panel-body">
		<form role="form">
			<input type="hidden" name="urn" value="<?php echo $urn ?>" />
			<input type="hidden" name="template_id" value="<?php echo $template_id ?>" />
			<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id'] ?>" />
			<div class="row">
            
            <div class="col-xs-8 col-sm-8">
            
			<div class="form-group input-group-sm">		
				<p>
					<span class="glyphicon glyphicon-question-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Add more than one address separated by comas"></span>
					From
				</p>
				<input type="text" class="form-control" name="send_from"
					title="Enter the sender" required 
					value="<?php echo $template['template_from']; ?>"				
				/>
			</div>
            </div>
             <div class="col-xs-4 col-sm-4">
<div class="form-group pull-right">
 <label for="type">Include unsubscribe button</label>
 <br>
            <div class="btn-group" data-toggle="buttons">
  <label class="btn btn-info btn-sm <?php if(@$template['template_unsubscribe']=="1"){ echo "active"; } ?>">
    <input type="radio" name="template_unsubscribe" value="1" autocomplete="off" id="unsubscribe-yes" <?php if(@$template['template_unsubscribe']=="1"){ echo "checked"; } ?> >Yes
  </label>
  <label class="btn btn-info btn-sm <?php if(@$template['template_unsubscribe']=="0"){ echo "active"; } ?>">
    <input type="radio" name="template_unsubscribe" value="0" autocomplete="off" id="unsubscribe-no" <?php if(@$template['template_unsubscribe']=="0"){ echo "checked"; } ?>>No
  </label>
</div>
</div>
    </div>
    </div>
			<div class="form-group input-group-sm">	
				<p>
					<span class="glyphicon glyphicon-question-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Add more than one address separated by comas"></span>
					To
					<span class="to-msg" style="color:red; font-style: italic; display: none;">You must enter a valid number, or comma separate multiple</span>
					<span class="glyphicon glyphicon-plus pull-right add-contact" option='send_to' data-toggle="tooltip" data-html="true" data-placement="top"></span>
				</p>
				<input type="text" class="form-control" name="send_to"
					title="Enter the destination" required 
					value="<?php echo $template['template_to']; ?>"
				/>
			</div>
			<div class="form-group input-group-sm">
                <textarea class="form-control" id="summernote" name="body"
                          title="Enter the template body" required
                          ><?php echo $template['template_body']; ?>
                </textarea>
            </div>

			<div class="form-actions pull-right">
				<button class="marl btn btn-default close-email">Back</button>
				<button type="submit" class="marl btn btn-primary send-email">Send</button>
			</div>
  
  </form>

	</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {   
        var urn = '<?php echo $urn ?>';
		email.init(urn);
    });
</script>
<script>

	$(function () {
	    'use strict';

        var name;

	    $('#fileupload').fileupload({
            // The regular expression for allowed file types, matches
            // against either file type or file name:
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|csv|pdf|docx?|txt|xml|eml|wav|mp3|ogg|mp4|avi|mpe?g|wmv|mov|xlsx|xls)$/i,
            // The maximum allowed file size in bytes:
            maxFileSize: 10000000, // 10 MB
            // The minimum allowed file size in bytes:
            minFileSize: undefined, // No minimal file size
            // The limit of files to be uploaded:
            maxNumberOfFiles: 1,
            dataType: 'json',
	        progressall: function (e, data) {
                flashalert.success("File attached");
	        },
			always:function(e,data){
                //flashalert.success("File uploaded");
			}
	    }).on('fileuploadadd', function (e, data) {
	    		var file = data.files[0];
                name = file.name;

	    }).on('fileuploaddone', function (e, data) {

	    	var file = data.result.files[0];
       		var path = "";

	    	$.ajax({ url:helper.baseUrl+'templates/get_attachment_file_path',
		    	type:"POST",
		    	dataType:"JSON",
		    	data: { file: file.name }
		    	}).done(function(response){
		    		path = response.path;
		    		email.attach_new_file(name, path);
		    	});
        }).on('fileuploadprocessalways', function (e, data) {
            var currentFile = data.files[data.index];

            if (data.files.error && currentFile.error) {
                // there was an error, do something about it
                flashalert.danger("ERROR: "+currentFile.error);
            }
		}).prop('disabled', !$.support.fileInput)
       	.parent().addClass($.support.fileInput ? undefined : 'disabled');
	});

</script>
<script src="<?php echo base_url() ?>assets/js/summernote-customize.js"></script>