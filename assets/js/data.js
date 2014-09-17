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
                                        $('.goto-step-3').show();
                                    }
                                }).on('fileuploaddone', function(e, data) {
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
						if(!cotel||!tel&&!name){
						var error = "You must add a company telephone number or a contact for a B2B campaign";
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
								if(response.progress==0){
								$('#import-progress').text("Preparing data...");
								importer.show_progress();
								}
								if(response.progress>1&&response.progress<100){
								$('#import-progress').text("Importing to database..."+response.progress+"%");	
								importer.show_progress();
								} else if(response.progress==100){
								$('#import-progress').text("Import completed");
								flashalert.success("Import was completed");
								}
						});					 
				},
				start_import:function(){
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

	/*the class below is for the data management page. It gets initialized by the data_management.php view*/
    var data_manager = {
            init: function() {
                $(document).on('change', '#campaign', function(e) {
					if($('#state-select').val()!=""){
                    data_manager.get_user_data();
					}
                });
                $(document).on('change', '#state-select', function(e) {
					if($(this).val()!=""){
                    data_manager.get_user_data();
					}
                });

                $(document).on('click', '#reassign-btn', function(e) {
                    e.preventDefault();
                    data_manager.reassign_data();
                });
                data_manager.load_sliders();
            },
            get_user_data: function() {
                $.ajax({
                    url: helper.baseUrl + 'data/get_user_data',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        campaign: $('#campaign').val(),
                        state: $('#state-select').val()
                    }
                }).done(function(response) {
                    $('#data-stats').show();
                    $('#total-records').text(response.total);
                    $('#assigned-records').text(response.assigned);
                    $('#unassigned-records').text(response.unassigned);
                    $('#parked-records').text(response.parked);
                    if (response.total > 0) {
                        data_manager.load_html(response);
                    } else {
                        $('#sliders').text("No data could be found");
                        $('#reassign-btn').hide();
                    }
                });

            },
            load_html: function(response) {
                $('#sliders').empty();
                if (response.n > 1) {
                    $.each(response.data, function(k, row) {
                        $slider = '<li><input type="text" name="user[' + k + ']" value="' + row.count + '" /><div class="slider" value="' + row.pc + '"/>' + row.name + ': <span class="value"></span>% <span class="pull-right">' + row.count + ' records</span></li>';
                        $('#sliders').append($slider);
                    });
                    $('#reassign-btn').show();
                    data_manager.load_sliders(response.n, response.total);
                } else {
                    $('#sliders').append("Can not continue. There must be at least 2 users assigned to a campaign for the reassignment tool to work.");
                    $('#reassign-btn').hide();
                }

            },
            reassign_data: function() {
                $.ajax({
                    url: helper.baseUrl + 'data/reassign_data',
                    type: "POST",
                    dataType: "JSON",
                    data: $('#data-form').serialize()
                }).done(function(response) {
                    if (response.success) {
                        data_manager.get_user_data();
                        flashalert.success("Records were reassigned");
                    }
                });
            },
            load_sliders: function(n, total_records) {
                var sliders = $("#sliders .slider");
                var availableTotal = 100;

                sliders.each(function() {
                    var init_value = parseInt($(this).attr('value'));
                    $(this).siblings('.value').text($(this).attr('value'));
                    $(this).slider({
                        value: init_value,
                        min: 0,
                        max: 100,
                        range: "max",
                        tooltip: "hide",
                        step: 2,
                        animate: 0
                    }).on('slide', function(event) {
                        // Get current total
                        var gval = event.value;
                        $(this).closest('.slider-horizontal').siblings('.value').text(gval);
                        $(this).closest('li').find('input').val(Math.round(total_records * (gval / 100)));
                        var total = 0;
                        sliders.not(this).each(function() {
                            total += Number($(this).val());
                            $(this).closest('.slider-horizontal').siblings('.value').text($(this).val());
                        });
                        total += gval;
                        var delta = availableTotal - total;

                        // Update each slider
                        sliders.not(this).each(function() {
                            var t = $(this),
                                value = t.val();

                            var num = (Number(n) - 1);
                            var new_value = Math.floor(value + (delta / num));


                            if (new_value < 0 || gval == 100)
                                new_value = 0;
                            if (new_value > 100)
                                new_value = 100;

                            t.closest('li').find('input').val(Math.round(total_records * (new_value / 100)));
                            t.closest('.slider-horizontal').siblings('.value').text(new_value);
                            t.slider('setValue', Number(new_value));
                        });

                    });
                });
				}
            }