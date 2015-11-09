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
	 teams: {
        //initalize the team specific buttons 
        init: function() {
            $(document).on('click', '.add-btn', function() {
                admin.teams.create();
            });
            $(document).on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.teams.save($(this));
            });
            $(document).on('click', '.edit-btn', function() {
                admin.teams.edit($(this));
            });
            $(document).on('click', '.new-btn', function() {
                admin.teams.create();
            });
            $(document).on('click', '.del-btn', function() {
                modal.delete_team($(this).attr('item-id'));
            });
            //start the function to load the teams into the table
            admin.teams.load_teams();
        },
        //this function reloads the teams into the table body
        load_teams: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/get_teams',
                type: "POST",
                dataType: "JSON"
            }).done(function(response) {
                $tbody = $('.teams-panel').find('tbody');
                $tbody.empty();
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody.append("<tr><td class='team_id'>" + val.id + "</td><td class='team_name'>" + val.name + "</td><td class='group_name'><span class='hidden group_id'>"+val.group_id+"</span>" + val.group_name + "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' item-id='" + val.id + "'>Delete</button></td></tr>");
                    }
                });
            });
        },
        //edit a team
        edit: function($btn) {
            var row = $btn.closest('tr');
            $('form').find('input[name="team_id"]').val(row.find('.team_id').text());
            $('form').find('input[name="team_name"]').val(row.find('.team_name').text());
			$('form').find('select[name="group_id"]').selectpicker('val',row.find('.group_id').text());
			var team = $('form').find('input[name="team_id"]').val();
			$.getJSON(helper.baseUrl+'admin/get_team_managers/'+team,function(response){
				$('form').find('#user-select').selectpicker('val',response.data).selectpicker('render');
			});
			
            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //add a new team
        create: function() {
            $('form').trigger('reset');
            $('form').find('input[type="hidden"]').val('');

            $('.ajax-table').fadeOut(1000, function() {
                $('form').fadeIn();
            });
        },
        //save a team
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'admin/save_team',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                admin.teams.load_teams();
                admin.hide_edit_form();
                flashalert.success("team saved");
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_team',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.teams.load_teams();
                if (response.success) {
                    flashalert.success("team was deleted");
                } else {
                    flashalert.danger("Unable to delete team. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete team. Contact administrator");
            });

        }

    }
}
/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
    delete_team: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this team?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.teams.remove(id);
            $('#modal').modal('toggle');
        });
    }
}