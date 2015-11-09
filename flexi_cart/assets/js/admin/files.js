var admin = {
    //initialize all the generic javascript datapickers etc for this page
    init: function() {
        $(document).on('click', '.close-btn', function(e) {
            e.preventDefault();
            admin.hide_edit_form();
        });

    },
    hide_edit_form: function() {
        $('form').fadeOut(1000, function() {
            $('.folder-data').fadeIn();
        });
    },
	folders: {
        //initalize the group specific buttons 
        init: function() {
            $(document).on('click', '.add-btn', function() {
                admin.folders.create();
            });
            $(document).on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.folders.save($(this));
            });
            $(document).on('click', '.edit-btn', function() {
                admin.folders.edit($(this));
            });
            $(document).on('click', '.new-btn', function() {
                admin.folders.create();
            });
            $(document).on('click', '.del-btn', function() {
                modal.delete_folder($(this).attr('item-id'));
            });
            //start the function to load the groups into the table
            admin.folders.load_folders();
        },
        //this function reloads the groups into the table body
        load_folders: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/folder_data',
                type: "POST",
                dataType: "JSON"
            }).done(function(response) {
                $tbody = $('.folders-panel').find('tbody');
                $tbody.empty();
                if (response.success) {
					var table = '<table class="table ajax-table"><thead><th>Folder ID</th><th>Folder Name</th><th>Options</th></thead><body>';
                    $.each(response.data, function(i, val) {
                            table += '<tr><td class="hidden accepted_filetypes">'+val.accepted_filetypes+'</td><td class="folder_id">'+val.folder_id+'</td><td class="folder_name">'+val.folder_name+'</td><td><button class="btn btn-default btn-xs edit-btn">Edit</button> <button class="btn btn-default btn-xs del-btn" item-id="' + val.folder_id + '">Delete</button></td></tr>';   
                    });
					table += '</tbody></table>';
					$('.folder-data').html(table);
                } else {
                    $('.folder-data').html('<p>' + response.msg + '</p>');
                }
				admin.hide_edit_form();
            });
        },
        //edit a group
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('form').find('input[name="folder_id"]').val(row.find('.folder_id').text());
            $('form').find('input[name="folder_name"]').val(row.find('.folder_name').text());
			$('form').find('input[name="accepted_filetypes"]').val(row.find('.accepted_filetypes').text());
			//set the values in the user select pickers
			$.ajax({url:helper.baseUrl+'admin/get_folder_read_users',
			data: {id:row.find('.folder_id').text() },
			dataType: "JSON",
			type:"POST"
		}).done(function(response){
			$('.folder-read-users').selectpicker('val',response.users).selectpicker('render');
			});
			//set the values in the user select pickers
			$.ajax({url:helper.baseUrl+'admin/get_folder_write_users',
			data: {id:row.find('.folder_id').text() },
			dataType: "JSON",
			type:"POST"
		}).done(function(response){
			$('.folder-write-users').selectpicker('val',response.users).selectpicker('render');
			});
			
			$('.folder-data').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //add a new group
        create: function() {
            $('form').trigger('reset');
            $('form').find('input[type="hidden"]').val('');

            $('.folder-data').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //save a group
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'admin/save_folder',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
				if(response.success){
				admin.folders.load_folders();
                flashalert.success("Folder saved");
				} else {
				flashalert.danger(response.msg);	
				}
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_folder',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.folders.load_folders();
                if (response.success) {
                    flashalert.success("folder was deleted");
                } else {
                    flashalert.danger("Unable to delete folder. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete folder. Contact administrator");
            });

        }

    }
}
/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
    delete_folder: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this folder?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.folders.remove(id);
            $('#modal').modal('toggle');
        });
    }
}