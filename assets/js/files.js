var files = {
    init: function(folder_id, folder_name, write_access) {
        if (write_access) {
            this.write_access = true;
        }
        $('#folderpicker').on('change', function() {
            window.location.href = helper.baseUrl + 'files/manager/' + $(this).val();
        });

        $('#showall-files').on('click', function() {
            files.reload_folder(folder_id, 1);
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
                var table = "<table class='table table-striped table-bordered data-table'><thead><tr><th>Folder ID</th><th>File ID</th><th>Folder Name</th><th>Filename</th><th>File size</th><th>Added by</th><th>Date Added</th><th>Options</th></tr></thead>";

                /*
				table += "<tbody>";
				$.each(response.files, function(i, row) {
					if(files.write_access){
					var delete_button = '<span data-file="' + row.file_id + '" class="delete-file glyphicon glyphicon-remove red tt" data-toggle="tooltip" data-placement="top" title="Delete file"></span>';	
					} else {
					var delete_button = '';	
					}
					
                    table += '<tr><td><a href="' + helper.baseUrl + 'upload/' + row.folder_name + '/' + row.filename + '">' + row.filename + '</a></td><td>' + row.size + '</td><td>' + row.username + '</td><td>' + row.date_added + '</td><td><span data-file="' + row.file_id + '" class="download-file glyphicon glyphicon-download-alt green pointer tt" data-toggle="tooltip" data-placement="top" title="Compress and download"></span> '+delete_button+'</td></tr>';
                });
				table += "</tbody>";
				*/
                table += "<tfoot><tr><th>Folder ID</th><th>File ID</th><th>Folder Name</th><th>Filename</th><th>File size</th><th>Added by</th><th>Date Added</th><th>Options</th></tr></tfoot></table>";

                $('#files-panel').find('.panel-body').html(table);
                files.data_table();
            } else {
                $('#files-panel').find('.panel-body').html('<p>No files found in the selected folder</p>');
            }

        });

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
    data_table: function() {

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
                "data": "folder_id"
            }, {
                "data": "file_id"
            }, {
                "data": "folder_name"
            }, {
                "data": "filename"
            }, {
                "data": "filesize"
            }, {
                "data": "username"
            }, {
                "data": "date_added"
            }, {
                "data": "options"
            }],
            "columnDefs": [{
                "targets": [0, 1, 2, 3, 4, 5, 6, 7],
                "data": null,
                "defaultContent": "na"
            }, {
                "targets": [0, 1, 2],
                "visible": false,
                "searchable": false
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