// JavaScript Document
var template = {
    //initalize the group specific buttons 
    init: function() {
    	$(document).on('click', '.add-btn', function() {
        	template.create();
        });
        $(document).on('click', '.save-btn', function(e) {
            e.preventDefault();
            template.save($(this));
        });
        $(document).on('click', '.edit-btn', function() {
        	template.edit($(this));
        });
        $(document).on('click', '.new-btn', function() {
        	template.create();
        });
        $(document).on('click', '.del-btn', function() {
            modal.delete_template($(this).attr('item-id'));
        });
        $(document).on('click', '.close-btn', function(e) {
        	e.preventDefault();
        	template.cancel();
        });
        $(document).on('click', '.delete-attach-btn', function(e) {
        	e.preventDefault();
        	template.delete_attachment($(this));
        });
        //Empty attachment table
    	template.empty_attachment_table();
        //start the function to load the groups into the table
        template.load_templates();
    },
    //this function reloads the groups into the table body
    load_templates: function() {
    	$.ajax({
            url: helper.baseUrl + 'templates/all_template_data',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            $tbody = $('.template-data .ajax-table').find('tbody');
            $tbody.empty();
            $.each(response.data, function(i, val) {
                if (response.data.length) {
						$tbody
							.append("<tr><td class='template_id'>"
										+ val.template_id
									+ "</td><td class='template_name'>"
										+ val.template_name
									+ "</td><td><button class='btn btn-default btn-xs edit-btn' data-id='"+val.template_id+"' >Edit</button> <button class='btn btn-default btn-xs del-btn' item-id='"
										+ val.template_id
									+ "'>Delete</button></td></tr>");
                }
            });
        });
    },
    //cancel the edit view
    cancel: function() {
    	//Delte attachments file uploaded without save
    	var data = {filesUploaded :  $('#container-fluid form').find('input[name="template_attachments"]').val()};
    	$.ajax({
            url: helper.baseUrl + "templates/delete_attachments_list",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function(data){
            	//Hide edit form
            	template.hide_edit_form();
            	//Empty attachment table
            	template.empty_attachment_table();
            	//Load template table
            	template.load_templates();
            },
            error: function(jqXHR, textStatus, errorThrown) {
             console.log(textStatus+" "+errorThrown);
           }
        });
    },
	//edit a template
    edit: function($btn) {
		var id = $btn.attr('data-id');
		$.ajax({  url: helper.baseUrl + 'templates/template_data',
            data: { id:id },
			type:"POST",
			dataType:"JSON",
		}).done(function(result){
			    	$("button[type=submit]").attr('disabled',false);
    	$('#attachments').fadeOut();
        var row = $btn.closest('tr');
        $('#container-fluid form').find('input[name="template_id"]').val(result.data.template_id);
        $('#container-fluid form').find('input[name="template_name"]').val(result.data.template_name);
        $('#container-fluid form').find('input[name="template_from"]').val(result.data.template_from);
        $('#container-fluid form').find('input[name="template_to"]').val(result.data.template_to);
        $('#container-fluid form').find('input[name="template_cc"]').val(result.data.template_cc);
        $('#container-fluid form').find('input[name="template_bcc"]').val(result.data.template_bcc);
        $('#container-fluid form').find('input[name="template_subject"]').val(result.data.template_subject);
		if(result.data.template_unsubscribe=="1"){
			$('#container-fluid form').find('#unsubscribe-yes').prop('checked',true).parent().addClass('active');
			$('#container-fluid form').find('#unsubscribe-no').prop('checked',false).parent().removeClass('active');
		} else {
			$('#container-fluid form').find('#unsubscribe-no').prop('checked',true).parent().addClass('active');
			$('#container-fluid form').find('#unsubscribe-yes').prop('checked',false).parent().removeClass('active');
		}
        tinyMCE.activeEditor.setContent(result.data.template_body);

        var data = {id : $('#container-fluid form').find('input[name="template_id"]').val()};
        
        $.ajax({
            url: helper.baseUrl + "templates/get_campaings_by_template_id",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function(data){
    
                    $('#campaigns_select').selectpicker('val',data["data"]).selectpicker('render');
       
            },
            error: function(jqXHR, textStatus, errorThrown) {
             console.log(textStatus+" "+errorThrown);
           }
        });
        
        template.load_attachments();
		});
       
    },
	//add a new template
    create: function() {
    	//Delte attachments file uploaded without save
    	var data = {filesUploaded :  $('#container-fluid form').find('input[name="template_attachments"]').val()};
    	$.ajax({
            url: helper.baseUrl + "templates/delete_attachments_list",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function(data){
            	$("button[type=submit]").attr('disabled',false);
                $('#container-fluid form').trigger('reset');
                 tinyMCE.activeEditor.setContent('');
                $('#campaigns_select').selectpicker('val',[]).selectpicker('render');
                $('#container-fluid form').find('input[type="hidden"]').val('');
                template.empty_attachment_table();
            },
            error: function(jqXHR, textStatus, errorThrown) {
             console.log(textStatus+" "+errorThrown);
           }
        });
    	
        $('.ajax-table').fadeOut(1000, function() {
            $('#container-fluid form').fadeIn(1000)
        });
    },
    //save a template
    save: function($btn) {
    	$('#tinymce').html(tinyMCE.activeEditor.getContent());
    	$.ajax({
            url: helper.baseUrl + 'templates/save_template',
            type: "POST",
            dataType: "JSON",
            data: $('#container-fluid form').serialize(),
			beforeSend:function(){	
				$("button[type=submit]").prop('disabled',true);
			}
        }).done(function(response) {
        	//Reload template table
            template.load_templates();
            //Hide edit form
            template.hide_edit_form();
            //Empty attachment table
        	template.empty_attachment_table();
        	
            flashalert.success("Template saved");
        }).fail(function(response){
				$("button[type=submit]").prop('disabled',false);
				flashalert.danger(response.responseText);
		});
    },
    remove: function(id) {
        $.ajax({
            url: helper.baseUrl + 'templates/delete_template',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            }
        }).done(function(response) {
            template.load_templates();
			if(response.success){
            flashalert.success("Template was deleted");
			} else {
			flashalert.danger("Unable to delete template. Contact administrator");
			}
        }).fail(function(response){
			flashalert.danger("Unable to delete template. Contact administrator");
		});

    },
    //this fades out the template edit form and brings back the table
    hide_edit_form: function() {
        $('form,.editor-form').fadeOut(1000, function() {
            $('.ajax-table').fadeIn();
        });
    },
    //add a new attached file to the list of the new attachments
    attach_new_file: function(filename, path) {
    	var data = {filename : filename, path : path, newFiles :  $('#container-fluid form').find('input[name="template_attachments"]').val()};
    	
    	$.ajax({
            url: helper.baseUrl + 'templates/set_attached_files',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
        	$('#container-fluid form').find('input[name="template_attachments"]').val(response.data);
        	//Reload the new attachments table
        	template.load_new_attachments(response.data_array);
        });
    },
    //Remove an attachment from the list of new attachments
    remove_new_attach: function(path) {
    	//If the path exist as a new attachment, remove from the list
    	var data = {path : path, newFiles :  $('#container-fluid form').find('input[name="template_attachments"]').val()};
    	
    	$.ajax({
            url: helper.baseUrl + 'templates/unset_attached_files',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
        	$('#container-fluid form').find('input[name="template_attachments"]').val(response.data);
        	//Reload the new attachments table
        	template.load_new_attachments(response.data_array);
        });
    },
    //Delete action for the remove button
    delete_attachment: function($btn) {
    	var id = $btn.attr('item-id');
    	var path = $btn.attr('item-path');
    	
    	//Delte attachments file uploaded without save
    	var data = {id : id, path: path};
    	$.ajax({
            url: helper.baseUrl + "templates/delete_attachment_by_id",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function(data){
            	template.load_attachments();
            	template.remove_new_attach(path);
            	$('#files').find('#file-status').text("File removed").removeClass('text-success').addClass('text-danger');
            }
        });
    },
    //Empty attachment table
    empty_attachment_table: function() {
    	//Empty attachment table
    	$tbody = $('.template-data .new_attach_table').find('tbody');
    	$thead = $('.template-data .new_attach_table').find('thead');
        $tbody.empty();
        $thead.empty();
        $tbody = $('.template-data .attach_table').find('tbody');
    	$thead = $('.template-data .attach_table').find('thead');
        $tbody.empty();
        $thead.empty();
        
    	//Set progress-bar 0%
    	$('#container-fluid form').find('input[name="template_attachments"]').val("");
    	$('#progress .progress-bar').css(
             'width',
             0 + '%'
        );
    	$('#files').find('#filename').empty();
     	$('#files').find('#file-status').empty();
    },
    //Load attachments from the database for a particular template
    load_attachments: function() {
    	var data = {id : $('#container-fluid form').find('input[name="template_id"]').val()};
    	$.ajax({
            url: helper.baseUrl + "templates/get_attachments_by_template_id",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function(data){
            	var thead,tbody;
            	$tbody = $('.template-data .attach_table').find('tbody');
            	$tbody.empty();
            	$.each(data['data'],function(key,val){
         			tbody += "<tr>"
         			thead += "<th></th>";
         			tbody += "<td><a target='_blank' href='"+val['path']+"'>"+val['name']+"</a></td><td><button item-id='"+key+"' item-path='"+val['path']+"' class='marl btn btn-danger delete-attach-btn'>Remove</button></td>";
         		});
         		$('#attachments').find('.attach_table thead').append(thead);
         		$('#attachments').find('.attach_table tbody').append(tbody);
         		$('#attachments').fadeIn();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus+" "+errorThrown);
           }
        });
        
        $('.ajax-table').fadeOut(1000, function() {
            $('#container-fluid form').fadeIn();
        });
    },
  //Load the new attachments table
    load_new_attachments: function(data) {
    	var thead,tbody;

    	$('#upload-status').fadeIn();
        
    	$tbody = $('.template-data .new_attach_table').find('tbody');
    	$thead = $('.template-data .new_attach_table').find('thead');
        $tbody.empty();
        $thead.empty();
        if (data.length == 0) {
        	thead += "<th></th>";
        } else {
        	thead += "<th>New Attachments</th>";
        }
		$.each(data,function(key,val){
			tbody += "<tr>"
			tbody += "<td><a target='_blank' href='"+val.substring(0, template.stripos(val,'?'))+"'>"+val.substring(template.stripos(val,'?')+1)+"</a></td><td><button item-path='"+val+"' class='marl btn btn-danger delete-attach-btn'>Remove</button></td>";
		});
		$('#attachments').find('.new_attach_table thead').append(thead);
		$('#attachments').find('.new_attach_table tbody').append(tbody);
		$('#attachments').fadeIn();
		$('#upload-status').fadeOut(1000);
    },
    stripos: function(f_haystack, f_needle, f_offset) {
        //  discuss at: http://phpjs.org/functions/stripos/
        // original by: Martijn Wieringa
        //  revised by: Onno Marsman
        //   example 1: stripos('ABC', 'a');
        //   returns 1: 0

        var haystack = (f_haystack + '')
            .toLowerCase();
        var needle = (f_needle + '')
            .toLowerCase();
        var index = 0;

        if ((index = haystack.indexOf(needle, f_offset)) !== -1) {
            return index;
        }
        return false;
    }

}

/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    delete_template: function(id) {
       modal_header.text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
		modal_body.text('Are you sure you want to delete this template?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            template.remove(id);
            $('#modal').modal('toggle');
        });
    }
}