// JavaScript Document

var script = {
    //initalize the group specific buttons 
    init: function() {
    	$('#scripts-panel').on('click', '.add-btn', function() {
        	script.create();
        });
        $('#scripts-panel').on('click', '.save-btn', function(e) {
            e.preventDefault();
            script.save($(this));
        });
        $('#script-data').on('click', '.edit-btn', function(e) {
			e.preventDefault();
        	script.edit($(this));
        });
        $('#script-data').on('click', '.del-btn', function() {
            modal.delete_script($(this).attr('item-id'));
        });
        $('#scripts-panel').on('click', '.close-btn', function(e) {
        	e.preventDefault();
        	script.cancel();
        });
        //start the function to load the scripts into the table
        script.load_scripts();
    },
    //this function reloads the scripts into the table body
    load_scripts: function() {
    	$.ajax({
            url: helper.baseUrl + 'scripts/script_data',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            $tbody = $('#script-data .ajax-table').find('tbody');
            $tbody.empty();
            $.each(response.data, function(i, val) {
                if (response.data.length) {
						$tbody
							.append("<tr><td class='script_id'>"
										+ val.script_id
									+ "</td><td class='script_name'>"
										+ val.script_name
									+ "</td><td class='script_text' style='display:none;'>"
										+ val.script
									+ "</td><td class='script_sort' style='display:none;'>"
										+ val.sort
									+ "</td><td class='script_expandable' style='display:none;'>"
										+ val.expandable
									+ "</td><td><button class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' item-id='"
										+ val.script_id
									+ "'>Delete</button></td></tr>");
                }
            });
        });
    },
    //cancel the edit view
    cancel: function() {
    	//Hide edit form
        script.hide_edit_form();
        //Load script table
        script.load_scripts();
    },
	//edit a script
    edit: function($btn) {
    	$("button[type=submit]").attr('disabled',false);
    	var row = $btn.closest('tr');
    	$('#container-fluid form').find('input[name="script_id"]').val(row.find('.script_id').text());
        $('#container-fluid form').find('input[name="script_name"]').val(row.find('.script_name').text());
        $('#container-fluid form').find('input[name="sort"]').val(row.find('.script_sort').text());
        
        document.getElementById("script").value = row.find('.script_text').html();
        
        if (row.find('.script_expandable').text() == 1)
        	document.getElementById("expandable").checked = true;
        else
        	document.getElementById("expandable").checked = false;
        
        
        var data = {id : $('#container-fluid form').find('input[name="script_id"]').val()};
        
        $.ajax({
            url: helper.baseUrl + "scripts/get_campaings_by_script_id",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function(response){
                    $('#scripts-panel .selectpicker').selectpicker('val',response["data"]).selectpicker('refresh');
       
            },
            error: function(jqXHR, textStatus, errorThrown) {
             console.log(textStatus+" "+errorThrown);
           }
        });

        $('.ajax-table').fadeOut(1000, function() {
            $('#container-fluid form').fadeIn();
        });
    },
	//add a new script
    create: function() {
    	$('#container-fluid form').trigger('reset');
    	$('.ajax-table').fadeOut(1000, function() {
            $('#container-fluid form').fadeIn(1000)
        });
    },
    //save a script
    save: function($btn) {
    	$("button[type=submit]").attr('disabled','disabled');
    	$.ajax({
            url: helper.baseUrl + 'scripts/save_script',
            type: "POST",
            dataType: "JSON",
            data: $('#container-fluid form').serialize()
        }).done(function(response) {
        	//Reload script table
            script.load_scripts();
            //Hide edit form
            script.hide_edit_form();
        	
            flashalert.success("Script saved");
        });
    },
    remove: function(id) {
        $.ajax({
            url: helper.baseUrl + 'scripts/delete_script',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            }
        }).done(function(response) {
            script.load_scripts();
			if(response.success){
            flashalert.success("Script was deleted");
			} else {
			flashalert.danger("Unable to delete script. Contact administrator");
			}
        }).fail(function(response){
			flashalert.danger("Unable to delete script. Contact administrator");
		});

    },
    //this fades out the script edit form and brings back the table
    hide_edit_form: function() {
        $('form,.editor-form').fadeOut(1000, function() {
            $('.ajax-table').fadeIn();
        });
    },
    
}

/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    delete_script: function(id) {
       modal_header.text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
		modal_body.text('Are you sure you want to delete this script?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            script.remove(id);
            $('#modal').modal('toggle');
        });
    }
}