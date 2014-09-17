// JavaScript Document
jQuery(document).ready(function() {
	$('#summernote').summernote({
		  height: 300,                 // set editor height
	
		  minHeight: null,             // set minimum height of editor
		  maxHeight: null,             // set maximum height of editor
	
		  focus: true,                 // set focus to editable area after initializing summernote
		  onImageUpload: function(files, editor, welEditable) {
	         sendFile(files[0],editor,welEditable);
	      }
	});
});


function sendFile(file,editor,welEditable) {
    data = new FormData();
    data.append("file", file);
    $.ajax({
        url: helper.baseUrl + "templates/saveimage",
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        type: 'POST',
        //dataType: "JSON",
        success: function(data){
          editor.insertImage(welEditable, data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
         console.log(textStatus+" "+errorThrown);
         flashalert.danger("asdasdasd");
       }
	});
}
