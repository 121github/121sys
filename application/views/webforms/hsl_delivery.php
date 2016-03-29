<?php
$show_footer = false;
if (isset($_SESSION['current_campaign']) && in_array("show footer", $_SESSION['permissions'])) {
    $show_footer = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title>HSL Delivery Notes</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/bootstrap-theme.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/dataTables.bootstrap.css">
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slider.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/jqfileupload/jquery.fileupload.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/packages/fancybox/jquery.fancybox.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/packages/fancybox/helpers/jquery.fancybox-thumbs.css"> 
    <!-- Set the baseUrl in the JavaScript helper -->
    <?php //load specific javascript files set in the controller
    if (isset($css)):
        foreach ($css as $file): ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/<?php echo $file ?>">
        <?php endforeach;
    endif; ?>
    <link rel="shortcut icon"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/icon.png">
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/wavsurfer.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.numeric.min.js"></script>

    <!--Need to make a new icon for this
          <link rel="apple-touch-icon" href="http://www.121system.com/assets/img/apple-touch-icon.png" />-->
    <style>
        .tooltip-inner {
            max-width: 450px;
            /* If max-width does not work, try using width instead */
            width: 450px;
        }
		select,input,textarea { display:none }
		#logo-img { display:none }
		@media print {
			.collapse {
    display: block !important;
    height: auto !important;
}
th{ width:140px; }
.print-hide { display:none !important }
#images-panel { display:none !important }
body { font-size:12px !important  }
.col-md-6 { width:50% !important; float:left !important; }
#logo-img { display:inline !important; position:absolute !important; top:0px; right:20px;  }
		}

    </style>
</head>
<body>
<div class="container">
    <h3>HSL Delivery<small></small><img id="logo-img" class="pull-right" width="60px" src="<?php echo base_url(); ?>assets/themes/images/hsl/hsl_stacked_logo.png"></h3>

    <form id="form" style="padding-bottom:50px;">
    <input type="hidden" id="webform-id" name="id" value="<?php echo @$values['id'] ?>" />
    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id'] ?>" />
    
    
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
  <div class="panel panel-default">
    <div class="panel-heading print-hide">
      <h4 class="panel-title pointer" role="tab" id="headingOne" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Customer Details
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body">
      
            <div class="row">
      <div class="col-md-6">
      <h4>Appointment Info</h4>
<table class="table"><tr><th>Customer</th><td><?php echo $appointment['fullname'] ?></td></tr>
       <tr><th>Address</th><td><?php echo str_replace(",","<br>",$appointment['address']) ?></td></tr>
          <tr><th>Telephone(s)</th><td><?php echo $appointment['all_numbers'] ?></td></tr>
               <tr><th>Delivery Date</th><td><?php echo date("l jS F Y (a)",strtotime($appointment['sql_start'])) ?></td></tr>
               <tr><th>Delivery Type</th><td><?php echo $appointment['appointment_type']; ?></td></tr>
                  <tr><th>Delivery Notes</th><td><?php echo $appointment['text'] ?></td></tr>
       </table>
       </div>
        <div class="col-md-6">
        <h4>Delivery Info</h4>
   <table class="table">
   <tr><th>HSL Ref</th><td><?php echo $custom['c1'] ?></td></tr>
   <!--<tr><th>Value (&pound;)</th><td><?php echo $custom['c2'] ?></td></tr>-->
   <?php $show_fields = array("Order number","Delivery Items","Delivery Comments","Delivery Product Type"); ?>
   <?php foreach($custom_panels as $row){ ?>
   <?php if(in_array($row['name'],$show_fields)){ ?>
   <tr><th><?php echo $row['name'] ?></th><td><?php echo $row['value'] ?></td></tr>
   <?php } ?>
   <?php } ?>
       </table>
       </div>
       </div>
      
       
      </div>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading " >
      <h4 class="panel-title pointer" role="tab" id="headingThree" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
      To be completed on delivery
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
      <div class="panel-body">
      <div class="row">
     <div class="col-md-12">
     <div class="form-group">
     <label>Assembly Completed</label> Yes &#9744; No &#9744; N/A &#9744;
     </div>
      <div class="form-group">
    <label>Demonstration Completed</label> Yes &#9744; No &#9744; N/A &#9744;
    </div>
     <div class="form-group">
     <label>Quality Check</label> Pass &#9744; Fail &#9744; N/A &#9744;</div>
      <div class="form-group">
      <label>Reason for fail (if applicable)</label><br><br> _____________________________<br></div>
       <div class="form-group">
      <p><label>Customer Signature</label><br><br> _____________________________</p>
       <p> <label>Customer Print</label><br><br> _____________________________</p>
       </div>
     </div>
       </div>
       </div>
      </div>
    </div>
  </div>

    
    

</form>
  
  </div>
   
          </div>
          
             <div class="navbar navbar-default navbar-fixed-bottom" style="z-index:1">
             <div style="padding:8px 50px">
       <a href="<?php echo base_url() . 'records/detail/' . $this->uri->segment(4); ?>" class="btn btn-default  navbar-btn">Go
            back</a>
            <button class="btn btn-primary  navbar-btn" onclick="window.print()" id="print-report">Print</button>
      <!-- <button class="btn btn-primary  navbar-btn" id="edit-report">Edit</button>
       <button class="btn btn-primary  navbar-btn" id="save-form" style="display:none">Save</button>
       <button class="btn btn-success  navbar-btn" id="complete-form">Set Complete</button>-->
       
       </div>
       
       
       <div id="page-success" class="alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-info"  class=" alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-warning"  class=" alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-danger"  class=" alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
    </div>
    
    <script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-slider.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/browser/jquery.browser.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/modals.js"></script>
<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/jqfileupload/vendor/jquery.ui.widget.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/jqfileupload/jquery.iframe-transport.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/jqfileupload/jquery.fileupload.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/jqfileupload/jquery.fileupload-process.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/jqfileupload/jquery.fileupload-validate.js"></script>
<script src="<?php echo base_url(); ?>assets/packages/fancybox/jquery.fancybox.js"></script>
<script src="<?php echo base_url(); ?>assets/packages/fancybox/helpers/jquery.fancybox-thumbs.js"></script>
    <script type="text/javascript">
	helper.baseUrl = '<?php echo base_url(); ?>' + '';
	</script>

          
          <script>
		
     $(function () {
		uploads.init(); 
		<?php if(empty($values['completed_on'])){ ?>
			  $('#edit-report').trigger('click');
			  <?php } else { ?>
			  $('.val-text').each(function(){
					  $(this).find('span').show();
					  $(this).find('input,textarea,select').hide();
				  });
			  <?php } ?>   
	 });
	 
	 var uploads = {
		 init:function(){
			 uploads.load_images();
			 $(document).on('click','.fileinput-button',function(e){;
				e.preventDefault();
				 $('#fileupload').trigger('click');
			 });
		
 $(document).on('click','#edit-report',function(e){
				  e.preventDefault();
				  $('.panel-collapse').removeClass('in');
				   $('#collapseFour,#collapseThree').addClass('in');
				  $('.val-text').each(function(){
					   $(this).find('span').hide();
					  $(this).find('input,textarea,select').show();
				  });
				  $('#save-form').show();
				  $('#edit-report').hide();
				   });
				   
				   $(document).on('click', '#complete-form', function (e) {
            e.preventDefault();
			 if (uploads.check_form()) {
           uploads.save_form(1);
			} else {
                flashalert.danger("Please answer all questions");
				$('.panel-collapse').removeClass('in');
				$('.val-text').each(function(){
					   $(this).find('span').hide();
					  $(this).find('input,textarea,select').show();
				  });
				   $('#collapseFour,#collapseThree').addClass('in');
				    $('#save-form').show();
				  $('#edit-report').hide();
            }	
        });
		
	
		
		$(document).on('click', '#save-form', function (e) {
            e.preventDefault();
             uploads.save_form(0);
        });	 
			 
         var file;
         var name;
         var type;
         var path;
		 this.webform=$('#webform-id').val();

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
			uploads.save_form(0);
             $.ajax({ url:helper.baseUrl+'records/get_attachment_file_path',
                 type:"POST",
                 dataType:"JSON",
                 data: { file: file.name }
             }).done(function(response){
                 path = response.path;
                 uploads.save_attachment(name, type, path)
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

	 },

save_attachment:function (name, type, path) {
            $.ajax({
                url: helper.baseUrl + 'records/save_attachment',
                type: "POST",
                dataType: "JSON",
                data: {
                    name: name,
                    type: type,
                    path: path,
                    urn: <?php echo $urn ?>,
					webform: uploads.webform
                }
            }).done(function (response) {
                if (response.success) {
					uploads.load_images();
                    flashalert.success("Attachment was saved");
                }
                else {
                    flashalert.danger("ERROR: The attachment was NOT saved");
                }
            });
        },
			 save_form:function(complete){
		
                $.ajax({
                    type: "POST",
                    data: $('#form').serialize() + '&save=1&complete='+complete,
					dataType:"JSON"
                }).done(function (response) {
					$(this).removeAttr('style');
					uploads.webform = response.id;
					  $('.val-text').each(function(){
					  $(this).find('span').text($(this).find('input,textarea,select').val()).show();
					  $(this).find('input,textarea,select').hide();
					    flashalert.success("Form was saved");
						 $('#save-form').hide();
				  $('#edit-report').show();
				  });
					
                  
                });
            
			
		},
		 check_form:function() {
            var completed = true;
			 $.each($('select.req,input.req,textarea.req'), function () {
				 $(this).removeAttr('style');
                if ($(this).val().length < 1) {
                    $(this).css('border', '1px solid red');
                    completed = false;
                }
            });
            return completed;

        },
		load_images:function(){
				  $.ajax({
                url: helper.baseUrl + "records/get_attachments",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: <?php echo $urn ?>,
					webform: uploads.webform
                }
            }).done(function(response){
				var images="";
				$.each(response.data,function(i,row){
					images += '<a class="fancybox-thumb" rel="fancybox-thumb" href="'+row.path+'" title="Added '+row.date+' by '+row.user+'"><img style="max-height:100px;max-width:100px;border:1px solid #000; margin:3px 5px" src="'+row.path+'" alt="" /></a>';
				});
				$('#image-gallery').html(images);
				$(".fancybox-thumb").fancybox({
		prevEffect	: 'none',
		nextEffect	: 'none',
		helpers	: {
			title	: {
				type: 'outside'
			},
			thumbs	: {
				width	: 50,
				height	: 50
			}
		}
	});

			});
			
		}
		
		}
          </script>
         
          
          <?php //load specific javascript files set in the controller
if (isset($javascript)):
    foreach ($javascript as $file): ?>
        <script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
    <?php endforeach;
endif; ?>
</body>
</html>