Dropzone.options.mydropzone = {
					 init: function () {
    this.on("queuecomplete", function (file) {
		   files.reload_folder($('#dropzone-folder-id').val());
    });
  },
  paramName: "file", // The name that will be used to transfer the file
  maxFilesize: 100, // MB
  
};

var files = {
    init: function() {
        $('#folderpicker').on('change', function() {
            window.location.href = helper.baseUrl + 'files/manager/' + $(this).val();
        });

        files.reload_folder();
		
		$('.folder-filter').on('click',function() {
		   $('#folder-filter-text').html('<span class="glyphicon glyphicon-filter"></span> '+$(this).text());
		   if(Number($(this).attr('data-id'))>0){
			   
			$('#panel-title').text('Showing all files in '+$('#folder-filter-text').text()+' folder'); 
			 files.check_access($(this).attr('data-id'));
		   } else {
			 $('#upload-btn').addClass('disabled');
			 $('#dropzone-holder').hide(); 
			$('#panel-title').text('Showing all files...'); 
		   }
		 
		  files.reload_folder($(this).attr('data-id'));
        });
		$(document).on('click','#upload-btn,.close-upload',function(){
				$('#dropzone-holder').toggle();
			})
	
    },
	check_access:function(id){
		$.ajax({
            type: "POST",
            url: helper.baseUrl + 'files/get_permissions',
            data: {
                id: id
            },
            dataType: 'JSON'
        }).done(function(response) {
            if (response.success) {
				$('#dropzone-folder-id').val(response.permissions.folder_id);
				$('#dropzone-folder-name').val(response.permissions.folder_name);
                if(response.permissions.write_access){
				$('#upload-btn').removeClass('disabled'); 
				}
            }
        });
	},
    reload_folder: function(folder_id) {
                var table = "<table class='table table-striped table-bordered data-table'><thead><tr><th>Folder Name</th><th>Filename</th><th>File size</th><th>Added by</th><th>Date Added</th><th>Options</th></tr></thead>";
                table += "<tfoot><tr><th>Folder Name</th><th>Filename</th><th>File size</th><th>Added by</th><th>Date Added</th><th>Options</th></tr></tfoot></table>";

                $('#files-panel').find('#table-holder').html(table);
                files.data_table(folder_id);

    },
    download: function(id) {
        window.location.href = helper.baseUrl + 'files/download/' + id;
    },
    delete_file: function(id) {

        $.ajax({
            type: "POST",
            url: helper.baseUrl + 'files/delete_file',
            data: {
                id: id
            },
            dataType: 'JSON'
        }).done(function(result) {
            if (result.success) {
                files.reload_folder(files.folder_id);
            }
        });
    },
    data_table: function(folder_id) {
		if(folder_id>0){
		$('.data-table').dataTable().fnDestroy();
		}
        var table = $('.data-table').DataTable({
            "dom": '<"top">p<"dt_info"i>rt<"bottom"lp><"clear">',
            "oLanguage": {
                "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
            },
            "bAutoWidth": false,
            "processing": true,
            "serverSide": true,
            //ordering:  false,
            "iDisplayLength": 10,
            stateSave: true,
            "ajax": {
                url: helper.baseUrl + "files/process_files",
                type: 'POST',
                beforeSend: function() {
                    $('.dt_info').hide();
                },
				data: function (d){
				d.folder = folder_id;
           		 },
                complete: function() {
                    $('.dt_info').show();
                    $('.tt').tooltip();
                    $('.download-file').click(function() {
                        files.download($(this).attr('data-file'));
                    });
                    $('.delete-file').click(function() {
                        modal.confirm_delete($(this).attr('data-file'));
                    });				
                }
            },
            "columns": [{
                "data": "folder_name"
            }, {
                "data": "filename"
            }, {
                "data": "filesize"
            }, {
                "data": "username"
            }, {
                "data": "date_uploaded"
            }, {
                "data": "options"
            }],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5],
                "data": null,
                "defaultContent": "na"
            }]
        });

        //filterable columns
        // Setup - adds search input boxes to the footer row
        $('.data-table tfoot th').each(function() {
            var title = $('.data-table thead th').eq($(this).index()).text();
            if (title == "Options") {
                $(this).html('');
            } else {
                var search_val = table.column($(this).index()).search();
				//console.log(table.column($(this).index()).search());
                $(this).html('<input class="dt-filter form-control" placeholder="Filter..." value="' + search_val[0] + '" />');
            }
        });

        // Apply the search
        table.columns().eq(0).each(function(colIdx) {
            $('input', table.column(colIdx).footer()).on('keyup change', function() {
                table
                    .column(colIdx)
                    .search(this.value)
                    .draw();
            });
        });
        //this moves the search input boxes to the top of the table
		
        var r = $('.data-table tfoot tr');
        r.find('th').each(function() {
            $(this).css('padding', 8);
        });
        $('.data-table thead').append(r);
        $('#search_0').css('text-align', 'center');
    }



}

var modal = {
    confirm_delete: function(id) {
        modal_header.text('Are you sure?');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
		modal_body.text('This will perminantly delete the file. Do you want to continue?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            files.delete_file(id);
            $('#modal').modal('toggle');
        });
    }
}