 <div class="panel panel-primary attachment-panel">
      <div class="panel-heading">
        <h4 class="panel-title"> Attachments<?php if(in_array("add attachment",$_SESSION['permissions'])){ ?>
                <span class="glyphicon glyphicon-plus fileinput-button pull-right">
                    <!-- The file input field used as target for the file upload widget -->
                    <input id="fileupload" type="file" name="files"  data-url="<?php echo base_url()."records/upload_attach"; ?>">
                </span>
                <div id="upload-status" style="display: none;">
                    <!-- The global progress bar -->
                    <div id="progress" class="progress">
                        <div class="progress-bar progress-bar-success"></div>
                    </div>
                </div>
            <?php } ?></h4>
      </div>

      <div class="attachment-list panel-body">
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
             maxNumberOfFiles: 1,
             dataType: 'json',
             //acceptFileTypes: /(\.|\/)(jpg)$/i,
             progressall: function (e, data) {
                 $('#upload-status').fadeIn();
                 var progress = parseInt(data.loaded / data.total * 100, 10);
                 $('#progress .progress-bar').css(
                     'width',
                     progress + '%'
                 );
                 $('#upload-status').fadeOut(4000);
             },
             always:function(e,data){
                 $('#files').find('#file-status').text("File uploaded").removeClass('text-danger').addClass('text-success');
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
             var file = data.files[0];
             if (file.error) {
                 $('#files').find('#file-status').text(file.error).removeClass('text-success').addClass('text-danger');
             }
         }).prop('disabled', !$.support.fileInput)
             .parent().addClass($.support.fileInput ? undefined : 'disabled');
     });

 </script>