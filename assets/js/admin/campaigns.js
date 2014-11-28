var admin = {
    //initialize all the generic javascript datapickers etc for this page
    init: function() {
        $(document).on('change', '.campaign-select', function(e) {
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
                    if ($('.group-select').val() != "") {
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
                        $tbody.append("<tr><td class='campaign_id'>" + val.campaign_id + "</td><td class='campaign_name'>" + val.campaign_name + "</td><td>" + val.campaign_type_desc + "<span class='hidden custom_panel_name'>" + val.custom_panel_name + "</span><span class='hidden campaign_type_id'>" + val.campaign_type_id + "</span><span class='hidden min_quote_days'>" + (val.min_quote_days?val.min_quote_days:'') + "</span><span class='hidden max_quote_days'>" + (val.max_quote_days?val.max_quote_days:'') + "</span></td><td>" + val.client_name + "<span class='hidden client_id'>" + val.client_id + "</span></td><td>" + val.campaign_status_text + "<span class='hidden campaign_status'>" + val.campaign_status + "</span></td><td class='start_date'>" + val.start_date + "</td><td class='end_date'>" + val.end_date + "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn'  item-id='" + val.campaign_id + "'>Delete</button></td></tr>");
                    }
                });
            });
        },
        edit: function($btn) {
            var row = $btn.closest('tr');
            var min_quote_days = $('form').find('input[name="min_quote_days"]');
            var max_quote_days = $('form').find('input[name="max_quote_days"]');

            $('form').find('input[name="campaign_id"]').val(row.find('.campaign_id').text());
            $('form').find('input[name="custom_panel_name"]').val(row.find('.custom_panel_name').text());
            $('form').find('select[name="campaign_type_id"]').selectpicker('val', row.find('.campaign_type_id').text());
            $('form').find('input[name="campaign_name"]').val(row.find('.campaign_name').text());
            $('form').find('select[name="client_id"]').selectpicker('val', row.find('.client_id').text());
            $('form').find('select[name="campaign_status"]').selectpicker('val', row.find('.campaign_status').text());
            $('form').find('input[name="start_date"]').data('DateTimePicker').setDate(row.find('.start_date').text());
            $('form').find('input[name="end_date"]').data('DateTimePicker').setDate(row.find('.end_date').text());
            min_quote_days.val(row.find('.min_quote_days').text());
            max_quote_days.val(row.find('.max_quote_days').text());

            admin.campaigns.get_features(row.find('.campaign_id').text());

            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });

            //Only numbers permited in those fields
            min_quote_days.numeric();
            max_quote_days.numeric();

            //Check if the min_quote_days is less than max_quote_days
            min_quote_days.blur(function(){
                admin.campaigns.check_quote_days(parseInt(min_quote_days.val()), parseInt(max_quote_days.val()));
            });
            max_quote_days.blur(function(){
                admin.campaigns.check_quote_days(parseInt(min_quote_days.val()), parseInt(max_quote_days.val()));
            });

        },
        check_quote_days: function(min_quote_days, max_quote_days){
            if (min_quote_days > max_quote_days) {
                $('form').find('.quote_days_error').append("The minimum quote days must be less than the maximum quote days");
                $('form').find('input[name="min_quote_days"]').css('color', 'red');
                $('form').find('input[name="max_quote_days"]').css('color', 'red');
            }
            else {
                $('form').find('.quote_days_error').text("");
                $('form').find('input[name="min_quote_days"]').css('color', 'black');
                $('form').find('input[name="max_quote_days"]').css('color', 'black');
            }
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
                if (response.success) {
                    admin.campaigns.load_campaigns();
                    admin.hide_edit_form();
                    flashalert.success(response.message);
                }
                else {
                    flashalert.danger(response.message);
                }
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
    }
}

/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
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