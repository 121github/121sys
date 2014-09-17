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
    campaigns: {
        //initalize the campaigns specific buttons 
        init: function() {
            $(document).on('click', '.add-btn', function() {
                admin.campaigns.create();
            });
            $(document).on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.campaigns.save($(this));
            });
            $(document).on('click', '.edit-btn', function() {
                admin.campaigns.edit($(this));
            });
            $(document).on('click', '.new-btn', function() {
                admin.campaigns.create();
            });
            $(document).on('click', '.del-btn', function() {
                modal.delete_campaign($(this).attr('item-id'));
            });
            $(document).on('change', '.group-select', function() {
                admin.campaigns.populate_users($(this).val(), true);
            });
            $(document).on('change', '.campaignlist-select', function() {
                admin.campaigns.populate_outcomes($(this).val());
                admin.campaigns.campaign_outcomes($(this).val());
            });
            $(document).on('change', '.campaign-select', function() {
                if ($(this).val() == "") {
                    $('.user-select,.group-select,.access-select,.access-add,.access-del').prop('disabled', true);
                    $('.user-select').empty().append('<option value="">Select a group first</option>');
                    $('.access-select').empty();
                } else {
					if($('.group-select').val()!=""){
					 admin.campaigns.populate_users($('.group-select').val(), true);
					}
                    admin.campaigns.populate_access($(this).val());
                }
            });
            $(document).on('click', '.access-add', function() {
                admin.campaigns.add_access($('.campaign-select').val(), $('.user-select').val());
            });
            $(document).on('click', '.access-del', function() {
                admin.campaigns.revoke_access($('.campaign-select').val(), $('.access-select').val());
            });
            $(document).on('click', '.outcome-add', function() {
                admin.campaigns.add_outcomes($('.campaignlist-select').val(), $('.outcome-select').val());
            });
            $(document).on('click', '.outcome-del', function() {
                admin.campaigns.remove_outcomes($('.campaignlist-select').val(), $('.camp-outcome-select').val());
            });
            //start the function to load the campaigns into the table
            admin.campaigns.load_campaigns();
        },
        add_access: function(camp, users) {
            $.ajax({
                url: helper.baseUrl + 'admin/add_access',
                type: "POST",
                dataType: "JSON",
                data: {
                    campaign: camp,
                    users: users
                }
            }).done(function(response) {
                admin.campaigns.populate_access(camp);
                admin.campaigns.populate_users($('.group-select').val());
                flashalert.success('User(s) were added to the campaign access list');
            });

        },
        add_outcomes: function(camp, outcomes) {
            $.ajax({
                url: helper.baseUrl + 'admin/add_campaign_outcomes',
                type: "POST",
                dataType: "JSON",
                data: {
                    campaign: camp,
                    outcomes: outcomes
                }
            }).done(function(response) {
                admin.campaigns.populate_outcomes(camp);
                admin.campaigns.campaign_outcomes($('.campaignlist-select').val());
                flashalert.success('Outcome(s) were added to the selected campaign');
            });

        },
        remove_outcomes: function(camp, outcomes) {
            $.ajax({
                url: helper.baseUrl + 'admin/remove_campaign_outcomes',
                type: "POST",
                dataType: "JSON",
                data: {
                    campaign: camp,
                    outcomes: outcomes
                }
            }).done(function(response) {
                admin.campaigns.populate_outcomes(camp);
                admin.campaigns.campaign_outcomes($('.campaignlist-select').val());
                flashalert.success('Campaign outcomes were updated');
            });

        },
        revoke_access: function(camp, users) {
            $.ajax({
                url: helper.baseUrl + 'admin/revoke_access',
                type: "POST",
                dataType: "JSON",
                data: {
                    campaign: camp,
                    users: users
                }
            }).done(function(response) {
                admin.campaigns.populate_access(camp);
                admin.campaigns.populate_users($('.group-select').val());
                flashalert.success('Campaign access was updated');
            });

        },
        populate_access: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/get_campaign_access',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                if (id != "") {
                    $('.access-add,.access-del,.group-select,.campaign-select,.access-select').prop('disabled', false);
                }
                $('.access-select').empty();
                $.each(response.data, function(i, row) {
                    $('.access-select').append('<option value="' + row.id + '">' + row.name + '</option>');
                });
            });
        },
        populate_outcomes: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/populate_outcomes',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                if (id != "") {
                    $('.outcome-add,.outcome-del,.camp-outcome-select,.campaign-select,.outcome-select').prop('disabled', false);
                }
                $('.outcome-select').empty();
                $.each(response.data, function(i, row) {
                    $('.outcome-select').append('<option value="' + row.id + '">' + row.name + '</option>');
                });
            });
        },
        campaign_outcomes: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/campaign_outcomes',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                if (id != "") {
                    $('.outcome-add,.outcome-del,.campaignlist-select,.camp-outcome-select,.outcome-select').prop('disabled', false);
                }
                $('.camp-outcome-select').empty();
                $.each(response.data, function(i, row) {
                    $('.camp-outcome-select').append('<option value="' + row.id + '">' + row.name + '</option>');
                });
            });
        },
        populate_users: function(id, selected) {
            if (selected) {
                selected = "selected";
            } else {
                selected = "";
            }
            $.ajax({
                url: helper.baseUrl + 'admin/users_in_group',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    campaign: $('.campaign-select').val()
                }
            }).done(function(response) {
                $('.user-select').empty().prop('disabled', false);
                $.each(response.data, function(i, row) {
                    $('.user-select').append('<option value="' + row.id + '" ' + selected + '>' + row.name + '</option>');
                });
            });

        },
        //this function reloads the campaigns into the table body
        load_campaigns: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/get_campaigns',
                type: "POST",
                dataType: "JSON"
            }).done(function(response) {
                $tbody = $('.campaign-panel').find('tbody');
                $tbody.empty();
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody.append("<tr><td class='campaign_id'>" + val.campaign_id + "</td><td class='campaign_name'>" + val.campaign_name + "</td><td>" + val.campaign_type_desc + "<span class='hidden custom_panel_name'>" + val.custom_panel_name + "</span><span class='hidden campaign_type_id'>" + val.campaign_type_id + "</span></td><td>" + val.client_name + "<span class='hidden client_id'>" + val.client_id + "</span></td><td>" + val.campaign_status_text + "<span class='hidden campaign_status'>" + val.campaign_status + "</span></td><td class='start_date'>" + val.start_date + "</td><td class='end_date'>" + val.end_date + "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn'  item-id='" + val.campaign_id + "'>Delete</button></td></tr>");
                    }
                });
            });
        },
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('form').find('input[name="campaign_id"]').val(row.find('.campaign_id').text());
			$('form').find('input[name="custom_panel_name"]').val(row.find('.custom_panel_name').text());
            $('form').find('select[name="campaign_type_id"]').selectpicker('val', row.find('.campaign_type_id').text());
            $('form').find('input[name="campaign_name"]').val(row.find('.campaign_name').text());
            $('form').find('select[name="client_id"]').selectpicker('val', row.find('.client_id').text());
            $('form').find('select[name="campaign_status"]').selectpicker('val', row.find('.campaign_status').text());
            $('form').find('input[name="start_date"]').data('DateTimePicker').setDate(row.find('.start_date').text());
            $('form').find('input[name="end_date"]').data('DateTimePicker').setDate(row.find('.end_date').text());
            admin.campaigns.get_features(row.find('.campaign_id').text());
            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        create: function() {
            $('form').trigger('reset');
            $('form').find('input[type="hidden"]').val('');

            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //save a campaign
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'admin/save_campaign',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                admin.campaigns.load_campaigns();
                admin.hide_edit_form();
                flashalert.success("Campaign saved");
            });
        },

        get_features: function(campaign) {
            $.ajax({
                url: helper.baseUrl + 'admin/get_campaign_features',
                type: "POST",
                dataType: "JSON",
                data: {
                    campaign: campaign
                }
            }).done(function(response) {
                $('form').find('.campaign-features').selectpicker('val', response.data).selectpicker('render');
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_campaign',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.campaigns.load_campaigns();
                if (response.success) {
                    flashalert.success("Campaign was deleted");
                } else {
                    flashalert.danger("Unable to delete campaign. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete campaign. Contact administrator");
            });
        }
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
                        $tbody.append("<tr><td class='group_id'>" + val.id + "</td><td class='group_name'>" + val.name + "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' item-id='" + val.id + "'>Delete</button></td></tr>");
                    }
                });
            });
        },
        //edit a group
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('form').find('input[name="group_id"]').val(row.find('.group_id').text());
            $('form').find('input[name="group_name"]').val(row.find('.group_name').text());
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
                            $tbody.append('<tr><td class="hidden user_email">' + val.user_email + '</td><td class="hidden user_telephone">' + val.user_telephone + '</td><td class="user_id">' + val.user_id + '</td><td class="name">' + val.name + '</td><td class="username">' + val.username + '</td><td><span class="hidden group_id">' + val.group_id + '</span>' + val.group_name + '</td><td><span class="hidden role_id">' + val.role_id + '</span>' + val.role_name + '</td><td><span class="hidden user_status">' + val.user_status + '</span>' + val.status_text + '</td><td><button class="btn btn-default btn-xs edit-btn">Edit</button> <button class="btn btn-default btn-xs del-btn" item-id="' + val.user_id + '">Delete</button></td></tr>');
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

    },
    //this fades out the campaign edit form and brings back the table
    hide_edit_form: function() {
        $('form').fadeOut(1000, function() {
            $('.ajax-table').fadeIn();
        });
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
    },
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
    },
    delete_campaign: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this campaign?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.campaigns.remove(id);
            $('#modal').modal('toggle');
        });
    }
}