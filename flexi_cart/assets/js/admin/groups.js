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
            $('.ajax-table').fadeIn();
        });
    },
	 groups: {
        //initalize the group specific buttons 
        init: function() {
            $(document).on('click', '.add-btn', function() {
                admin.groups.create();
            });
            $(document).on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.groups.save($(this));
            });
            $(document).on('click', '.edit-btn', function() {
                admin.groups.edit($(this));
            });
            $(document).on('click', '.new-btn', function() {
                admin.groups.create();
            });
            $(document).on('click', '.del-btn', function() {
                modal.delete_group($(this).attr('item-id'));
            });
            //start the function to load the groups into the table
            admin.groups.load_groups();
        },
        //this function reloads the groups into the table body
        load_groups: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/get_groups',
                type: "POST",
                dataType: "JSON"
            }).done(function(response) {
                $tbody = $('.groups-panel').find('tbody');
                $tbody.empty();
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody.append("<tr><td class='group_id'>" + val.id + "</td><td class='group_name'>" + val.name + "</td><td class='theme_folder'>" + val.theme_folder + "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' item-id='" + val.id + "'>Delete</button></td></tr>");
                    }
                });
            });
        },
        //edit a group
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('form').find('input[name="group_id"]').val(row.find('.group_id').text());
            $('form').find('input[name="group_name"]').val(row.find('.group_name').text());
			$('form').find('input[name="theme_folder"]').val(row.find('.theme_folder').text());
            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //add a new group
        create: function() {
            $('form').trigger('reset');
            $('form').find('input[type="hidden"]').val('');

            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //save a group
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'admin/save_group',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                admin.groups.load_groups();
                admin.hide_edit_form();
                flashalert.success("Group saved");
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_group',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.groups.load_groups();
                if (response.success) {
                    flashalert.success("Group was deleted");
                } else {
                    flashalert.danger("Unable to delete group. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete group. Contact administrator");
            });

        }

    }
}
/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
    delete_group: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this group?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.groups.remove(id);
            $('#modal').modal('toggle');
        });
    }
}