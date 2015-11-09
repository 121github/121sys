var admin = {
    //initialize all the generic javascript datapickers etc for this page
    init: function() {
		
		this.panel = $('#category-panel');
		
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
	 categories: {
        //initalize the group specific buttons 
        init: function() {
			
            admin.panel.on('click', '.add-btn', function() {
                admin.categories.create();
            });
            admin.panel.on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.categories.save();
            });
            admin.panel.on('click', '.edit-btn', function() {
                admin.categories.edit($(this).attr('data-id'));
            });
            admin.panel.on('click', '.new-btn', function() {
                admin.categories.create();
            });
            admin.panel.on('click', '.del-btn', function() {
                modal.delete_category($(this).attr('data-id'));
            });
            //start the function to load the groups into the table
            admin.categories.load_categories();
        },
        //this function reloads the groups into the table body
        load_categories: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/get_categories',
                type: "POST",
                dataType: "JSON",
				beforeSend:function(){
				$('#form-container').hide();
				$('#table-container').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" />').show();	
				}
            }).done(function(response) {
				var table = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Categories</th><th>Options</th></tr></thead><tbody>';
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
                url: helper.baseUrl + 'admin/category_details',
                type: "POST",
                dataType: "JSON",
				data: { id:id }
            }).done(function(response) {
			$('#form-container').find('[name="category_id"]').val(response.data.category_id);
			$('#form-container').find('[name="category_name"]').val(response.data.category_name);	
			$('#category-select').selectpicker('val',response.data.campaigns).selectpicker('refresh');
			 $('#table-container').fadeOut(1000, function() {
                $('#form-container').fadeIn();
            });
			})
        },
        //add a new group
        create: function() {
			$('#form-container').find('[name="category_id"]').val('');	
			$('#form-container').find('[name="category_name"]').val('');	
			$('#category-select').selectpicker('val',[]).selectpicker('refresh');

          $('#table-container').fadeOut(1000, function() {
                $('#form-container').fadeIn();
            });
        },
        //save a group
        save: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/save_category',
                type: "POST",
                dataType: "JSON",
                data: $('#form-container').find('form').serialize()
            }).done(function(response) {
                admin.categories.load_categories();
                admin.hide_edit_form();
                flashalert.success("Category group saved");
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_category',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.categories.load_categories();
                if (response.success) {
                    flashalert.success("Category group deleted");
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
    delete_category: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this campaign group?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.categories.remove(id);
            $('#modal').modal('toggle');
        });
    }
}