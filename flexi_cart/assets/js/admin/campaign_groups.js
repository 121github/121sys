var admin = {
    //initialize all the generic javascript datapickers etc for this page
    init: function() {
		
		this.panel = $('#campaign-group-panel');
		
        $(document).on('click', '.close-btn', function(e) {
            e.preventDefault();
            admin.hide_edit_form();
        });

    },
	    hide_edit_form: function() {
   			$('#form-container').fadeOut(1000, function() {
                $('#table-container').fadeIn();
            });
    },
	 campaign_groups: {
        //initalize the group specific buttons 
        init: function() {
			
            admin.panel.on('click', '.add-btn', function() {
                admin.campaign_groups.create();
            });
            admin.panel.on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.campaign_groups.save();
            });
            admin.panel.on('click', '.edit-btn', function() {
                admin.campaign_groups.edit($(this).attr('data-id'));
            });
            admin.panel.on('click', '.new-btn', function() {
                admin.campaign_groups.create();
            });
            admin.panel.on('click', '.del-btn', function() {
                modal.delete_campaign_group($(this).attr('data-id'));
            });
            //start the function to load the groups into the table
            admin.campaign_groups.load_campaign_groups();
        },
        //this function reloads the groups into the table body
        load_campaign_groups: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/get_campaign_groups',
                type: "POST",
                dataType: "JSON",
				beforeSend:function(){
				$('#form-container').hide();
				$('#table-container').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" />').show();	
				}
            }).done(function(response) {
				var table = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Campaigns</th><th>Options</th></tr></thead><tbody>';
				if(response.data.length>0){
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        table += "<tr><td>" + val.id + "</td><td>" + val.name + "</td><td>" + val.count + "</td><td><button data-id='"+val.id+"' class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' data-id='" + val.id + "'>Delete</button></td></tr>";
                    }
                });
				 table += '</body></table>';
				 $('#table-container').html(table);
				} else {
				$('#form-container').hide();
				$('#table-container').html('No campaign groups were found').show();	
				}
            });
        },
        //edit a group
        edit: function(id) {
			$.ajax({
                url: helper.baseUrl + 'admin/campaign_group_details',
                type: "POST",
                dataType: "JSON",
				data: { id:id }
            }).done(function(response) {
			$('#form-container').find('[name="campaign_group_id"]').val(response.data.campaign_group_id);
			$('#form-container').find('[name="campaign_group_name"]').val(response.data.campaign_group_name);	
			$('#campaign-group-select').selectpicker('val',response.data.campaigns).selectpicker('refresh');
			 $('#table-container').fadeOut(1000, function() {
                $('#form-container').fadeIn();
            });
			})
        },
        //add a new group
        create: function() {
			$('#form-container').find('[name="campaign_group_id"]').val('');	
			$('#form-container').find('[name="campaign_group_name"]').val('');	
			$('#campaign-group-select').selectpicker('val',[]).selectpicker('refresh');

          $('#table-container').fadeOut(1000, function() {
                $('#form-container').fadeIn();
            });
        },
        //save a group
        save: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/save_campaign_group',
                type: "POST",
                dataType: "JSON",
                data: $('#form-container').find('form').serialize()
            }).done(function(response) {
                admin.campaign_groups.load_campaign_groups();
                admin.hide_edit_form();
                flashalert.success("Campaign group saved");
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_campaign_group',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.campaign_groups.load_campaign_groups();
                if (response.success) {
                    flashalert.success("Campaign group deleted");
                } else {
                    flashalert.danger("Unable to delete campaign group. Contact administrator");
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
    delete_campaign_group: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this campaign group?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.campaign_groups.remove(id);
            $('#modal').modal('toggle');
        });
    }
}