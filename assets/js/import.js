// JavaScript Document
	/*the class below is for the data import page. It gets initialized by the data.php view*/
	var importer = {
                        init: function() {
                            $(document).on('change', '#source', function(e) {
                                importer.check_source($(this));
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
                        		importer.load_step_3();
                			});
							$(document).on("click", ".goto-step-1", function(e) {
                        		e.preventDefault();
                        		importer.load_step_1();
                			});
							$(document).on("click", "#import", function(e) {
                        		e.preventDefault();
                        		importer.check_import();
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
									importer.importcsv();
                                    var file = data.result.files[0];
                                    $('#files').find('#filename').text(file.name);
                                    $('#files').find('#file-status').text('');
                                }).on('fileuploadprocessalways', function(e, data) {
                                    var file = data.files[0];
                                    if (file.error) {
                                        $('#files').find('#file-status').text(file.error).removeClass('text-success').addClass('text-danger');
                                    }
                                }).prop('disabled', !$.support.fileInput)
                                .parent().addClass($.support.fileInput ? undefined : 'disabled');


				},
				importcsv:function(){
					 $.ajax({
                                url: helper.baseUrl + 'import/import_csv',
                                type: "POST",
                                dataType: "JSON"
                            }).done(function(response) {
					 		if(response.success){
					 $('.goto-step-3').show();
							} else {
					flashalert.danger('There was a problem with the bash script');
							}
							});
				},
				
				check_import:function(){
					var ctype = $('#campaign option:selected').attr('ctype');
					var error,urn,coname,tel,cotel,name;
					var fields = new Array();
					$('.import-field').each(function(){
						if($.inArray($(this).val(), fields) != -1){
						var error = "Field <b>"+ $(this).val()+"</b> was selected more than once";
						}
						
						fields.push($(this).val());
						if($(this).val()=="urn"){
							urn=true;
						}
						if($(this).val()=="name"){
							coname=true;
						}
						if($(this).val()=="fullname"||$(this).val()=="firstname"||$(this).val()=="lastname"){
							name=true;
						}
						if($(this).val()=="telephone_Tel"){
							cotel=true;
						}
						if($(this).val()=="telephone_Landline"||
						$(this).val()=="telephone_Mobile"||
						$(this).val()=="telephone_Work"||
						$(this).val()=="telephone_Telephone"){
							tel=true;
						}
					});
					if(ctype=="B2B"&&!coname){
						var error = "You cannot add data to a B2B campaign without a company name";
					}
					if(ctype=="B2C"&&!name){
						var error = "You cannot add data to a B2C campaign without a contact name";
					}
					if(ctype=="B2C"&&!tel){
						var error = "You must add at least one type of contact telephone number for B2C";
					}
					if(ctype=="B2B"){
						if(!cotel){
							if(!tel&&!name){
						var error = "You must add a company telephone number or a contact for a B2B campaign";
							}
						}
					}
					if(urn&&$('#urn-options').val()==1){
							var error = "You cannot use auto increment if you have selected a URN column";
					}
					if($('#urn-options').val()==2&&!urn){
							var error = "You have not selected a URN column. You should use auto increment.";
					}
					if($('#dupe-options').val()==2&&!urn||$('#dupe-options').val()==3&&!urn){
							var error = "Overwriting and updating records is only possible with a URN column";
					}
						
						
						
						if(error){
						flashalert.danger(error);
						} else {
						importer.start_import();
						}
					
				},
				show_progress:function(first){
						$.ajax({
                                url: helper.baseUrl + 'data/get_progress',
                                type: "POST",
                                dataType: "JSON",
								data:{first:first}
                            }).done(function(response) {
                            	if (!response.success&&$('#import').text()!="Stop") {
								$('#import-progress').text(response.progress);
								importer.show_progress();
								} else {
								$('#import-progress').html("<span class='green'>Import was completed</span>");
									flashalert.success("Import was completed");
								}
						});				
				},
				start_import:function(){
					$('#import-progress').text("Preparing data...");
					importer.show_progress(1);
					 $.ajax({
                                url: helper.baseUrl + 'data/start_import',
                                type: "POST",
                                dataType: "JSON",
                                data:  $('#data-form').serialize()+'&filename='+encodeURIComponent($('#filename').text())+'&campaign='+$('#campaign').val()+'&source='+$('#source').val()+'&type='+$('#campaign option:selected').attr('ctype')
                            }).done(function() {
							});
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
				load_step_1:function(){
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
                load_step_3: function() {
                    $('#step-2').slideUp(1000, function() {
                            $.ajax({
                                url: helper.baseUrl + 'data/get_sample',
                                type: "POST",
                                dataType: "JSON",
                                data: {
                                    file: $('#filename').text()
                                }
                            }).fail(function() {
                                $('#step-3').find('#sample-table thead').empty().append("<tr><th>Error</th></tr>");
                                $('#step-3').find('#sample-table tbody').empty().append("<tr><td>The was a problem with the supplied CSV file. Please open it in excel and save as a comma seperated file and try again.</td></tr>");
                                $('#step-3').slideDown(1000);
                            }).done(function(response) {
								$('#import-progress').html('&nbsp;');
                                    var thead, tbody;
                                    $.each(response, function(i, row) {
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
							});
							});
                                },

                                create_field_options: function() {
                                    var ctype = $('#campaign option:selected').attr('ctype');
                                    var camp = $('#campaign').val();
                                    $.ajax({
                                        url: helper.baseUrl + 'data/import_fields',
                                        type: "POST",
                                        dataType: "JSON",
                                        data: {
                                            type: ctype,
                                            campaign: camp
                                        }
                                    }).done(function(response) {
                                        var $select = "<select class='import-field'><option value=''>Select field</option>";
                                        $.each(response, function(table, fields) {
                                            $select += "<optgroup label='" + table + "'>";

                                            $.each(fields, function(column, name) {
                                                $select += "<option value='" + column + "'>" + name + "</option>";
                                            });
                                            $select += "</optgroup>";
                                        });
                                        $select += "</select>";
                                        $('#step-3').find('#sample-table th').each(function(i) {
                                            $(this).append($select);
                                            $(this).find('select').attr('name', 'field[' + i + ']');
                                        });
                                    });
                                }
					}

	
  