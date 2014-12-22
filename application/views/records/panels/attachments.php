 <div class="panel panel-primary attachment-panel">
      <div class="panel-heading">
        <h4 class="panel-title"> Attachments<?php if(in_array("add attachment",$_SESSION['permissions'])){ ?>
                <span class="glyphicon glyphicon-plus fileinput-button pull-right">
                    <!-- The file input field used as target for the file upload widget -->
                    <input id="fileupload" type="file" name="files"  data-url="<?php echo base_url()."records/upload_attach"; ?>">
                </span>
                <!-- The global progress bar -->
                <div id="progress-files" class="progress pull-right" style="display: none; width: 200px; margin-right: 10px;">
                    <div class="progress-bar progress-bar-success"></div>
                </div>
                <!-- The container for the uploaded files -->
                <div id="files" class="files pull-right" style="display: none; margin-right: 10px;">
                    <span id="file-status">sd</span>
                </div>
        <?php } ?></h4>
      </div>

      <div class="attachment-list panel-body">
       <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> 
      </div>





    </div>

 <script>

     $(function () {
         'use strict';

         var file;
         var name;
         var type;
         var path;

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
                 $('#progress-files').fadeIn();
                 var progress = parseInt(data.loaded / data.total * 100, 10);
                 $('#progress-files .progress-bar').css(
                     'width',
                     progress + '%'
                 );
                 $('#progress-files').fadeOut(4000);
             },
             always:function(e,data){
                 $('#files').fadeIn();
                 $('#files').find('#file-status').text("File uploaded").removeClass('text-danger').addClass('text-success');
                 $('#files').fadeOut(4000);
             }
         }).on('fileuploadadd', function (e, data) {
             file = data.files[0];
             name = file.name;
             type = file.type;
         }).on('fileuploaddone', function (e, data) {

             file = data.result.files[0];
             path = "";

             $.ajax({ url:helper.baseUrl+'records/get_attachment_file_path',
                 type:"POST",
                 dataType:"JSON",
                 data: { file: file.name }
             }).done(function(response){
                 path = response.path;
                 record.attachment_panel.save_attachment(name, type, path);
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