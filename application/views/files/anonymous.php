<h1>File Upload</h1>
<form action="<?php echo base_url()."files/upload" ?>" id="mydropzone" class="dropzone" >
<input type="hidden" name="folder" value="<?php echo $folder ?>" />
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