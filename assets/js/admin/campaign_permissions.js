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
	campaign_permissions: {
        //initalize the campaign_permissions specific buttons 
        init: function() {
            $(document).on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.campaign_permissions.save($(this));
            });
            $(document).on('click', '.edit-btn', function() {
                admin.campaign_permissions.edit($(this));
            });
            //start the function to load the roles into the table
            admin.campaign_permissions.load_campaigns();
        },
        //this function reloads the campaign_permissions into the table body
        load_campaigns: function() {
            $.ajax({
                url: helper.baseUrl + 'ajax/get_campaigns',
                type: "POST",
                dataType: "JSON"
            }).done(function(response) {
                $tbody = $('#campaign-permissions-panel').find('tbody');
                $tbody.empty();
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody.append("<tr><td class='campaign_id'>" + val.id + "</td><td class='campaign_name'>" + val.name + "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button></td></tr>");
                    }
                });
            });
        },
        //edit a role
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('#cp-form').trigger('reset');
			$('input[type="checkbox"]').checkboxX('destroy');
            $('#cp-form').find('input[name="campaign_id"]').val(row.find('.campaign_id').text());
            $('#cp-form').find('input[name="campaign_name"]').val(row.find('.campaign_name').text());
            $.ajax({
                url: helper.baseUrl + 'admin/get_campaign_permissions',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: row.find('.campaign_id').text()
                }
            }).done(function(response) {
                $.each(response.data, function(k, p) {
					if(p.permission_state=="1"){
                    $('#cb_' + p.permission_id).val('1').prop('checked', true);
					$('#pm_' + p.permission_id).val('1');
					} else {
					$('#cb_' + p.permission_id).prop('checked', false).val('0');
					 $('#pm_' + p.permission_id).val('0');
					}
                });
				$('input[type="checkbox"]').checkboxX({
    iconChecked: "<b>&check;</b>",
    iconUnchecked: "<b>X</b>",
	iconNull: "",
}).change(function(){
	$(this).closest('div.checkbox-group').find('input[type="hidden"]').val($(this).val());
})
            });
            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //save a permissions
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'admin/save_campaign_permissions',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                admin.campaign_permissions.load_campaigns();
                admin.hide_edit_form();
                flashalert.success("Campaign permissions saved");
            }).fail(function(){
				flashalert.danger("There was an error saving");
			});
        }
    }
}
