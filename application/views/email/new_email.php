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
		<form role="form" action="post">
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
					value="<?php echo !empty($template['template_from'])?$template['template_from']:$_SESSION['email']; ?>"				
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
					<span class="to-msg" style="color:red; font-style: italic; display: none;">You must enter a valid email, or comma separate multiple</span>
					<span class="glyphicon glyphicon-plus pointer pull-right add-contact" option='send_to' data-toggle="tooltip" data-html="true" data-placement="top"></span>
				</p>
				<input type="text" class="form-control" name="send_to"
					title="Enter the destination" required 
					value="<?php echo (!empty($template['template_to'])?$template['template_to']:$email_address); ?>"
				/>
			</div>
			<div class="form-group input-group-sm">		
				<p>
					<span class="glyphicon glyphicon-question-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Add more than one address separated by comas"></span>
					CC
					<span class="cc-msg" style="color:red; font-style: italic; display: none;">You must enter a valid email, or comma separate multiple</span>
					<span class="glyphicon glyphicon-plus pointer pull-right add-contact" option='cc' data-toggle="tooltip" data-html="true" data-placement="top"></span>
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
					<span class="glyphicon glyphicon-plus pointer pull-right add-contact" option='bcc' data-toggle="tooltip" data-html="true" data-placement="top"></span>
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
                                <script src="<?php echo base_url() ?>assets/js/plugins/tinymce/tinymce.min.js"></script>
            <script src="<?php echo base_url() ?>assets/js/plugins/tinymce/jquery.tinymce.min.js"></script>
                            <script type="text/javascript">
							$(document).ready(function(){
		       		         tinymce.init({
            selector: "#tinymce",
			theme: "modern",
			content_css: helper.baseUrl+"/assets/css/bootstrap.css",
			height : 500,
			paste_data_images: true,
			relative_urls : false,
			document_base_url : helper.baseUrl,
			remove_script_host:false,
			toolbar: "undo redo | styleselect | fontsizeselect fontselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table | jbimages",
			fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
			font_formats: "Andale Mono=andale mono,times;"+
        "Arial=arial,helvetica,sans-serif;"+
        "Arial Black=arial black,avant garde;"+
        "Book Antiqua=book antiqua,palatino;"+
        "Comic Sans MS=comic sans ms,sans-serif;"+
        "Courier New=courier new,courier;"+
        "Georgia=georgia,palatino;"+
        "Helvetica=helvetica;"+
        "Impact=impact,chicago;"+
        "Symbol=symbol;"+
        "Tahoma=tahoma,arial,helvetica,sans-serif;"+
        "Terminal=terminal,monaco;"+
        "Times New Roman=times new roman,times;"+
        "Trebuchet MS=trebuchet ms,geneva;"+
        "Verdana=verdana,geneva;"+
        "Webdings=webdings;"+
        "Wingdings=wingdings,zapf dingbats",
			plugins: [
         "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
         "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
         "save table contextmenu directionality emoticons template paste textcolor jbimages"
],
        });
		
							});
    </script>

			<div class="form-group input-group-sm">
                <textarea class="form-control" id="tinymce" name="template_body" title="Enter the template body" required ><?php echo $template['template_body']; ?></textarea>
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