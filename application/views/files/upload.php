<h1>File Upload</h1>
<form action="<?php echo base_url()."files/start_upload" ?>" id="mydropzone" class="dropzone" >
<h4>Select the upload destination</h4>
<select class="selectpicker" name="folder"><option value="" selected>Choose the folder...</option><option value="cv" selected>CV</option></select>
</form>

<script>
$(document).ready(function(){

   Dropzone.options.mydropzone = {
        accept: function(file, done) {
            if (file.name.split('.').pop() != "doc"&&file.name.split('.').pop() != "docx") {
                done("Only .doc files can be added to the CV folder");
            }
            else { done(); }
        }
    }
});
</script>