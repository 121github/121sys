
<div class="modal fade" id="uploader" tabindex="-1" role="dialog" aria-labelledby="updater" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">✕</button>
<i class="icon-credit-card icon-7x"></i>
<p class="no-margin">You can upload only 1 JPEG file at a time!</p>
</div>
<div class="modal-body">
<form action="" class="uploadform dropzone no-margin dz-clickable">
<div class="dz-default dz-message">
<span>Drop the logo file here or click to explore</span>
</div>
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default attachtopost" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>


<div id="wrapper">
<div id="sidebar-wrapper">
  <?php  $this->view('dashboard/navigation.php',$page) ?>
</div>
<div id="page-content-wrapper">
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Logo Admin</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary roles-panel">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Campaign Logos
            <div class="pull-right">
              <div class="btn-role">
                <button type="button" data-toggle="modal" data-target="#uploader" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-plus"></span> Add Logo</button>
              </div>
            </div>
          </div>
          <!-- /.panel-heading -->
          <div class="panel-body logo-panel">
			   <form class="form-horizontal" id="logo-form" style="padding:10px 20px;" >
<div class="form-group input-group-sm">
        <p>Campaign</p>
        <select name="campaign_id" class="selectpicker">
        <?php foreach($campaigns as $campaign){ ?>
        <option value="<?php echo $campaign['id'] ?>"><?php echo $campaign['name'] ?></option>
        <?php } ?>
        
        </select>
    </div>
         <div class="form-group input-group-sm logo-list">
         </div>   
            <div class="form-actions pull-right">
        <button type="submit" class="marl btn btn-primary save-logo">Set logo</button>
    </div>
            </form>
          </div>
          <!-- /.panel-body --> 
        </div>
      </div>
      
      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --></div>
</div>
<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 

<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>

$(document).ready(function(){
$('.selectpicker').selectpicker();
	
Dropzone.autoDiscover = false; // keep this line if you have multiple dropzones in the same page
$(".uploadform").dropzone({
acceptedFiles: "image/jpeg",
url: '../images/add_logo',
maxFiles: 1, // Number of files at a time
maxFilesize: 1, //in MB
maxfilesexceeded: function(file)
{
alert('You have uploaded more than 1 Image. Only the first file will be uploaded!');
},
success: function (response) {
var x = JSON.parse(response.xhr.responseText);
$('.icon').hide(); // Hide Cloud icon
$('#uploader').modal('hide'); // On successful upload hide the modal window
$('.img').attr('src',x.img); // Set src for the image
$('.thumb').attr('src',x.thumb); // Set src for the thumbnail
$('img').addClass('imgdecoration');
this.removeAllFiles(); // This removes all files after upload to reset dropzone for next upload
console.log('Image -> '+x.img+', Thumb -> '+x.thumb); // Just to return the JSON to the console.
logo.load_logos();
},
addRemoveLinks: true,
removedfile: function(file) {
var _ref; // Remove file on clicking the 'Remove file' button
return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
}
});
logo.init();
});

var logo = {
init:function(){
	logo.load_logos();
	$('.logo-panel').find('select[name="campaign_id"]').change(function(){
		logo.load_logos();
	});
	$('.save-logo').click(function(e){
		e.preventDefault();
		logo.save();
	});
},
save:function(){
	$.ajax({url:helper.baseUrl+"logos/save",
	type:"POST",
	dataType:"JSON",
	data:$('#logo-form').serialize()
	}).done(function(response){
		if(response.success){
		flashalert.success('Camapign logo saved');	
		}
	});
	
},
load_logos:function(id){
	$.ajax({url:helper.baseUrl+"logos/get_logo_html",
	type:"POST",
	dataType:"HTML",
	data:{ id: $('.logo-panel').find('select[name="campaign_id"]').val() }
	}).done(function(response){
		$('.logo-list').html(response);
	});
	
}

}
</script> 
