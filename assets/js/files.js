// JavaScript Document
var files = {
    init: function(folder_id,folder_name,write_access) {
		if(write_access){
		this.write_access=true;	
		}
        $('#folderpicker').on('change', function() {
            window.location.href = helper.baseUrl + 'files/manager/' + $(this).val();
        });

        $('#showall-files').on('click', function() {
        files.reload_folder(folder_id,1);
        });
        files.reload_folder(folder_id);
        this.folder_id = folder_id;
		this.folder_name = folder_name;
    },
    reload_folder: function(id, showall) {
        if (showall) {
            showall = 1;
        }
        $.ajax({
            url: helper.baseUrl + 'files/get_files',
            type: "POST",
            dataType: "JSON",
            data: {
                folder: id,
                showall: showall
            }
        }).done(function(response) {
            if (response.files.length > 0) {
                if (response.files.length > 9) {
                    $('#showall-files').show();
                }
                var table = "<table class='table'><thead><tr><th>Filename</th><th>File size</th><th>Added by</th><th>Date Added</th><th>Options</th></tr></thead><tbody>";
				
                $.each(response.files, function(i, row) {
					if(files.write_access){
					var delete_button = '<span data-file="' + row.file_id + '" class="delete-file glyphicon glyphicon-remove red tt" data-toggle="tooltip" data-placement="top" title="Delete file"></span>';	
					} else {
					var delete_button = '';	
					}
					
                    table += '<tr><td><a href="' + helper.baseUrl + 'upload/' + row.folder_name + '/' + row.filename + '">' + row.filename + '</a></td><td>' + row.size + '</td><td>' + row.username + '</td><td>' + row.date_added + '</td><td><span data-file="' + row.file_id + '" class="download-file glyphicon glyphicon-download-alt green pointer tt" data-toggle="tooltip" data-placement="top" title="Compress and download"></span> '+delete_button+'</td></tr>';
                });
                table += "</tbody></table>";

                $('#files-panel').find('.panel-body').html(table);
                $('.tt').tooltip();
                		$('.download-file').click(function() {
        files.download($(this).attr('data-file'));
        });
		$('.delete-file').click(function() {
        modal.confirm_delete($(this).attr('data-file'));
        });
            } else {
                $('#files-panel').find('.panel-body').html('<p>No files found in the selected folder</p>');
            }

        });

    },
	download:function(id){
window.location.href=helper.baseUrl+'files/download/'+id;
	},
	delete_file:function(id){
		
		$.ajax({
type: "POST",
  url: helper.baseUrl + 'files/delete_file',
data: {id:id},
dataType:  'JSON'
}).done(function(result){
	if(result.success){
	files.reload_folder(files.folder_id);	
	}
});
	}
}	
	
	var modal = {
confirm_delete:function(id){
	   $('.modal-title').text('Are you sure?');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('This will perminantly delete the file. Do you want to continue?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
           files.delete_file(id);
            $('#modal').modal('toggle');
        });
}
	}
	

