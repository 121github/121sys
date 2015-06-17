// JavaScript Document
jQuery(document).ready(function() {
	$('#summernote').summernote({
		  height: 300,                 // set editor height
	
		  minHeight: null,             // set minimum height of editor
		  maxHeight: null,             // set maximum height of editor
	
		  focus: true,                 // set focus to editable area after initializing summernote
		  onImageUpload: function(files, editor, welEditable) {
	         sendFile(files[0],editor,welEditable);
	      },
		  onpaste: function(e) {
            var thisNote = $(this);
            var updatePastedText = function(someNote){
                var original = someNote.code();
                var cleaned = CleanPastedHTML(original); //this is where to call whatever clean function you want. I have mine in a different file, called CleanPastedHTML.
                someNote.code('').html(cleaned); //this sets the displayed content editor to the cleaned pasted code.
            };
            setTimeout(function () {
                //this kinda sucks, but if you don't do a setTimeout, 
                //the function is called before the text is really pasted.
                updatePastedText(thisNote);
            }, 10);


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
