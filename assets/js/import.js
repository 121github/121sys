// JavaScript Document
/*the class below is for the data import page. It gets initialized by the data.php view*/
var importer = {
    init: function() {
        $(document).on('change', '#source', function(e) {
            importer.check_source($(this));
        });
		$(document).on('blur', '#new_source', function(e) {
            if($(this).val().length>0){
				 importer.add_source();
			}
        });
        $(document).on('change', '#campaign', function() {
            importer.show_campaign_type();
        });

        $(document).on('click', '.goto-step-2', function(e) {
            e.preventDefault();
            importer.load_step_2();
        });
        $(document).on("click", ".goto-step-3", function(e) {
            e.preventDefault();
            importer.display_sample();
        });
        $(document).on("click", ".goto-step-1", function(e) {
            e.preventDefault();
            importer.load_step_1();
        });
        $(document).on("click", "#import", function(e) {
            e.preventDefault();
            importer.fix_headers();
        });

        //initialize the upload widget
        $('#fileupload').fileupload({
                maxNumberOfFiles: 1,
                dataType: 'json',
                acceptFileTypes: /(\.|\/)(csv)$/i,
                progressall: function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                },
                always: function(e, data) {
                    $('#files').find('#file-status').text("File uploaded").removeClass('text-danger').addClass('text-success');
                }
            }).on('fileuploaddone', function(e, data) {
                var file = data.result.files[0];
                $('#files').find('#filename').text(file.name);
                $('#files').find('#file-status').text('');
                importer.importcsv(file.name);
            }).on('fileuploadprocessalways', function(e, data) {
                var file = data.files[0];
                if (file.error) {
                    $('#files').find('#file-status').text(file.error).removeClass('text-success').addClass('text-danger');
                }
            }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');


    },
	add_source:function(){
		 $.ajax({
            url: helper.baseUrl + 'import/add_source',
            type: "POST",
            dataType: "JSON",
            data: {
                source: $('#new_source').val()
            }
        }).done(function(response) {
			if(response.success){
				$('#source option').each(function(){
					$(this).removeAttr('selected');
				});
				$('#source').append('<option selected value="'+response.data+'">'+$('#new_source').val()+'</option>');	
			$('#new_source').hide();
			$('#source').selectpicker('render').selectpicker('refresh')
			} else {
			flashalert.danger("This data source already exists");	
			}
		
		});
	},
    importcsv: function(filename) {
        $.ajax({
            url: helper.baseUrl + 'import/import_csv',
            type: "POST",
            dataType: "JSON",
            data: {
                filename: filename
            }
        }).done(function(response) {
            if (response.success) {
				$('.tt').tooltip();
                $('.goto-step-3').show();
            } else {
				if(response.error){
				$('#filename').text("File was removed due to a formatting issue");
				$('#file-status').html("<span class='red'>"+response.error+"</span>");
				flashalert.danger('There was a problem with the file contents');
				}
               if(response.output){
				$('#filename').text("Temp table could not be created. Please contact the site admin");
				$('#file-status').html("<span class='red'>"+response.output+"</span>");
				flashalert.danger('There was a problem with the import script.');
				}
            }
        });
    },

    fix_headers: function() {
         $('#import-progress').text("Reassigning column headers...");
 			$.ajax({
            url: helper.baseUrl + 'import/update_headers',
            type: "POST",
            dataType: "JSON",
			data: $('#data-form').serialize()+'&filename='+encodeURIComponent($('#filename').text())+'&campaign='+$('#campaign').val()+'&source='+$('#source').val()+'&type='+$('#campaign option:selected').attr('ctype')
        }).done(function(response) {
            if (response.success) {
                importer.check_import();
            } else {
				$('#import-progress').html("<span class='red'>"+response.msg+"</span>");
                flashalert.danger(response.msg);
            }
        });
    },
    check_import: function() {
         $('#import-progress').text("Checking available data...");
 			$.ajax({
            url: helper.baseUrl + 'import/check_import',
            type: "POST",
            dataType: "JSON",
			data: $('#data-form').serialize()+'&filename='+encodeURIComponent($('#filename').text())+'&campaign='+$('#campaign').val()+'&source='+$('#source').val()+'&type='+$('#campaign option:selected').attr('ctype')
        }).done(function(response) {
            if (response.success) {
                importer.start_import();
            } else {
				$('#import-progress').html("<span class='red'>"+response.msg+"</span>");
                flashalert.danger(response.msg);
            }
        });
    },
    start_import: function() {
        $('#import-progress').text("Generating new keys...");
        $.ajax({
            url: helper.baseUrl + 'import/add_urns',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                importer.format_data();
            } else {
				$('#import-progress').html("<span class='red'>Import failed while generating new keys</span>");
                flashalert.danger("Import failed while generating new keys");
            }
        });
    },
    format_data: function() {
        $('#import-progress').text("Formatting data...");
        $.ajax({
            url: helper.baseUrl + 'import/format_data',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                importer.create_records();
            } else {
				$('#import-progress').html("<span class='red'>Import failed while formatting the data</span>");
                flashalert.danger("Import failed while formatting the data");
            }
        });
    },
    create_records: function() {
        $('#import-progress').text("Creating new records...");
        $.ajax({
            url: helper.baseUrl + 'import/create_records',
            type: "POST",
            dataType: "JSON",
            data: {
                campaign: $('#campaign').val(),
                source: $('#source').val(),
                type: $('#campaign option:selected').attr('ctype')
            }
        }).done(function(response) {
            if (response.success) {
                importer.create_client_refs();
            } else {
				$('#import-progress').html("<span class='red'>Import failed while creating the records</span>");
                flashalert.danger("Import failed while creating the records");
				importer.undo_changes();
            }
        });
    },
	    create_client_refs: function() {
        $('#import-progress').text("Creating client refs...");
        $.ajax({
            url: helper.baseUrl + 'import/create_client_refs',
            type: "POST",
            dataType: "JSON",
            data: {
                campaign: $('#campaign').val(),
                source: $('#source').val(),
                type: $('#campaign option:selected').attr('ctype')
            }
        }).done(function(response) {
            if (response.success) {
                importer.create_record_details();
            } else {
				$('#import-progress').html("<span class='red'>Import failed while creating the client references</span>");
                flashalert.danger("Import failed while creating the references");
				importer.undo_changes();
            }
        });
    },
    create_record_details: function() {
        $('#import-progress').text("Adding custom fields...");
        $.ajax({
            url: helper.baseUrl + 'import/create_record_details',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                importer.create_contacts();
            } else {
				$('#import-progress').html("<span class='red'>Import failed while adding the custom fields</span>");
                flashalert.danger("Import failed while adding the custom fields");
				importer.undo_changes();
            }
        });
    },
    create_contacts: function() {
        $('#import-progress').text("Adding contacts...");
        $.ajax({
            url: helper.baseUrl + 'import/create_contacts',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                importer.create_contact_telephones();
            } else {
				$('#import-progress').html("<span class='red'>Import failed while adding the contacts</span>");
                flashalert.danger("Import failed while adding the contacts");
				importer.undo_changes();
            }
        });
    },
    create_contact_telephones: function() {
        $('#import-progress').text("Adding contact telephone numbers...");
        $.ajax({
            url: helper.baseUrl + 'import/create_contact_telephones',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                importer.create_contact_addresses();
            } else {
				importer.undo_changes();
                flashalert.danger("Import failed while adding the contact telephone numbers");
            }
        });
    },
    create_contact_addresses: function() {
        $('#import-progress').text("Adding contact addresses...");
        $.ajax({
            url: helper.baseUrl + 'import/create_contact_addresses',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
				if($('#campaign option:selected').attr('ctype')=="B2B"){
                importer.create_companies();
				} else {
				importer.tidy_up();	
				}
            } else {
				$('#import-progress').html("<span class='red'>Import failed while adding the contact addresses</span>");
				importer.undo_changes();
                flashalert.danger("Import failed while adding the contact addresses");
            }
        });
    },
    create_companies: function() {
        $('#import-progress').text("Adding companies...");
        $.ajax({
            url: helper.baseUrl + 'import/create_companies',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                importer.create_company_telephones();
            } else {
				$('#import-progress').html("<span class='red'>Import failed while adding companies</span>");
				importer.undo_changes();
                flashalert.danger("Import failed while adding companies");
				importer.undo_changes();
            }
        });
    },
    create_company_telephones: function() {
        $('#import-progress').text("Adding company telephone numbers...");
        $.ajax({
            url: helper.baseUrl + 'import/create_company_telephones',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                importer.create_company_addresses();
            } else {
				importer.undo_changes();
				$('#import-progress').html("<span class='red'>Import failed while adding the company telephone numbers</span>");
                flashalert.danger("Import failed while adding the company telephone numbers");
            }
        });
    },
    create_company_addresses: function() {
        $('#import-progress').text("Adding company addresses...");
        $.ajax({
            url: helper.baseUrl + 'import/create_company_addresses',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
				if($('#merge-options').val()!=""){
					importer.merge_contacts();
				} else {
                  importer.tidy_up();
				}
            } else {
				$('#import-progress').html("<span class='red'>Import failed while adding the company addresses</span>");
                flashalert.danger("Import failed while adding the company addresses");
				importer.undo_changes();
            }
        });
    },
	merge_contacts:function(){
		 $('#import-progress').text("Merging contacts to companies...");
		 if($('#merge-options').val()=="1"){
			var merge_path = "merge_by_client_refs"
		 } else if($('#merge-options').val()=="2"){
			 var merge_path = "merge_dupe_companies";
		 }else if($('#merge-options').val()=="3"){
			 var merge_path = "merge_by_merge_column";
		 }
		 $.ajax({
            url: helper.baseUrl + 'import/'+merge_path,
            type: "POST",
            dataType: "JSON",
			data: { campaign: $('#campaign').val() }
        }).done(function(response) {
            if (response.success) {
                importer.tidy_up();
            }
		});
	},
	    tidy_up: function() {
        $('#import-progress').text("Cleaning up any mess...");
        $.ajax({
            url: helper.baseUrl + 'import/tidy_up',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            if (response.success) {
                flashalert.success("The import was successful");
				$('#import-progress').html("<span class='green'>Import was completed</span>");
            } else {
				$('#import-progress').html("<span class='red'>Import failed while cleaning up</span>");
                flashalert.danger("Import failed while cleaning up");
				importer.undo_changes();
            }
        });
    },
	undo_changes: function(){
        $('#import-progress').text($('#import-progress').text()+' ..Undoing changes');
        $.ajax({
            url: helper.baseUrl + 'import/undo_changes',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
			$('#import-progress').html("Import failed");
		})
	},
    show_campaign_type: function() {
        var ctype = $('#campaign option:selected').attr('ctype');
        $('#ctype-text').text("This is a " + ctype + " campaign.").show();
    },
    check_source: function($btn) {
        if ($btn.val() == "other") {
            $btn.closest('.form-group').find('input[type="text"]').show()
        } else {
            $btn.closest('.form-group').find('input[type="text"]').val('').hide()
        }
    },
    load_step_1: function() {
        $('#step-2').slideUp(1000, function() {
            $('#step-1').slideDown(1000);
        });
    },
    load_step_2: function($btn) {
        var incomplete;
        if ($('#campaign').val() == "") {
            var incomplete = "Please select a campaign";
        } else if ($('#source').val() == "") {
            var incomplete = "Please select a data source";
        } else if ($('#source').val() == "other" && $('#new_source').val() == "") {
            var incomplete = "Please enter the new source name";
        }

        if (incomplete) {
            flashalert.danger(incomplete)
        } else {
            $('#step-1,#step-3').slideUp(1000, function() {
                $('#step-2').slideDown(1000);
            });
            $('#campaign-name-title').text($('#campaign option:selected').text());
            $('#campaign-type-title').text($('#campaign option:selected').attr('ctype'));
        }

    },
    display_sample: function() {
        $('#step-2').slideUp(1000, function() {
            $.ajax({
                url: helper.baseUrl + 'import/get_sample',
                type: "POST",
                dataType: "JSON",
                data: {
                    file: $('#filename').text()
                }
            }).done(function(response) {
                if (response.success) {
                    $('#import-progress').html('&nbsp;');
                    var thead, tbody;
                    $.each(response.sample, function(i, row) {
                        tbody += "<tr>"
                        thead = "<tr>"
                        $.each(row, function(key, val) {
                            thead += "<th></th>";
                            tbody += "<td>" + val + "</td>";
                        });
                        tbody += "</tr>"
                        thead += "</tr>"
                    });
                    $('#step-3').find('#sample-table thead').empty().append(thead);
                    $('#step-3').find('#sample-table tbody').empty().append(tbody);
                    $('#step-3').slideDown(1000);
                    $('.goto-step-3').show();
                    importer.create_field_options();
                } else {
                    $('#step-3').find('#sample-table thead').empty().append("<tr><th>Error</th></tr>");
                    $('#step-3').find('#sample-table tbody').empty().append("<tr><td>The was a problem with the supplied CSV file. Please open it in excel and save as a comma seperated file and try again.</td></tr><tr><td>" + response.output[0] + "</td></tr>");
                    $('#step-3').slideDown(1000);
                }
            });

        });

    },

    create_field_options: function() {
        var ctype = $('#campaign option:selected').attr('ctype');
        var camp = $('#campaign').val();
        $.ajax({
            url: helper.baseUrl + 'import/import_fields',
            type: "POST",
            dataType: "JSON",
            data: {
                type: ctype,
                campaign: camp
            }
        }).done(function(response) {
            $('#step-3').find('#sample-table th').each(function(i) {
                var $select = "<select class='import-field'><option value=''>Select field</option>";
                $.each(response.fields, function(table, fields) {
                    $select += "<optgroup label='" + table + "'>";
                    $.each(fields, function(column, name) {
                        var is_selected = "";
                        if (response.selected[i] == column) {
                            var is_selected = "selected";
                        }
                        $select += "<option " + is_selected + " value='" + column + "'>" + name + "</option>";
                    });
                    $select += "</optgroup>";
                });
                $select += "</select>";
                $(this).append($select);
                $(this).find('select').attr('name', 'field[' + i + ']');
            });
        });
    }
}