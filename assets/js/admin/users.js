var admin = {
    //initialize all the generic javascript datapickers etc for this page
    init: function() {
        $(document).on('change', '.selectpicker', function(e) {
            if ($(this).val() == "other") {
                $(this).closest('.form-group').find('input[type="text"]').show()
            } else {
                $(this).closest('.form-group').find('input[type="text"]').val('').hide()
            }
        });

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
	users: {
        //initalize the group specific buttons 
        init: function() {
            $(document).on('click', '.add-btn', function() {
                admin.users.create();
            });
            $(document).on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.users.save($(this));
            });
            $(document).on('click', '.edit-btn', function() {
                admin.users.edit($(this));
            });
            $(document).on('click', '.new-btn', function() {
                admin.users.create();
            });
            $(document).on('click', '.del-btn', function() {
                modal.delete_user($(this).attr('item-id'));
            });
            //start the function to load the groups into the table
            admin.users.load_users();
        },
        //this function reloads the groups into the table body
        load_users: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/user_data',
                type: "POST",
                dataType: "JSON"
            }).done(function(response) {
                $tbody = $('.users-panel').find('tbody');
                $tbody.empty();
                if (response.success) {
                    $.each(response.data, function(i, val) {
                        if (response.data.length) {
                            $tbody.append('<tr><td class="hidden user_email">' + val.user_email + '</td><td class="hidden user_telephone">' + val.user_telephone + '</td><td class="user_id">' + val.user_id + '</td><td class="name">' + val.name + '</td><td class="username">' + val.username + '</td>><td><span class="hidden group_id">' + val.group_id + '</span>' + val.group_name + '</td><td><span class="hidden team_id">' + val.team_id + '</span>' + val.team_name + '</td><td><span class="hidden role_id">' + val.role_id + '</span>' + val.role_name + '</td><td><span class="hidden user_status">' + val.user_status + '</span>' + val.status_text + '</td><td><button class="btn btn-default btn-xs edit-btn">Edit</button> <button class="btn btn-default btn-xs del-btn" item-id="' + val.user_id + '">Delete</button></td></tr>');
                        }
                    });
                } else {
                    $('.user-data').append('<p>' + response.msg + '</p>');
                }
            });
        },
        //edit a group
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('form').find('input[name="user_id"]').val(row.find('.user_id').text());
            $('form').find('input[name="name"]').val(row.find('.name').text());
            $('form').find('input[name="username"]').val(row.find('.username').text());
            $('form').find('select[name="group_id"]').selectpicker('val', row.find('.group_id').text());
            $('form').find('select[name="role_id"]').selectpicker('val', row.find('.role_id').text());
            $('form').find('select[name="user_status"]').selectpicker('val', row.find('.user_status').text());
			$('form').find('select[name="team_id"]').selectpicker('val', row.find('.team_id').text());
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
                url: helper.baseUrl + 'admin/save_user',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                admin.users.load_users();
                admin.hide_edit_form();
                flashalert.success("User saved");
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_user',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.users.load_users();
                if (response.success) {
                    flashalert.success("User was deleted");
                } else {
                    flashalert.danger("Unable to delete user. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete user. Contact administrator");
            });

        }

    }
}
/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
    delete_user: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this user?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.users.remove(id);
            $('#modal').modal('toggle');
        });
    }
}