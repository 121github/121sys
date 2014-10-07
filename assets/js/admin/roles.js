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
	roles: {
        //initalize the role specific buttons 
        init: function() {
            $(document).on('click', '.add-btn', function() {
                admin.roles.create();
            });
            $(document).on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.roles.save($(this));
            });
            $(document).on('click', '.edit-btn', function() {
                admin.roles.edit($(this));
            });
            $(document).on('click', '.new-btn', function() {
                admin.roles.create();
            });
            $(document).on('click', '.del-btn', function() {
                modal.delete_role($(this).attr('item-id'));
            });
            //start the function to load the roles into the table
            admin.roles.load_roles();
        },
        //this function reloads the roles into the table body
        load_roles: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/get_roles',
                type: "POST",
                dataType: "JSON"
            }).done(function(response) {
                $tbody = $('.roles-panel').find('tbody');
                $tbody.empty();
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody.append("<tr><td class='role_id'>" + val.id + "</td><td class='role_name'>" + val.name + "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' item-id='" + val.id + "'>Delete</button></td></tr>");
                    }
                });
            });
        },
        //edit a role
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('form').find('input[name="role_id"]').val(row.find('.role_id').text());
            $('form').find('input[name="role_name"]').val(row.find('.role_name').text());
            $.ajax({
                url: helper.baseUrl + 'admin/get_role_permissions',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: row.find('.role_id').text()
                }
            }).done(function(response) {
                $.each(response.data, function(k, p) {
                    $('#pm_' + p.permission_id).prop('checked', true);
                });

            });
            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //add a new role
        create: function() {
            $('form').trigger('reset');
            $('form').find('input[type="hidden"]').val('');

            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //save a role
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'admin/save_role',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                admin.roles.load_roles();
                admin.hide_edit_form();
                flashalert.success("Role saved");
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_role',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.groups.load_roles();
                if (response.success) {
                    flashalert.success("Role was deleted");
                } else {
                    flashalert.danger("Unable to delete role. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete role. Contact administrator");
            });

        }

    }
}
/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
    delete_role: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this role?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.roles.remove(id);
            $('#modal').modal('toggle');
        });
    }
}