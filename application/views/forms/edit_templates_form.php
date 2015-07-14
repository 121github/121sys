
<form style="display: none; padding: 10px 20px;" class="form-horizontal" >
	<input type="hidden" name="template_id">
    <div class="row">
    <div class="col-xs-8">
	<div class="form-group input-group-sm">
		<p>Please set the template name</p>
		<input type="text" class="form-control" name="template_name"
			title="Enter the template name" required />
	</div>
    </div>
    <div class="col-xs-4">
<div class="form-group pull-right">
 <label for="type">Include unsubscribe button</label>
 <br>
            <div class="btn-group" data-toggle="buttons">
  <label class="btn btn-info btn-sm">
    <input type="radio" name="template_unsubscribe" value="1" autocomplete="off" id="unsubscribe-yes">Yes
  </label>
  <label class="btn btn-info btn-sm renewal-label">
    <input type="radio" name="template_unsubscribe" value="0" autocomplete="off" id="unsubscribe-no">No
  </label>
</div>
</div>
    </div>
    </div>
	<div class="form-group input-group-sm">
		<p>Campaigns</p>	
		<select  name="campaign_id[]" class="selectpicker" id="campaigns_select" data-width="100%" data-size="5" multiple>
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
		<textarea class="form-control" id="tinymce" name="template_body"
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

        var name;
	
	    $('#fileupload').fileupload({
            // The regular expression for allowed file types, matches
            // against either file type or file name:
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|csv|pdf|docx?|txt|xml|eml|wav|mp3|ogg|mp4|avi|mpe?g|wmv|mov|xls|xlsx)$/i,
            // The maximum allowed file size in bytes:
            maxFileSize: 10000000, // 10 MB
            // The minimum allowed file size in bytes:
            minFileSize: undefined, // No minimal file size
            // The limit of files to be uploaded:
            maxNumberOfFiles: 1,
            dataType: 'json',
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
                name = file.name;

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
		    		template.attach_new_file(name, path);
		    	});
        }).on('fileuploadprocessalways', function (e, data) {
            var currentFile = data.files[data.index];

            if (data.files.error && currentFile.error) {
                // there was an error, do something about it
                flashalert.danger("ERROR: "+currentFile.error);
                $('#files').fadeIn();
                $('#files').find('#file-status').text(currentFile.error).removeClass('text-success').addClass('text-danger');
                $('#files').fadeIn(500).delay(250).fadeOut(500).fadeIn(500).delay(250).fadeOut(500).fadeIn(500).delay(250).fadeOut(500).fadeIn(500).fadeOut(500);
            }
		}).prop('disabled', !$.support.fileInput)
       	.parent().addClass($.support.fileInput ? undefined : 'disabled');
	});

</script>