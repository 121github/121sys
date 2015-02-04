<div class="page-header">
	<h2>
		New Email <small>URN: <?php echo $urn ?></small>
	</h2>
</div>

<div class="panel panel-primary contact-panel">
	<!-- Default panel contents -->
	<div class="panel-heading">
		<h4 class="panel-title">
			Email
			<span class="glyphicon glyphicon-question-sign pull-right tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Please complete all the fields. When you are finished click the send button below"></span>
		</h4>
	</div>
	<div class="panel-body">
		<form role="form" role="form">
			<input type="hidden" name="urn" value="<?php echo $urn ?>" />
			<input type="hidden" name="template_id" value="<?php echo $template_id ?>" />
			<input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id'] ?>" />
			
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
			<div class="form-group input-group-sm">	
				<p>
					<span class="glyphicon glyphicon-question-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Add more than one address separated by comas"></span>
					To
					<span class="to-msg" style="color:red; font-style: italic; display: none;">You must enter a valid email, or comma separate multiple</span>
					<span class="glyphicon glyphicon-plus pull-right add-contact" option='send_to' data-toggle="tooltip" data-html="true" data-placement="top"></span>
				</p>
				<input type="text" class="form-control" name="send_to"
					title="Enter the destination" required 
					value="<?php echo $template['template_to']; ?>"
				/>
			</div>
			<div class="form-group input-group-sm">		
				<p>
					<span class="glyphicon glyphicon-question-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Add more than one address separated by comas"></span>
					CC
					<span class="cc-msg" style="color:red; font-style: italic; display: none;">You must enter a valid email, or comma separate multiple</span>
					<span class="glyphicon glyphicon-plus pull-right add-contact" option='cc' data-toggle="tooltip" data-html="true" data-placement="top"></span>
				</p>
				<input type="text" class="form-control" name="cc"
					title="Enter the CC" required 
					value="<?php echo $template['template_cc']; ?>"
				/>
			</div>
			<div class="form-group input-group-sm">	
				<p>
					<span class="glyphicon glyphicon-question-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Add more than one address separated by comas"></span>
					BCC
					<span class="bcc-msg" style="color:red; font-style: italic; display: none;">You must enter a valid email, or comma separate multiple</span>
					<span class="glyphicon glyphicon-plus pull-right add-contact" option='bcc' data-toggle="tooltip" data-html="true" data-placement="top"></span>
				</p>
				<input type="text" class="form-control" name="bcc"
					title="Enter the BCC" required
					value="<?php echo $template['template_bcc']; ?>" 
				/>
			</div>
			<div class="form-group input-group-sm">	
				<p>Subject</p>
				<input type="text" class="form-control" name="subject"
					title="Enter the template subject" required 
					value="<?php echo $template['template_subject']; ?>"
				/>
			</div>
			<div class="form-group input-group-sm">
                <textarea class="form-control" id="summernote" name="body"
                          title="Enter the template body" required
                          ><?php echo $template['template_body']; ?>
                </textarea>
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
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|csv|pdf|docx?|txt|xml|eml|wav|mp3|ogg|mp4|avi|mpe?g|wmv|mov)$/i,
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