// JavaScript Document
/* ==========================================================================
 RECORD DETAILS PAGE
 ========================================================================== */


//when all the ajax requests have finished we run the stretch function to align the panels
$(document).ajaxStop(function () {
      stretch();
  });

var record = {
    init: function(urn, role, campaign, permissions) {
        /* Initialize all the jquery widgets */
        $(".close-alert").click(function() {
            $(this).closest('.alert').addClass('hidden');
            $(this).closest('.alert-text').text('');
        });
        /*initialize the call when the agent calls */
        $(document).on('click', '.startcall', function(e) {
            e.preventDefault();
            window.location.href = $(this).attr('item-url');
        });
		/*initialize the timer when the agent calls */
        $(document).on('click', '.starttimer', function(e) {
            e.preventDefault();
            record.start_call();
        });
        /* Initialize all the panel functions for the record details page */
        this.urn = urn;
        this.role = role;
        this.campaign = campaign;
		this.permissions = permissions;
        this.limit = 6;
		var data = [];
		window.history.pushState(data, "Record Details-"+record.urn, helper.baseUrl+'records/detail/'+record.urn);
    },
    start_call: function(){
        $('#defaultCountdown').countdown('destroy');
        $('#timeropened').css({"color":"#3C0"});
        $('#timerclosed').css({"color":"#3C0"});
        $('#timeropened').fadeIn('slow');
        $('#timerclosed').fadeOut('slow');
        $('#defaultCountdown').countdown({since: 0, compact: true,
                format: 'MS', description: ''});
        var counter = 0; setInterval(function () {
            ++counter;
            if ($('#defaultCountdown').text() >= '00:20') {
                $('#timeropened').css({"color":"#F00"});
                $('#timerclosed').css({"color":"#F00"});
            }
        }, 1000);

        $('.closetimer').click(function(){
            $('#timeropened').fadeOut('slow');
            $('#timerclosed').fadeIn('slow');
        });
        $('.opentimer').click(function(){
            $('#timeropened').fadeIn('slow');
            $('#timerclosed').fadeOut('slow');
        });
        $('.stoptimer').click(function(){
            $('#timeropened').hide();
            $('#timerclosed').hide();
            $('#defaultCountdown').countdown('destroy');
        });
    },
    sticky_note: {
        init: function() {
            /*initialize the save notes button*/
            $(document).on('click', '.save-notes', function(e) {
                e.preventDefault();
                record.sticky_note.save($(this).prev('span'));
            });
        },
        save: function($alert) {
            $.ajax({
                url: helper.baseUrl + 'records/save_notes',
                type: "POST",
                dataType: "JSON",
                data: {
                    'notes': $('.sticky-notes').val(),
                    'urn': record.urn
                }
            }).done(function(response) {
                if (response.success) {
                    flashalert.success('Sticky note was updated');
                } else {
                    flashalert.danger('Notes could not be saved. Please contact support@121customerinsight.co.uk');
                }
            });

        }

    },
    //history panel functions
    history_panel: {
        init: function() {
            record.history_panel.load_panel();

            $(document).on('click', '.show-all-history-btn', function(e) {
                e.preventDefault();
                record.history_panel.show_all_history($(this));
            });
            $(document).on('click', '.close-history-all', function(e) {
                e.preventDefault();
                record.history_panel.close_all_history($(this));
            });
            $(document).on('click', '.edit-history-btn', function(e) {
                e.preventDefault();
                record.history_panel.edit_history($(this).attr('item-id'), $(this).attr('item-modal'));
            });
            $(document).on("click", ".close-history-btn", function(e) {
                e.preventDefault();
                var modal = $(this).attr('item-modal');
                var panel = (modal=="1"?'.history-all-panel':'.history-panel');
                var content = (modal=="1"?'.history-all-content':'.panel-content');
                $(panel).find('.glyphicon-remove').removeClass('glyphicon-remove close-history-btn');
                $(panel).find('form').fadeOut(function() {
                    $(panel).find(content).fadeIn()
                });
            });
            $(document).on("click", ".save-history-btn", function(e) {
                e.preventDefault();
                record.history_panel.update_history($(this).attr('item-modal'));
            });
            $(document).on('click', '.del-history-btn', function(e) {
                e.preventDefault();
                modal.delete_history($(this).attr('item-id'), $(this).attr('item-modal'));
            });
        },
        load_panel: function() {
            $.ajax({
                url: helper.baseUrl + 'ajax/get_history',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    limit: record.limit
                }
            }).done(function(response) {
                if (response.success) {
                    $('.history-panel').find('.panel-content').empty();
                    var $body = "";
                    var $edit_btn = "";
                    var $delete_btn = "";
                    if (response.data.length) {
						//if any outcomes in the history have the "keep" flag the user will keep ownership. This stops answermachines from being taken out of the users pot when they have had a call back Dm previously
						if(response.keep){
							$('#record-update-form').append('<input type="hidden" name="keep" value="1" />');
						}
                        //Use the k var only to know if there are more than x records
                        var k = 0;
                        $.each(response.data, function(i, val) {
                            if(record.permissions['edit history']>0){
                                $edit_btn = '<span class="glyphicon glyphicon-pencil pull-right edit-history-btn" item-modal="0" item-id="' + val.history_id + '"></span>';
                            }
                            if(record.permissions['delete history']>0){
                                $delete_btn = '<span class="glyphicon glyphicon-trash pull-right del-history-btn marl" data-target="#modal" item-modal="0" item-id="' + val.history_id + '" title="Delete history"></span>';
                            }
                            if (k <= record.limit-1) {
                                $body += '<tr><td>' + val.contact + '</td><td>' + val.outcome + '</td><td>' + val.client_name + '</td><td>' + val.comments + '</td><td>'+ $edit_btn +'</td><td>'+ $delete_btn +'</td></tr>';
                            }
                            k++;
                        });
                        if (k > record.limit-1) {
                            $body += '<tr><td colspan="6"><a href="#"><span class="btn pull-right show-all-history-btn marl" data-target="#modal" title="Show All" >Show All</span></a></td></tr>';
                        }
                        $('.history-panel').find('.panel-content').append('<div class="table-responsive"><table class="table table-striped table-condensed table-responsive"><thead><tr><th>Date</th><th>Outcome</th><th>User</th><th>Notes</th><th colspan="2"></th></tr></thead><tbody>' + $body + '</tbody></table></div>');

                    } else {
                        $('.history-panel').find('.panel-content').append('<p>This record has no history information yet</p>');
                    }
                }
						;
            });
	
        },
        show_all_history: function(btn) {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop all-history in"></div>').appendTo(document.body).hide().fadeIn();
            $('.history-all-container').find('.attachment-all-panel').show();
            $('.history-all-content').show();
            $('.history-all-container').fadeIn()
            $('.history-all-container').animate({
                width: '600px',
                left: moveto,
                top: '10%'
            }, 1000);
            record.history_panel.load_all_history_panel();
        },
        load_all_history_panel: function() {
            //Get attachment data
            $.ajax({
                url: helper.baseUrl + "ajax/get_history",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function(response) {
                var $thead = $('.history-all-table').find('thead');
                $thead.empty();

                var $tbody = $('.history-all-table').find('tbody');
                $tbody.empty();
                var $body = "";
                var $edit_btn = "";
                var $delete_btn = "";

                if (response.data.length > 0) {
                    $.each(response.data, function(i, val) {
                        if(record.permissions['edit history']>0){
                            $edit_btn = '<span class="glyphicon glyphicon-pencil pull-right edit-history-btn" item-modal="1" item-id="' + val.history_id + '"></span>';
                        }
                        if(record.permissions['delete history']>0){
                            $delete_btn = '<span class="glyphicon glyphicon-trash pull-right del-history-btn marl" data-target="#modal" item-modal="1" item-id="' + val.history_id + '" title="Delete history"></span>';
                        }
                        $body += '<tr><td>' + val.contact + '</td><td>' + val.outcome + '</td><td>' + val.client_name + '</td><td>' + val.comments + '</td><td>' + $edit_btn + '</td><td>' + $delete_btn + '</td></tr>';
                    });
                    $thead.append('<tr><th>Name</th><th>Date</th><th>Outcome</th><th>User</th><th>Notes</th><th colspan="2"></th></tr>');
                    $tbody.append($body);
                } else {
                    $tbody.append('<p>This record has no history information yet</p>');
                }
            });
        },
        close_all_history: function() {
            $('.modal-backdrop.all-history').fadeOut();
            $('.history-container').fadeOut(500, function() {
                $('.history-content').show();
                $('.history-select-form')[0].reset();
                $('.alert').addClass('hidden');
            });
            $('.history-all-container').fadeOut(500, function() {
                $('.history-all-content').show();
            });
        },
        edit_history: function(id, modal) {
            var panel = (modal=="1"?'.history-all-panel':'.history-panel');
            var content = (modal=="1"?'.history-all-content':'.panel-content');
            $(panel).find(content).fadeOut(function() {
                $.ajax({
                    url: helper.baseUrl + "ajax/get_history_by_id",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    record.history_panel.load_history_form(response.data,response.outcomes, response.progress_list, id, modal);
                });
                $(panel).find('form').fadeIn();
            });

        },
        load_history_form: function(data,outcomes, progress_list, id, modal) {
            var panel = (modal=="1"?'.history-all-panel':'.history-panel');
            var $form = $(panel).find('form');
            $form.empty();
            $form.append("<input type='hidden' name='history_id' value='" + id + "'/>");
            var form = "";
            var $select_outcome = "<option value=''>No action required</option>";
            var $select_progress = "<option value=''>No action required</option>";

            var disabled = (record.role == 1?"":"disabled");

            $.each(outcomes, function(key, outcome) {
                var selected = "";
                if (data.outcome == outcome['name']) {
                    selected = "selected";
                }
                $select_outcome += "<option " + selected + " value='" + outcome['id'] + "'>" + outcome['name'] + "</option>";
            });

            $.each(progress_list, function(key, progress) {
                var selected = "";
                if (data.progress == progress['name']) {
                    selected = "selected";
                }
                $select_progress += "<option " + selected + " value='" + progress['id'] + "'>" + progress['name'] + "</option>";
            });
            date_input = "<div class='form-group input-group-sm'><p>"+data.contact+"</p></div>";
            user_input = "<div class='form-group input-group-sm'><p>"+data.name+"</p></div>";
            outcome_input = (data.outcome_id)?"<div class='form-group input-group-sm'><p>Outcome</p><select name='outcome_id' class='selectpicker_outcome'  " + disabled  + ">" + $select_outcome + "</select></div>":"";
            progress_input = (data.progress_id)?"<div class='form-group input-group-sm'><p>Progress</p><select name='progress_id' class='selectpicker_progress'  " + disabled  + ">" + $select_progress + "</select></div>":"";
            comments_input = "<div class='form-group input-group-sm'><p>Comments</p><textarea class='form-control ' placeholder='Enter the comments here' rows='3' name='comments'>" + data.comments + "</textarea></div>";

            form += date_input +
                    user_input +
                    outcome_input +
                    progress_input +
                    comments_input;


            $form.append(form + "<button class='btn btn-primary pull-right marl save-history-btn' item-modal='"+ modal +"'>Save</button> <button class='btn btn-default pull-right close-history-btn' item-modal='"+ modal +"'>Cancel</button>");
            $('.selectpicker_outcome').selectpicker();
            $('.selectpicker_progress').selectpicker();
        },
        update_history: function(modal) {
            var panel = (modal=="1"?'.history-all-panel':'.history-panel');
            var content = (modal=="1"?'.history-all-content':'.panel-content');
            $.ajax({
                url: helper.baseUrl + "ajax/update_history",
                type: "POST",
                dataType: "JSON",
                data: $(panel).find('form').serialize()
            }).done(function(response) {
                if (modal=="1") {
                    record.history_panel.load_all_history_panel();
                }
                record.history_panel.load_panel();
                $(panel).find('form').fadeOut(function() {
                    $(panel).find(content).fadeIn()
                });
                flashalert.success(response.msg);
				
            });
        },
        remove_history: function(history_id,modal) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_history',
                type: "POST",
                dataType: "JSON",
                data: {history_id: history_id}
            }).done(function(response) {
                if (response.success) {
                    record.history_panel.load_panel();
                    if (modal == 1){
                        record.history_panel.load_all_history_panel();
                    }
                    flashalert.success("History was deleted from the history");
                };
            });
        }
    },
    //update panel functions
    update_panel: {
        init: function() {
            $(document).off('click', '.update-record');
            $(document).off('click', '.reset-record');
            $(document).off('click', '.favorite-btn');
            $(document).off('click', '.urgent-btn');
            $(document).off('click', '.close-xfer');
            $(document).off('click', '.set-xfer');
			
            /*initialize the save notes button*/
            $(document).on('click', '.update-record', function(e) {
                e.preventDefault();
				if($('.outcomepicker').val().length>0){
				if($('.outcomepicker').val()=="4"&&$('.history-panel').find('tbody tr').length>0){
				modal.dead_line($(this));	
				} else {
                record.update_panel.save($(this));
				}
				} else {
				flashalert.danger("You must select a call outcome first");
				}
            });
            $(document).on('click', '.reset-record', function(e) {
                e.preventDefault();
                record.update_panel.reset_record($(this));
            });
            $(document).on('click', '.favorite-btn', function(e) {
                record.update_panel.set_favorite($(this));
            });
            $(document).on('click', '.urgent-btn', function(e) {
                record.update_panel.set_urgent($(this));
            });
            $(document).on('click', '.close-xfer', function(e) {
                e.preventDefault();
                record.update_panel.close_cross_transfer();
            });
            $(document).on('click', '.set-xfer', function(e) {
                e.preventDefault();
                var xfer = $('select[name="campaign"]').find('option:selected').text()
                $('#record-update-form').append($('<input name="xfer_campaign" type="hidden"/>').val($('select[name="campaign"]').val()));
                $('div.outcomepicker').find('.filter-option').text('Cross Transer: ' + xfer);
                record.update_panel.close_cross_transfer();
            });
            var old_outcome = $('.outcomepicker option:selected').val();
            var current_outcome = old_outcome;
            $(document).on('change', '.outcomepicker', function(e) {
                e.preventDefault();
                $val = $(this).val();
                if ($val == 71) {
                    record.update_panel.cross_transfer();
                } else {
                    $('input[name="xfer_campaign"]').remove();
                }
                $delay = $('#outcomes').find("option[value='" + $val + "']").attr('delay');
                //if the selected option has a delay attribute we disable the nextcall and set it as now+the amount of delay. This is for outcomes such as answer machine to give us more control over when agents should try again
                if ($delay > 0) {
					var today = new Date();
                    var nextcall = new Date().addHours($delay);
					var hour = nextcall.getHours();
					console.log(hour);
					if(hour>16){
					var nextcall = moment(today).add(1, 'days').toDate();
					}

                    $('#nextcall').val(timestamp_to_uk(nextcall, true));
                    $('#nextcall').datetimepicker({
                        format: 'DD/MM/YYYY HH:mm'
                    });
					$('#nextcall').animate({backgroundColor:"#99FF99"},500).delay(300).animate({backgroundColor:"#FFFFFF"},500);
                    //$('#nextcall').data("DateTimePicker").setDate(timestamp_to_uk(nextcall,true));
                }
                //If the previous outcome had delay, set the date to the old_nextcall
                else if ($('#outcomes').find("option[value='" + current_outcome + "']").attr('delay')){
                    nextcall = old_nextcall;
                    $('#nextcall').val(nextcall);
                    $('#nextcall').datetimepicker({
                        format: 'DD/MM/YYYY HH:mm'
                    });
                   $('#nextcall').animate({backgroundColor:"#99FF99"},500).delay(300).animate({backgroundColor:"#FFFFFF"},500);
                }

                //Set the new outcome
                current_outcome = $('.outcomepicker option:selected').val();

                var outcome = $('.outcomepicker option:selected').val();
                var new_nextcall = $('input[name="nextcall"]').val();
                var comments = $('textarea[name="comments"]').val();
                record.update_panel.disabled_btn(old_outcome, outcome,old_nextcall,new_nextcall,old_comments,comments);
            });

            var old_nextcall = $('input[name="nextcall"]').val();
            var datetimepicker = $('.datetime');
            datetimepicker.off("dp.hide");
            datetimepicker.on("dp.hide",function (e) {
                var new_nextcall = $('input[name="nextcall"]').val();
                var outcome = $('.outcomepicker option:selected').val();
                var comments = $('textarea[name="comments"]').val();
                record.update_panel.disabled_btn(old_outcome, outcome,old_nextcall,new_nextcall,old_comments,comments);
            });

            var old_comments = $('textarea[name="comments"]').val();
            $('textarea[name="comments"]').bind('input propertychange', function() {
                var new_nextcall = $('input[name="nextcall"]').val();
                var outcome = $('.outcomepicker option:selected').val();
                var comments = $('textarea[name="comments"]').val();
                record.update_panel.disabled_btn(old_outcome, outcome,old_nextcall,new_nextcall,old_comments,comments);
            });
        },
        disabled_btn: function(old_outcome,outcome, old_nextcall, nextcall, old_comments, comments) {
            if (((outcome.length != 0)&&(outcome != old_outcome)) || ((nextcall.length != 0)&&(nextcall != old_nextcall)) || ((comments.length != 0)&&(comments != old_comments))) {
                $('.update-record').prop('disabled', false);
            }
            else {
                $('.update-record').prop('disabled', true);
            }
        },
        cross_transfer: function() {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop in"></div>').appendTo(document.body).hide().fadeIn();
            $('.xfer-container').find('.edit-panel').show()
            $('.xfer-container').fadeIn()
            $('.xfer-container').animate({
                width: '500px',
                left: moveto,
                top: '10%'
            }, 1000);
        },
        close_cross_transfer: function() {
            $('.modal-backdrop').fadeOut();
            $('.xfer-container').fadeOut(500, function() {});
        },
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/update',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize(),
                beforeSend: function() {
                    //$btn.hide()
                    //$btn.closest('div').append("<img class='update-loader pull-right' src='" + helper.baseUrl + "assets/img/ajax-load-black.gif' />");
                }
            }).done(function(response) {
                if (response.success) {
                    $('.last-update').text('Last Updated: Just Now');
                    record.history_panel.load_panel();
                    record.ownership_panel.load_panel();
					check_session();	
					$(document).off('click','.nav-btn');
                    flashalert.success(response.msg);
					if(response.email_trigger){
                        $.ajax({url:helper.baseUrl+'email/trigger_email',
                        type:"POST",
                        data:{urn:record.urn}
                        });
					}
                    record.update_panel.init();
					$('textarea[name="comments"]').val('');
                    $('.update-record').prop('disabled', true);
                } else {
                    flashalert.warning(response.msg);
                }
                $btn.show();
                $('.update-loader').remove();
            });
        },
        set_favorite: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/set_favorites',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    action: $btn.attr('action')
                }
            }).done(function(response) {
                if (response.added) {
                    $btn.html('<span class="glyphicon glyphicon-star"></span> Remove from favourites').attr("action", "remove").children('span').css('color', 'yellow');
                } else {
                    $btn.html('<span class="glyphicon glyphicon-star-empty"></span> Add to favourites').attr("action", "add").children('span').css('color', 'black');
                }
                flashalert.success(response.msg);
            });
        },
        set_urgent: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/set_urgent',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    action: $btn.attr('action')
                }
            }).done(function(response) {
                if (response.added) {
                    $btn.html('<span class="glyphicon glyphicon-flag red"></span> Unflag as urgent').attr("action", "remove");
                    //$('#progress').selectpicker('val','1').selectpicker('render');
                } else {
                    $btn.html('<span class="glyphicon glyphicon-flag"></span> Flag as urgent').attr("action", "add");
                }
                flashalert.success(response.msg);
            });
        },
        reset_record: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/reset_record',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function(response) {
                if (response.success) {
                    $btn.closest('form').find('textarea').val('').css('color', '#555');
                    $btn.closest('form').find('.selectpicker').selectpicker('val', '').selectpicker('render');
                    flashalert.success(response.msg);
                    location.reload();
                } else {
                    flashalert.danger(response.msg);
                }
            });
        }
    },
    //contact_panel_functions
    contact_panel: {
        init: function() {
            this.config = {
                panel: '.contact-panel'
            };
            /*initialize the add contact button*/
            $(document).on('click', '.add-contact-btn', function(e) {
                e.preventDefault();
                record.contact_panel.add_form();
            });
            /* initialize the edit contact buttons */
            $(document).on('click', '.edit-contact-btn', function(e) {
                e.preventDefault();
                record.contact_panel.edit_form($(this).attr('item-id'));
            });
            /* initialize the delete contact buttons */
            $(document).on('click', '.del-contact-btn', function(e) {
                e.preventDefault();
                modal.delete_contact($(this).attr('item-id'));
            });
            /* initialize the close contact buttons */
            $(document).on('click', '.close-contact-btn', function(e) {
                e.preventDefault();
                record.contact_panel.close_panel();
            });
            /*initialize the save contact button*/
            $(document).on('click', '.save-contact-general', function(e) {
                e.preventDefault();
                record.contact_panel.save_contact($(this));
            });
            /*initialize the add item button for phone or address*/
            $(document).on('click', '.contact-add-item', function(e) {
                e.preventDefault();
                record.contact_panel.new_item_form($(this));
            });
            /*initialize the edit item buttons for phone or address*/
            $(document).on('click', '.contact-item-btn', function(e) {
                e.preventDefault();
                record.contact_panel.edit_item_form($(this));
            });
            /*initialize the delete item buttons for phone or address*/
            $(document).on('click', '.del-item-btn', function(e) {
                e.preventDefault();
                record.contact_panel.delete_item($(this));

            });
            /*save the new phone or address*/
            $(document).on('click', '.save-contact-phone,.save-contact-address', function(e) {
                e.preventDefault();
                record.contact_panel.save_item($(this));
            });
            /*initialize the cancel button on the add/edit contact phone/address form*/
            $(document).on('click', '.hide-item-form', function(e) {
                e.preventDefault();
                $tab = $(this).closest('.tab-pane');
                $tab.find('form').hide();
                $tab.find('.table-container').show();
            });



        },
        save_item: function($btn) {
            var id = $btn.attr('item-id');
            $.ajax({
                url: helper.baseUrl + 'ajax/' + $btn.attr('action'),
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                record.contact_panel.load_tabs(response.id, response.type);
                record.contact_panel.load_panel(record.urn, response.id);
            });
        },
        delete_item: function($btn) {
            var id = $btn.attr('item-id');
            contact = $btn.closest('.tab-pane').find('input[name="contact_id"]').val();
            $.ajax({
                url: helper.baseUrl + 'ajax/' + $btn.attr('action'),
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    contact: contact
                }
            }).done(function(response) {
                record.contact_panel.load_tabs(response.id, response.type);
                record.contact_panel.load_panel(record.urn,response.id);
            });
        },
        new_item_form: function($btn) {
            $tab = $btn.closest('.tab-pane');
            $tab.find('.table-container').hide();
            $tab.find('form')[0].reset();
            $tab.find('form').show();
            $tab.find('.close-contact-btn').removeClass('close-contact-btn').addClass('hide-item-form');
            $tab.find('.save-contact-phone').attr('action', 'add_phone');
            $tab.find('.save-contact-address').attr('action', 'add_address');
            //reset the item id
            $tab.find('.item-id').val('');

        },
        edit_item_form: function($btn) {
            id = $btn.attr('item-id');
            var action = $btn.attr('action');
            $tab = $btn.closest('.tab-pane');
            $tab.find('.item-id').val(id);
            if (action == "edit_address") {
                page = "get_contact_address";
            } else if (action == "edit_phone") {
                page = "get_contact_number";
            }
            $.ajax({
                url: helper.baseUrl + 'ajax/' + page,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                $.each(response, function(key, val) {
                    $tab.find('form input[name="' + key + '"]').val(val);
                    $tab.find('select[name="' + key + '"]').selectpicker('val', val);
                });
                $tab.find('.table-container').hide();
                $tab.find('form').show();
            });
            $tab.find('.close-contact-btn').removeClass('close-contact-btn').addClass('hide-item-form');
            $tab.find('.save-contact-phone').attr('action', 'edit_phone');
            $tab.find('.save-contact-address').attr('action', 'edit_address');
        },
        load_panel: function(urn, id) {
            var $panel = $(record.contact_panel.config.panel);
            $.ajax({
                url: helper.baseUrl + 'ajax/get_contacts',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn
                }
            }).done(function(response) {
                $panel.find('.contacts-list').empty();
                $.each(response.data, function(key, val) {
                    var show = "";
                    var collapse = "collapsed"
                    if (key == id) {
                        show = "in";
                        collapse = ""
                    }
                    var $contact_detail_list_items = "",
                        $contact_detail_telephone_items = "";
                    $address = "";
                    $postcode = "";
                    $.each(val.visible, function(dt, dd) {
                        if (dd && dd != '' && dt != 'Address') {
                            $contact_detail_list_items += "<dt>" + dt + "</dt><dd>" + dd + "</dd>";
                        } else if (dd && dd != '' && dt == 'Address') {
                            $.each(dd, function(key, val) {
                                if (val) {
                                    $address += val + "</br>";
                                    $postcode = dd.postcode;
                                }
                            });
                            $contact_detail_list_items += "<dt>" + dt + "</dt><dd><a class='pull-right pointer' target='_blank' href='https://maps.google.com/maps?q=" + $postcode + ",+UK'><span class='glyphicon glyphicon-map-marker'></span> Map</a>" + $address + "</dd>";
                        }

                    });
                    $.each(val.telephone, function(dt, tel) {
                        if (tel.tel_name) {
                            $contact_detail_telephone_items += "<dt>" + tel.tel_name + "</dt><dd><a href='#' class='startcall' item-url='callto:" + tel.tel_num + "'>" + tel.tel_num + "</a></dd>";
                        }
                    });
                    $panel.find('.contacts-list').append($('<li/>').addClass('list-group-item').attr('item-id', key)
                        .append($('<a/>').attr('href', '#collapse-' + key).attr('data-parent', '#accordian').attr('data-toggle', 'collapse').text(val.name.fullname).addClass(collapse))
                        .append($('<span/>').addClass('glyphicon glyphicon-trash pull-right del-contact-btn').attr('item-id', key).attr('data-target', '#model'))
                        .append($('<span/>').addClass('glyphicon glyphicon-pencil pull-right edit-contact-btn').attr('item-id', key))
                        .append($('<div/>').attr('id', 'collapse-' + key).addClass('panel-collapse collapse ' + show)
                            .append($('<dl/>').addClass('dl-horizontal contact-detail-list').append($contact_detail_list_items).append($contact_detail_telephone_items))
                        )

                    );
                });

            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_contact',
                type: "POST",
                dataType: "JSON",
                data: {
                    contact: id
                }
            }).done(function(response) {
                if (response.success) {
                    $('.contacts-list').find('li[item-id="' + id + '"]').remove();
                    flashalert.warning("Contact was deleted");
                };
            });
        },
        animate_panel: function() {
            var $panel = $(record.contact_panel.config.panel);
            var width = $panel.css('width');
            $('<div class="modal-backdrop in"></div>').appendTo(document.body).hide().fadeIn();
            $panel.css('position', 'fixed').css('z-index', '99999').css('width', width);
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $panel.animate({
                width: '500px',
                left: moveto,
                top: '50px'
            }, 1000);
        },
        add_form: function() {
            var $panel = $(record.contact_panel.config.panel);
            $('.tab[href="#general"]').tab('show');

            $panel.find('.panel-title span').removeClass('glyphicon-plus add-contact-btn').addClass('glyphicon-remove close-contact-btn');
            $panel.find('form').each(function() {
                $(this)[0].reset();
                $(this).show();
                $(this).find('input[name="contact_id"]').val('');
            });
            $panel.find('.phone-tab,.address-tab').hide();
            $panel.find('.tab-alert').show();
            $panel.find('.table-container').hide();
            record.contact_panel.animate_panel();
            $panel.find('.list-group').fadeOut(1000, function() {
                $panel.find('.form-container').fadeIn(1000).find('.save-contact-general').attr('action', 'add_contact');
            });
        },
        edit_form: function(id) {
            var $panel = $(record.contact_panel.config.panel);
            $('.tab[href="#general"]').tab('show');
            $panel.find('.tab-alert').hide();
            $panel.find('tbody').empty();
            $panel.find('.phone-tab,.address-tab').show();
            $panel.find('input[name="contact_id"]').each(function() {
                $(this).val(id);
            });
            record.contact_panel.load_tabs(id);
            $panel.find('.panel-title span').removeClass('glyphicon-plus add-contact-btn').addClass('glyphicon-remove close-contact-btn');
            record.contact_panel.animate_panel();
            $panel.find('.list-group').fadeOut(1000, function() {
                $panel.find('.form-container').fadeIn(1000).find('.save-contact-general').attr('action', 'save_contact');
            });
        },
        save_contact: function($btn) {
            var action = $btn.attr('action');
            var $form = $btn.closest('form');
            var $alert = $btn.prev('span');
            $.ajax({
                url: helper.baseUrl + "ajax/" + action,
                type: "POST",
                dataType: "JSON",
                data: $form.serialize()
            }).done(function(response) {
                flashalert.success("Contact details saved");
                //change the add box to an edit box
                if (action == "add_contact") {
                    $btn.attr('action', 'save_contact');
                    $form.closest('.form-container').find('input[name="contact_id"]').val(response.id);
                    $('.phone-tab,.address-tab').show();
                    $('.tab-alert').hide();
                }
                record.contact_panel.load_panel(record.urn, response.id);
            });
        },
        close_panel: function() {
            var $panel = $(record.contact_panel.config.panel);
            $panel.find('.form-container').fadeOut(500, function() {
                $panel.removeAttr('style');
                $panel.find('.list-group').fadeIn(500);
                $('.modal-backdrop').fadeOut();
            })
            $panel.find('.panel-title span').removeClass('glyphicon-remove close-contact-btn').addClass('glyphicon-plus add-contact-btn');
        },
        load_tabs: function(contact, item_form) {
            var $panel = $(record.contact_panel.config.panel);
            if (item_form) {
                $panel.find('#' + item_form + ' form').hide();
                $panel.find('#' + item_form + ' .table-container').show();
            } else {
                $panel.find('#phone form, #address form').hide();
                $panel.find('#phone .table-container,#address .table-container').show();
            }
            $.ajax({
                url: helper.baseUrl + "ajax/get_contact",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: contact
                }
            }).done(function(response) {
                if (response.success) {
                    $.each(response.data.general, function(key, val) {
                        $panel.find('#general input[name="' + key + '"]').val(val);
                    });

                    if (response.data.telephone) {
                        $panel.find('#phone tbody').empty();
                        $panel.find('#phone .table-container,#phone .table-container table').show();
                        $panel.find('#phone .none-found').hide();
                        $.each(response.data.telephone, function(key, val) {
                            if (val.tel_tps == "0") {
                                var $tps = "<span style='color:green' class='glyphicon glyphicon-ok-sign tt'  data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                            } else if (val.tel_tps == "1") {
                                var $tps = "<span style='color:red' class='glyphicon glyphicon-exclamation-sign tt'  data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                            } else {
                                var $tps = "<span class='glyphicon glyphicon-question-sign tt'  data-toggle='tooltip' data-placement='right' title='TPS Status is unknown'></span>"
                            }
                            $phone = "<tr><td>" + val.tel_name + "</td><td>" + val.tel_num + "</td><td>" + $tps + "</td><td><span class='glyphicon glyphicon-trash pull-right del-item-btn' action='delete_phone' item-id='" + val.tel_id + "'></span><span class='glyphicon glyphicon-pencil pull-right contact-item-btn' action='edit_phone' item-id='" + val.tel_id + "'></span></td></tr>";
                            $panel.find('#phone tbody').append($phone);
                        });
                    } else {
                        $panel.find('#phone .table-container table').hide();
                        $panel.find('#phone .none-found').show();
                    }
                    if (response.data.address) {
                        $panel.find('#address tbody').empty();
                        $panel.find('#address .table-container, #address .table-container table').show();
                        $panel.find('#address .none-found').hide();
                        $.each(response.data.address, function(key, val) {
                            if (val.primary == 1) {
                                var $primary = "<span class='glyphicon glyphicon-ok-sign'></span>";
                            } else {
                                $primary = "";
                            }
                            $address = "<tr><td>" + val.add1 + "</td><td>" + val.postcode + "</td><td>" + $primary + "</td><td><span class='glyphicon glyphicon-trash pull-right del-item-btn' action='delete_address' item-id='" + val.address_id + "'></span><span class='glyphicon glyphicon-pencil pull-right contact-item-btn' action='edit_address' item-id='" + val.address_id + "'></span></td></tr>"
                            $panel.find('#address tbody').append($address);
                        });
                    } else {
                        $panel.find('#address .table-container table').hide();
                        $panel.find('#address .none-found').show();
                    }
                }
                $('.tt').tooltip();

            });
        }

    },
    //contact_panel_functions
    company_panel: {
        init: function() {
            this.config = {
                panel: '.company-panel'
            };
            /*initialize the add company button*/
            $(document).on('click', '.add-company-btn', function(e) {
                e.preventDefault();
                record.company_panel.add_form();
            });
            /* initialize the edit company buttons */
            $(document).on('click', '.edit-company-btn', function(e) {
                e.preventDefault();
                record.company_panel.edit_form($(this).attr('item-id'));
            });
            /* initialize the delete company buttons */
            $(document).on('click', '.del-company-btn', function(e) {
                e.preventDefault();
                modal.delete_company($(this).attr('item-id'));
            });
            /* initialize the close company buttons */
            $(document).on('click', '.close-company-btn', function(e) {
                e.preventDefault();
                record.company_panel.close_panel();
            });
            /*initialize the save company button*/
            $(document).on('click', '.save-company-general', function(e) {
                e.preventDefault();
                record.company_panel.save_company($(this));
            });
            /*initialize the add item button for phone or address*/
            $(document).on('click', '.company-add-item', function(e) {
                e.preventDefault();
                record.company_panel.new_item_form($(this));
            });
            /*initialize the edit item buttons for phone or address*/
            $(document).on('click', '.company-item-btn', function(e) {
                e.preventDefault();
                record.company_panel.edit_item_form($(this));
            });
            /*initialize the delete item buttons for phone or address*/
            $(document).on('click', '.del-item-btn', function(e) {
                e.preventDefault();
                record.company_panel.delete_item($(this));

            });
            /*save the new phone or address*/
            $(document).on('click', '.save-company-phone,.save-company-address', function(e) {
                e.preventDefault();
                record.company_panel.save_item($(this));
            });
            /*initialize the cancel button on the add/edit company phone/address form*/
            $(document).on('click', '.hide-item-form', function(e) {
                e.preventDefault();
                $tab = $(this).closest('.tab-pane');
                $tab.find('form').hide();
                $tab.find('.table-container').show();
            });


        },
        save_item: function($btn) {
            var id = $btn.attr('item-id');
            $.ajax({
                url: helper.baseUrl + 'ajax/' + $btn.attr('action'),
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
                record.company_panel.load_tabs(response.id, response.type);
                record.company_panel.load_panel(record.urn, response.id);
            });
        },
        delete_item: function($btn) {
            var id = $btn.attr('item-id');
            company = $btn.closest('.tab-pane').find('input[name="company_id"]').val();
            $.ajax({
                url: helper.baseUrl + 'ajax/' + $btn.attr('action'),
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    company: company
                }
            }).done(function(response) {
                record.company_panel.load_tabs(response.id, response.type);
                record.company_panel.load_panel();
            });
        },
        new_item_form: function($btn) {
            $tab = $btn.closest('.tab-pane');
            $tab.find('.table-container').hide();
            $tab.find('form')[0].reset();
            $tab.find('form').show();
            $tab.find('.close-company-btn').removeClass('close-company-btn').addClass('hide-item-form');
            $tab.find('.save-company-phone').attr('action', 'add_cophone');
            $tab.find('.save-company-address').attr('action', 'add_coaddress');
            //reset the item id
            $tab.find('.item-id').val('');

        },
        edit_item_form: function($btn) {
            id = $btn.attr('item-id');
            var action = $btn.attr('action');
            $tab = $btn.closest('.tab-pane');
            $tab.find('.item-id').val(id);
            if (action == "edit_coaddress") {
                page = "get_company_address";
            } else if (action == "edit_cophone") {
                page = "get_company_number";
            }
            $.ajax({
                url: helper.baseUrl + 'ajax/' + page,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                $.each(response, function(key, val) {
                    $tab.find('form input[name="' + key + '"]').val(val);
                    $tab.find('select[name="' + key + '"]').selectpicker('val', val);
                });
                $tab.find('.table-container').hide();
                $tab.find('form').show();
            });
            $tab.find('.close-company-btn').removeClass('close-company-btn').addClass('hide-item-form');
            $tab.find('.save-company-phone').attr('action', 'edit_cophone');
            $tab.find('.save-company-address').attr('action', 'edit_coaddress');
        },
        load_panel: function(urn, id) {
            var $panel = $(record.company_panel.config.panel);
            $.ajax({
                url: helper.baseUrl + 'ajax/get_companies',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn
                }
            }).done(function(response) {
                $panel.find('.company-list').empty();
                $.each(response.data, function(key, val) {
                    var show = "";
                    var collapse = "collapsed"
                    if (key == id) {
                        show = "in";
                        collapse = "";
                    }
                    var $company_detail_list_items = "",
                        $company_detail_telephone_items = "";
                    $address = "";
                    $postcode = "";
                    $.each(val.visible, function(dt, dd) {
                        if (dd && dd != '' && dt != 'Address' && dt != 'Company') {
                            $company_detail_telephone_items += "<dt>" + dt + "</dt><dd>" + dd + "</dd>";
                        } else if (dd && dd != '' && dt == 'Address') {
                            $.each(dd, function(key, val) {
                                if (val&&val!='') {
                                    $address += val + "</br>";
                                    $postcode = dd.postcode;
                                }
                            });
							
                            $company_detail_list_items += "<dt>" + dt + "</dt><dd><a class='pull-right pointer' target='_blank' href='https://maps.google.com/maps?q=" + $postcode + ",+UK'><span class='glyphicon glyphicon-map-marker'></span> Map</a>" + $address + "</dd>";
                        }
                    });
                    $.each(val.telephone, function(dt, tel) {
                        if (tel.tel_name) {
                            $company_detail_telephone_items += "<dt>" + tel.tel_name + "</dt><dd><a href='#' class='startcall' item-url='callto:" + tel.tel_num + "'>" + tel.tel_num + "</a></dd>";
                        }
                    });
                    $panel.find('.company-list').append($('<li/>').addClass('list-group-item').attr('item-id', key)
                        .append($('<a/>').attr('href', '#com-collapse-' + key).attr('data-parent', '#accordian').attr('data-toggle', 'collapse').text(val.visible['Company']).addClass(collapse))
                        .append($('<span/>').addClass('glyphicon glyphicon-trash pull-right del-company-btn').attr('item-id', key).attr('data-target', '#model'))
                        .append($('<span/>').addClass('glyphicon glyphicon-pencil pull-right edit-company-btn').attr('item-id', key))
                        .append($('<div/>').attr('id', 'collapse-' + key).addClass('panel-collapse collapse ' + show)
                            .append($('<dl/>').addClass('dl-horizontal company-detail-list').append($company_detail_list_items).append($company_detail_telephone_items))
                        )

                    );
                });

            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_company',
                type: "POST",
                dataType: "JSON",
                data: {
                    company: id
                }
            }).done(function(response) {
                if (response.success) {
                    $('.company-list').find('li[item-id="' + id + '"]').remove();
                    flashalert.warning("company was deleted");
                };
            });
        },
        animate_panel: function() {
            var $panel = $(record.company_panel.config.panel);
            var width = $panel.css('width');
            $('<div class="modal-backdrop in"></div>').appendTo(document.body).hide().fadeIn();
            $panel.css('position', 'fixed').css('z-index', '99999').css('width', width);
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $panel.animate({
                width: '500px',
                left: moveto,
                top: '50px'
            }, 1000);
        },
        add_form: function() {
            var $panel = $(record.company_panel.config.panel);
            $('.tab[href="#general"]').tab('show');

            $panel.find('.panel-title span').removeClass('glyphicon-plus add-company-btn').addClass('glyphicon-remove close-company-btn');
            $panel.find('form').each(function() {
                $(this)[0].reset();
                $(this).show();
                $(this).find('input[name="contact_id"]').val('');
            });
            $panel.find('.phone-tab,.address-tab').hide();
            $panel.find('.tab-alert').show();
            $panel.find('.table-container').hide();
            record.company_panel.animate_panel();
            $panel.find('.list-group').fadeOut(1000, function() {
                $panel.find('.form-container').fadeIn(1000).find('.save-company-general').attr('action', 'add_company');
            });
        },
        edit_form: function(id) {
            var $panel = $(record.company_panel.config.panel);
            $('.tab[href="#general"]').tab('show');
            $panel.find('.tab-alert').hide();
            $panel.find('tbody').empty();
            $panel.find('.phone-tab,.address-tab').show();
            $panel.find('input[name="company_id"]').each(function() {
                $(this).val(id);
            });
            record.company_panel.load_tabs(id);
            $panel.find('.panel-title span').removeClass('glyphicon-plus add-company-btn').addClass('glyphicon-remove close-company-btn');
            record.company_panel.animate_panel();
            $panel.find('.list-group').fadeOut(1000, function() {
                $panel.find('.form-container').fadeIn(1000).find('.save-company-general').attr('action', 'save_company');
            });
        },
        save_company: function($btn) {
            var action = $btn.attr('action');
            var $form = $btn.closest('form');
            var $alert = $btn.prev('span');
            $.ajax({
                url: helper.baseUrl + "ajax/" + action,
                type: "POST",
                dataType: "JSON",
                data: $form.serialize()
            }).done(function(response) {
                flashalert.success("company details saved");
                //change the add box to an edit box
                if (action == "add_company") {
                    $btn.attr('action', 'save_company');
                    $form.closest('.form-container').find('input[name="company_id"]').val(response.id);
                    $('.phone-tab,.address-tab').show();
                    $('.tab-alert').hide();
                }
                record.company_panel.load_panel(record.urn, response.id);
            });
        },
        close_panel: function() {
            var $panel = $(record.company_panel.config.panel);
            $panel.find('.form-container').fadeOut(500, function() {
                $panel.removeAttr('style');
                $panel.find('.list-group').fadeIn(500);
                $('.modal-backdrop').fadeOut();
            })
            $panel.find('.panel-title span').removeClass('glyphicon-remove close-company-btn').addClass('glyphicon-plus add-company-btn');
        },
        load_tabs: function(company, item_form) {
            var $panel = $(record.company_panel.config.panel);
            if (item_form) {
                $panel.find('#' + item_form + ' form').hide();
                $panel.find('#' + item_form + ' .table-container').show();
            } else {
                $panel.find('#cophone form, #coaddress form').hide();
                $panel.find('#cophone .table-container,#coaddress .table-container').show();
            }
            $.ajax({
                url: helper.baseUrl + "ajax/get_company",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: company
                }
            }).done(function(response) {
                if (response.success) {
                    $.each(response.data.general, function(key, val) {
                        $panel.find('#cogeneral input[name="' + key + '"]').val(val);
                    });

                    if (response.data.telephone) {
                        $panel.find('#cophone tbody').empty();
                        $panel.find('#cophone .table-container,#cophone .table-container table').show();
                        $panel.find('#cophone .none-found').hide();
                        $.each(response.data.telephone, function(key, val) {
                            if (val.ctps == "0") {
                                var $ctps = "<span style='color:green' class='glyphicon glyphicon-ok-sign tt'  data-toggle='tooltip' data-placement='right' title='This number is NOT CTPS registerd'></span>";
                            } else if (val.ctps == "1") {
                                var $ctps = "<span style='color:red' class='glyphicon glyphicon-exclamation-sign tt'  data-toggle='tooltip' data-placement='right' title='This number IS CTPS registered'></span>";
                            } else {
                                var $ctps = "<span class='glyphicon glyphicon-question-sign tt'  data-toggle='tooltip' data-placement='right' title='CTPS Status is unknown'></span>"
                            }
                            $phone = "<tr><td>" + val.tel_name + "</td><td>" + val.tel_num + "</td><td>" + $ctps + "</td><td><span class='glyphicon glyphicon-trash pull-right del-item-btn' action='delete_cophone' item-id='" + val.tel_id + "'></span><span class='glyphicon glyphicon-pencil pull-right company-item-btn' action='edit_cophone' item-id='" + val.tel_id + "'></span></td></tr>";
                            $panel.find('#cophone tbody').append($phone);
                        });
                    } else {
                        $panel.find('#cophone .table-container table').hide();
                        $panel.find('#cophone .none-found').show();
                    }
                    if (response.data.address) {
                        $panel.find('#coaddress tbody').empty();
                        $panel.find('#coaddress .table-container, #coaddress .table-container table').show();
                        $panel.find('#coaddress .none-found').hide();
                        $.each(response.data.address, function(key, val) {
                            if (val.primary == 1) {
                                var $primary = "<span class='glyphicon glyphicon-ok-sign'></span>";
                            } else {
                                $primary = "";
                            }
                            $address = "<tr><td>" + val.add1 + "</td><td>" + val.postcode + "</td><td>" + $primary + "</td><td><span class='glyphicon glyphicon-trash pull-right del-item-btn' action='delete_coaddress' item-id='" + val.address_id + "'></span><span class='glyphicon glyphicon-pencil pull-right company-item-btn' action='edit_coaddress' item-id='" + val.address_id + "'></span></td></tr>"
                            $panel.find('#coaddress tbody').append($address);
                        });
                    } else {
                        $panel.find('#coaddress .table-container table').hide();
                        $panel.find('#coaddress .none-found').show();
                    }
                }
                $('.tt').tooltip();

            });
        }

    },
    //emails panel functions
    email_panel: {
        init: function() {
            this.config = {
                panel: '.email-panel'
            };
            record.email_panel.load_panel();
            $(document).on('click', '.new-email-btn', function(e) {
                e.preventDefault();
                record.email_panel.create();
            });
            $(document).on('click', '.close-email', function(e) {
                e.preventDefault();
                record.email_panel.close_email($(this));
            });
            $(document).on('click', '.close-emails-all', function(e) {
                e.preventDefault();
                record.email_panel.close_all_email($(this));
            });
            $(document).on('click', '.continue-email', function(e) {
                e.preventDefault();
                var template = $(this).closest('form').find('.emailtemplatespicker').val();
                window.location.href = helper.baseUrl + 'email/create/' + template + '/' + record.urn;
            });
            $(document).on('click', '.del-email-btn', function(e) {
                e.preventDefault();
                modal.delete_email($(this).attr('item-id'), $(this).attr('item-modal'));
            });
            $(document).on('click', '.show-all-email-btn', function(e) {
                e.preventDefault();
                record.email_panel.show_all_email($(this));
            });
            $(document).on('click', '.view-email-btn', function(e) {
                e.preventDefault();
                record.email_panel.view_email($(this).attr('item-id'));
            });
        },
        create: function() {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop email in"></div>').appendTo(document.body).hide().fadeIn();
            $('.email-container').find('.edit-panel').show();
            $('.email-content').show();
            $('.email-container').fadeIn()
            $('.email-container').animate({
                width: '500px',
                left: moveto,
                top: '10%'
            }, 1000);
            $('.emailtemplatespicker').selectpicker();
            $('.emailtemplatespicker').on('change', function(){
                var selected = $('.emailtemplatespicker option:selected').val();
                if (selected) {
                    $('.continue-email').prop('disabled', false);
                }
                else {
                    $('.continue-email').prop('disabled', true);
                }
            });
        },
        close_email: function() {
            $('.modal-backdrop.email').fadeOut();
            $('.email-container').fadeOut(500, function() {
                $('.email-content').show();
                $('.email-select-form')[0].reset();
                $('.alert').addClass('hidden');
            });
            $('.email-view-container').fadeOut(500, function() {
                $('.email-view-content').show();
            });
        },
        close_all_email: function() {
            $('.modal-backdrop.all-email').fadeOut();
            $('.email-container').fadeOut(500, function() {
                $('.email-content').show();
                $('.email-select-form')[0].reset();
                $('.alert').addClass('hidden');
            });
            $('.email-all-container').fadeOut(500, function() {
                $('.email-all-content').show();
            });
        },
        remove_email: function(email_id,modal) {
            $.ajax({
                url: helper.baseUrl + 'email/delete_email',
                type: "POST",
                dataType: "JSON",
                data: {email_id: email_id}
            }).done(function(response) {
                if (response.success) {
                    record.email_panel.load_panel();
                    if (modal == 1){
                        record.email_panel.close_all_email();
                        record.email_panel.show_all_email();
                    }
                    flashalert.success("Email was deleted from the history");
                };
            });
        },
        view_email: function(email_id) {
        	 var pagewidth = $(window).width() / 2;
             var moveto = pagewidth - 250;
             $('<div class="modal-backdrop email in"></div>').appendTo(document.body).hide().fadeIn();
             $('.email-view-container').find('.edit-panel').show();
             $('.email-view-content').show();
             $('.email-view-container').fadeIn()
             $('.email-view-container').animate({
                 width: '600px',
                 left: moveto,
                 top: '10%'
             }, 1000);
             //Get template data
             $.ajax({
                 url: helper.baseUrl + 'email/get_email',
                 type: "POST",
                 dataType: "JSON",
                 data: {email_id : email_id}
             }).done(function(response) {
                 var message = (response.data.status == true)?"<th colspan='2' style='color:green'>This email was sent successfuly</th>":"<th colspan='2' style='color:red'>This email was not sent</th>"
                 var status = (response.data.status == true)?"Yes":"No";
                 var read_confirmed = (response.data.read_confirmed == 1)?"Yes "+" ("+response.data.read_confirmed_date+")":"No";
                 var $tbody = $('.email-view-table').find('tbody');
                 $tbody.empty();
                 body = "<tr>" +
                         message +
                         "</tr>" +
                         "<th>Sent Date</th>" +
                         "<td class='sent_date'>" + response.data.sent_date + "</td>" +
                         "</tr>" +
                         "<tr>" +
                         "<th>From</th>" +
                         "<td class='from'>" + response.data.send_from + "</td>" +
                         "</tr>" +
                         "<tr>" +
                         "<th>To</th>" +
                         "<td class='to'>" + response.data.send_to + "</td>" +
                         "</tr>" +
                         "<tr>" +
                         "<th>CC</th>" +
                         "<td class='cc'>" + response.data.cc + "</td>" +
                         "</tr>" +
                         "<tr>" +
                         "<th>BCC</th>" +
                         "<td class='bcc'>" + response.data.bcc + "</td>" +
                         "</tr>" +
                         "<tr>" +
                         "<th>Subject</th>" +
                         "<td class='subject'>" + response.data.subject + "</td>" +
                         "</tr>" +
                         "<tr>" +
                         "<th colspan=2>Body</th>" +
                         "</tr>" +
                         "<td colspan=2 class='body'>" + response.data.body + "</td>" +
                         "</tr>" +
                         "<th>Sent</th>" +
                         "<td class='status'>" + status + "</td>" +
                         "</tr>" +
                         "<th>Read Confirmed</th>" +
                         "<td class='read_confirmed'>" + read_confirmed + "</td>" +
                         "</tr>"
                 if (response.attachments.length>0) {
                     body += "<tr>" +
                     "<th colspan=2>Attachments</th>" +
                     "</tr>";
                     $.each(response.attachments, function (key, val) {
                         body += "<tr>" +
                         "<td colspan='2' class='attachments'><a target='_blank' href='" + val.path + "'>" + val.name + "</td>" +
                         "</tr>";
                     });
                 }
                 $tbody
					.append(body);
             });
        },
        show_all_email: function(btn) {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop all-email in"></div>').appendTo(document.body).hide().fadeIn();
            $('.email-all-container').find('.email-all-panel').show();
            $('.email-all-content').show();
            $('.email-all-container').fadeIn()
            $('.email-all-container').animate({
                width: '600px',
                left: moveto,
                top: '10%'
            }, 1000);
            //Get emails data
            $.ajax({
                url: helper.baseUrl + "email/get_emails",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function(response) {
                var $thead = $('.email-all-table').find('thead');
                $thead.empty();

                var $tbody = $('.email-all-table').find('tbody');
                $tbody.empty();
                var body = "";

                if (response.data.length > 0) {
                    $.each(response.data, function(key, val) {
                        var status = (val.status != true)?"red":((val.read_confirmed == 1)?"green":"");
                        var message = (val.status != true)?"Email no sent":((val.read_confirmed == 1)?"Email read confirmed "+" ("+val.read_confirmed_date+")":"Waiting email read confirmation");
                        var send_to = (val.send_to.length > 15)?val.send_to.substring(0, 15)+'...':val.send_to;
                        var subject = (val.subject.length > 20)?val.subject.substring(0, 20)+'...':val.subject;
                        var $delete_option = "";
                        if(record.permissions['delete emails']>0) {
                            $delete_option = '<span class="glyphicon glyphicon-trash pull-right del-email-btn marl" data-target="#modal" item-modal="1" item-id="' + val.email_id + '" title="Delete email" ></span>';
                        }
                        $view_option = '<span class="glyphicon glyphicon-eye-open '+status+' pull-right view-email-btn pointer"  item-id="' + val.email_id + '" title="'+message+'"></span>';
                        body += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="'+val.send_to+'" >' + send_to + '</td><td title="'+val.subject+'" >' + subject + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
                    });
                    $thead.append('<tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr>');
                    $tbody.append(body);
                } else {
                    $tbody.append('<p>No emails have been sent for this record</p>');
                }
            });
        },
        load_panel: function() {
            $.ajax({
                url: helper.baseUrl + "email/get_emails",
                type: "POST",
                dataType: "JSON",
                data: {
                	urn: record.urn,
                    limit: record.limit
                }
            }).done(function(response) {
                $('.email-panel').empty();
                var $body = "";
                if (response.data.length > 0) {
                    //Use the k var only to know if there are more than x records
                    var k = 0;
                    $.each(response.data, function(key, val) {
                        if (k <= record.limit-1) {
                            var status = (val.status != true)?"red":((val.read_confirmed == 1)?"green":"");
                            var message = (val.status != true)?"Email no sent":((val.read_confirmed == 1)?"Email read confirmed "+" ("+val.read_confirmed_date+")":"Waiting email read confirmation");
                            var send_to = (val.send_to.length > 15)?val.send_to.substring(0, 15)+'...':val.send_to;
                            var subject = (val.subject.length > 20)?val.subject.substring(0, 20)+'...':val.subject;
                            var $delete_option = "";
                            if(record.permissions['delete email']>0) {
                                $delete_option = '<span class="glyphicon glyphicon-trash pull-right del-email-btn marl" data-target="#modal" item-modal="0" item-id="' + val.email_id + '" title="Delete email" ></span>';
                            }
                            $view_option = '<span class="glyphicon glyphicon-eye-open '+status+' pull-right view-email-btn pointer"  item-id="' + val.email_id + '" title="'+message+'"></span>';
                            $body += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="'+val.send_to+'" >' + send_to + '</td><td title="'+val.subject+'" >' + subject + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
                        }
                        k++;
                    });
                    if (k > record.limit-1) {
                        $body += '<tr><td colspan="6"><a href="#"><span class="btn pull-right show-all-email-btn marl" data-target="#modal" title="Show All" >Show All</span></a></td></tr>';
                    }
                    $('.email-panel').append('<div class="table-responsive"><table class="table table-striped table-condensed table-responsive"><thead><tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr></thead><tbody>' + $body + '</tbody></table></div>');
                } else {
                    $('.email-panel').append('<p>No emails have been sent for this record</p>');
                }
						;
            });
        }
    },
    //surveys panel functions
    surveys_panel: {
        init: function() {
            this.config = {
                panel: '.surveys-panel'
            };
            record.surveys_panel.load_panel();
            $(document).on('click', '.new-survey', function(e) {
                e.preventDefault();
                record.surveys_panel.create();
            });
            $(document).on('click', '.close-survey', function(e) {
                e.preventDefault();
                record.surveys_panel.close_survey($(this));
            });
            $(document).on('click', '.continue-survey', function(e) {
                e.preventDefault();
                var survey = $(this).closest('form').find('.surveypicker').val();
                var contact = $(this).closest('form').find('.contactpicker').val();
                window.location.href = helper.baseUrl + 'survey/create/' + survey + '/' + record.urn + '/' + contact;
                //record.surveys_panel.new_survey(); we dont use the popup any more
            });
            $(document).on('change', '.contactpicker', function(e) {
                record.surveys_panel.check_contact($(this));
            });
            $(document).on('click', '.back-survey', function(e) {
                e.preventDefault();
                $('.survey-content-2').fadeOut('fast', function() {
                    $('.survey-content-1').fadeIn();
                    $('.survey-form').empty();
                })
            });
            $(document).on('click', '.edit-survey-btn', function(e) {
                e.preventDefault();
                //record.surveys_panel.load_survey($(this).attr('item-id'));
                window.location.href = helper.baseUrl + "survey/edit/" + $(this).attr('item-id');
            });
            $(document).on('click', '.del-survey-btn', function(e) {
                e.preventDefault();
                modal.delete_survey($(this).attr('item-id'));
            });

        },
        create: function() {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop in"></div>').appendTo(document.body).hide().fadeIn();
            $('.survey-container').find('.edit-panel').show();
            $('.survey-content-1').show();
            $('.survey-content-2').hide();
            $('.survey-container').fadeIn()
            $('.survey-container').animate({
                width: '500px',
                left: moveto,
                top: '10%'
            }, 1000);
            $('.surveypicker,.contactpicker').selectpicker();
        },
        close_survey: function() {
            $('.modal-backdrop').fadeOut();
            $('.survey-container').fadeOut(500, function() {
                $('.survey-content-1').show();
                $('.survey-content-2').hide();
                $('.survey-select-form')[0].reset();
                $('.alert').addClass('hidden');
            });

        },
        check_contact: function($btn) {
            if ($('.contactpicker').val() == "") {
                $('.continue-survey').prop('disabled', true);
                $('.page-danger .alert-text').text('You must add the contact to the record before you can start a survey');
                $('.page-danger').removeClass('hidden').fadeIn(1000);
            } else {
                $('.continue-survey').prop('disabled', false);
                $('.page-danger').fadeOut(1000).addClass('hidden');
            }
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_survey',
                type: "POST",
                dataType: "JSON",
                data: {
                    survey: id
                }
            }).done(function(response) {
                if (response.success) {
                    record.surveys_panel.load_panel();
                    flashalert.warning("Survey was deleted");
                };
            });
        },
        load_panel: function() {
            $.ajax({
                url: helper.baseUrl + "ajax/get_surveys",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function(response) {
                $('.surveys-panel').empty();
                var $body = "";
                if (response.data) {
                    $.each(response.data, function(key, val) {
                        var $delete = "";
						var $options = "";

							if(record.permissions['delete surveys']>0){
                            $options = '<span class="glyphicon glyphicon-trash pull-right del-survey-btn" data-target="#modal" item-id="' + key + '" ></span><span class="glyphicon glyphicon-pencil pull-right edit-survey-btn"  item-id="' + key + '"></span>';
							}
                        	if(record.permissions['edit surveys']>0||!val.locked){
                            $options = '<span class="glyphicon glyphicon-edit pull-right edit-survey-btn pointer"  item-id="' + key + '"></span>';
							}
						if($options==""){
						 $options = '<span class="glyphicon glyphicon-eye-open pull-right eye-survey-btn pointer"  item-id="' + key + '"></span>';	
						}

                        $body += '<tr><td>' + val.date_created + '</td><td>' + val.contact_name + '</td><td>' + val.client_name + '</td><td>' + val.answer + '</td><td>' + val.is_completed + '</td><td>' + $options + '</td></tr>';
                    });
                    $('.surveys-panel').append('<div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Date</th><th>Contact</th><th>User</th><th>NPS</th><th>Status</th><th>Options</th></tr></thead><tbody>' + $body + '</tbody></table></div>');

                    //alert("show surveys");
                } else {
                    $('.surveys-panel').append('<p>No surveys have been created for this record</p>');
                    //alert("no surveys");
                }
				
            });
        }
    },
    //get additional info
    additional_info: {
        init: function() {
            $(document).on("click", ".add-detail-btn", function() {
                $(this).removeClass('glyphicon-pencil add-detail-btn').addClass('glyphicon-remove close-custom');
                $('.custom-panel').find('.panel-content').fadeOut(function() {
                    $('.custom-panel').find('form')[0].reset();
                    $('.custom-panel').find('form input').not('input[name="urn"]').val('');
                    $('.custom-panel').find('form').fadeIn();
                });
            });
			 $(document).on("click", ".del-detail-btn", function() {
				 modal.delete_additional_item($(this).attr('item-id'));
			 });
            $(document).on("click", ".edit-detail-btn", function() {
                record.additional_info.edit($(this).attr('item-id'));
            });
            $(document).on("click", ".close-custom", function(e) {
                e.preventDefault();
                $('.custom-panel').find('.glyphicon-remove').removeClass('glyphicon-remove close-custom').addClass('glyphicon-plus add-detail-btn');
                $('.custom-panel').find('form').fadeOut(function() {
                    $('.custom-panel').find('.panel-content').fadeIn()
                });
            });
            $(document).on("click", ".save-info", function(e) {
                e.preventDefault();
                record.additional_info.save();
            })
            record.additional_info.load_panel();
        },
		remove:function(id){
			 $.ajax({
                    url: helper.baseUrl + "ajax/remove_custom_item",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    record.additional_info.load_panel();
					    flashalert.warning("Selected information was deleted");
                });
		},
        edit: function(id) {
            $('.custom-panel').find('.panel-content').fadeOut(function() {
                $.ajax({
                    url: helper.baseUrl + "ajax/get_details_from_id",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        urn: record.urn,
                        campaign: record.campaign,
                        id: id
                    }
                }).done(function(response) {
                    record.additional_info.load_form(response.data,id);
					$('.custom-panel').find('form').fadeIn();
                });  
            });
        },
        save: function() {
            $.ajax({
                url: helper.baseUrl + "ajax/save_additional_info",
                type: "POST",
                dataType: "JSON",
                data: $('.custom-panel').find('form').serialize()
            }).done(function(response) {
                record.additional_info.load_panel();
                $('.custom-panel').find('form').fadeOut(function() {
                    $('.custom-panel').find('.panel-content').fadeIn()
                });
                flashalert.success(response.msg);
            });
        },
        load_panel: function() {
            $.ajax({
                url: helper.baseUrl + "ajax/get_additional_info",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function(response) {
				if(response.data.length>0){
                record.additional_info.load_table(response.data);
                record.additional_info.load_form(response.data);
				} else {
				$('.custom-panel').find('.panel-content').text("Nothing was found");	
				}
            });
        },
        load_table: function(data) {
            var $panel = $('.custom-panel').find('.panel-content');
            $panel.empty();
            var table = "<div class='table-responsive'><table class='table table-striped table-condensed'>";
            var thead, detail_id;
            var tbody = "<tbody>";
            var contents = "";
            $.each(data, function(k, detail) {
                tbody += "<tr>";
                thead = "<thead><tr>";
                $.each(detail, function(i, row) {
                    thead += "<th>" + row.name + "</th>";
					if(row.formatted){
					tbody += "<td class='" + row.code + "'>" + row.formatted + "</td>";
					} else {
                    tbody += "<td class='" + row.code + "'>" + row.value + "</td>";
					}
                    detail_id = row.id;
                });
                tbody += '<td><span class="glyphicon glyphicon-trash pull-right del-detail-btn marl" data-target="#modal" item-id="' + detail_id + '" ></span> <span class="glyphicon glyphicon-pencil pull-right edit-detail-btn"  item-id="' + detail_id + '"></span></td><tr>';
            });
            table += thead + '</thead>' + tbody + '<tbody></table></div>';
            $panel.append(table);
        },
        load_form: function(data,id) {
            var $form = $('.custom-panel').find('form');
            $form.empty();
            $form.append("<input type='hidden' name='urn' value='" + record.urn + "'/>");
			$form.append("<input type='hidden' name='detail_id' value='" + id + "'/>");
            var form = "";
            $.each(data, function(k, detail) {

                $.each(detail, function(i, row) {
                    var inputclass="";
                    if (row.options) {
                        $select = "<div class='form-group input-group-sm'>" + row.name;
                        $select += '<br><select name="' + row.code + '" class="selectpicker"><option value="">Please select</option>';
                        $.each(row.options, function(option_id, option_val) {
                            if (row.value == option_val) {
                                var selected = "selected";
                            }
                            $select += "<option " + selected + " value='" + option_val + "'>" + option_val + "</option>";
                        });
                        $select += "</select></div>";
                        form += $select;
                    } else {
                        if (row.type != "varchar" && row.type != "number") {
                            inputclass = row.type;
							if(row.value='-'&&row.type=="datetime"){ row.value = moment(new Date()).format("DD/MM/YYYY HH:mm:ss"); } if(row.value='-'&&row.type=="date"){ row.value = moment(new Date()).format("DD/MM/YYYY"); }
                        }
                        form += " <div class='form-group input-group-sm " + inputclass + "'>" + row.name + "<input class='form-control' name='" + row.code + "' type='text' value='" + row.value + "'/></div>";
                    }
                });
            });
            $form.append(form + "<button class='btn btn-primary pull-right marl save-info'>Save</button> <button class='btn btn-default pull-right close-custom'>Cancel</button>");
            $('.selectpicker').selectpicker();
            $('.date').datetimepicker({
                pickTime: false,
                format: 'DD/MM/YYYY'
            });
            $('.datetime').datetimepicker({
                format: 'DD/MM/YYYY HH:mm'
            });
        }
    },
    //ownership panel functions
    ownership_panel: {
        init: function() {
            record.ownership_panel.load_panel(record.urn);
            $(document).on('click', '.edit-owner', function(e) {
                e.preventDefault();
                record.ownership_panel.edit($(this));
            });
            $(document).on('click', '.close-owner', function(e) {
                e.preventDefault();
                record.ownership_panel.close_panel();
            });
            $(document).on('click', '.save-ownership', function(e) {
                e.preventDefault();
                record.ownership_panel.save();
            });
        },

        close_panel: function() {
            $panel = $('.ownership-panel');
            record.ownership_panel.load_panel();
            $panel.find('.edit-panel').fadeOut(1000, function() {
                $panel.find('.panel-content').fadeIn(1000, function() {

                    $panel.find('.glyphicon-remove').removeClass('glyphicon-remove close-owner').addClass('glyphicon-pencil edit-owner');

                });
            });
        },
        save: function() {
            $panel = $('.ownership-panel');
            var owners = $panel.find('.owners').val();
            $.ajax({
                url: helper.baseUrl + "ajax/save_ownership",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    owners: owners
                }
            }).done(function(response) {
                record.ownership_panel.close_panel();
                flashalert.success("Ownership was updated");
            });
        },
        load_panel: function() {
            $panel = $('.ownership-panel');
            $.ajax({
                url: helper.baseUrl + "ajax/get_users",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                },
                beforeSend: function() {
                    $panel.find('.panel-content').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                }
            }).done(function(response) {
                $panel.find('.panel-content').empty();
                if (response.data.length) {
                    $panel.find('.panel-content').append($('<ul/>'));
                    $.each(response.data, function(i, val) {
                        $panel.find('.panel-content ul').append($('<li/>').text(val.name));
                    });
                } else {
                    $panel.find('.panel-content').append($('<p/>').text('There are no users allocated to this record. To take ownership you can update the record or use the edit button to assign it to another user.'));
                }
				
            });
        },
        edit: function($btn) {
            $panel = $('.ownership-panel');

            $btn.removeClass('glyphicon-pencil edit-owner').addClass('glyphicon-remove close-owner');
            $panel.find('.panel-content').fadeOut(1000, function() {
                $panel.find('.edit-panel').fadeIn(1000);
            });
            $.ajax({
                url: helper.baseUrl + "ajax/get_ownership",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function(response) {
                $('.owners').selectpicker('val', response.data).selectpicker('render');
            });
        }
    },
    //script panel functions
    script_panel: function() {
        $(document).on("click", ".view-script", function(e) {
            e.preventDefault();
            $.ajax({
                url: helper.baseUrl + "ajax/get_script",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: $(this).attr('script-id')
                }
            }).done(function(response) {
                $('#modal').modal({
                    backdrop: 'static',
                    keyboard: false
                }).find('.modal-body').html(response.data.script);
                $('#modal').find('.modal-title').text(response.data.script_name);
                $(".confirm-modal").hide();
            });
        });
    },
	appointment_panel: {
        //initalize the group specific buttons 
        init: function() {
            $(document).on('click', '.new-appointment', function() {
                record.appointment_panel.create();
            });
            $(document).on('click', '.save-appointment', function(e) {
                e.preventDefault();
				if($(this).closest('form')[0].checkValidity()){
                record.appointment_panel.save($(this));
				} else {
				flashalert.danger("Please complete all form fields");	
				}
            });
            $(document).on('click', '.edit-appointment', function() {
                record.appointment_panel.edit($(this).attr('item-id'));
            });
            $(document).on('click', '.del-appointment', function() {
                modal.delete_appointment($(this).attr('item-id'));
            });
			$(document).on('click', '.close-appointment', function(e) {
            e.preventDefault();
            record.appointment_panel.hide_edit_form();
        });
		$(document).on('click', '.view-calendar', function(e) {
            e.preventDefault();
            modal.show_calendar(record.urn);
        });
			$(document).on('change','.addresspicker',function(){
				if($(this).val().length>0){
				$(this).closest('form').find('input[name="postcode"]').val($(this).val());
				}
			});
		//this function automatically sets the end date for the appointment 1 hour ahead of the start date
		$(".startpicker").on("dp.hide",function (e) {
			var m = moment(e.date, "DD\MM\YYYY H:m");
           $('.endpicker').data("DateTimePicker").setMinDate(e.date);
		   $('.endpicker').data("DateTimePicker").setDate(m.add('hours', 1).format('DD\MM\YYYY H:m'));
            });
            //start the function to load the groups into the table
            record.appointment_panel.load_appointments();
        },
		hide_edit_form: function() {
        $('.appointment-panel').find('form').fadeOut(1000, function() {
            $('.panel-content').fadeIn();
        });
    	},
        //this function reloads the groups into the table body
        load_appointments: function() {
            $.ajax({
                url: helper.baseUrl + 'records/load_appointments',
                type: "POST",
                dataType: "JSON",
				data:{urn:record.urn}
            }).done(function(response) {
				var $panel = $('.appointment-panel').find('.panel-content');
				$panel.empty();
                if (response.success) {
					if(response.data.length>0){
					record.appointment_panel.load_table(response.data);
					} else {
					$panel.append('<p>No appointments have been created</p>');	
					}
                } else {
                    $panel.append('<p>' + response.msg + '</p>');
                }
            });
        },
		load_table: function(data) {
            var $panel = $('.appointment-panel').find('.panel-content');
            $panel.empty();
       	
					var table = "<div class='table-responsive'><table class='table table-striped table-condensed'><thead><tr><th>Title</th><th>Info</th><th>Date</th><th>Time</th><th>Options</th></tr></thead><tbody>";
                    $.each(data, function(i, val) {
                        if (data.length) {
                            table +='<tr><td>' + val.title + '</td><td>' + val.text + '</td><td>' + val.date + '</td><td>' + val.time + '</td><td><button class="btn btn-default btn-xs edit-appointment" item-id="' + val.appointment_id + '">Edit</button> <button class="btn btn-default btn-xs del-appointment" item-id="' + val.appointment_id + '">Delete</button></td></tr>';
                        }
                    });
					$panel.append(table+"</tbody></table></div>");
					
        },
        load_form: function(data,id) {
            var $form = $('.appointment-panel').find('form');
            $form.find('input[name="urn"]').val(record.urn);
			$form.find('input[name="appointment_id"]').val(id);
			$form.find('input[name="title"]').val(data[0].title);
			$form.find('input[name="text"]').val(data[0].text);
			$form.find('input[name="start"]').data("DateTimePicker").setDate(data[0].start);
			$form.find('input[name="end"]').data("DateTimePicker").setDate(data[0].end);
			$form.find('input[name="postcode"]').val(data[0].postcode);
			//$form.find('.attendeepicker').selectpicker('val',data.attendees).selectpicker('render');
            $('.datetime').datetimepicker({
                format: 'DD/MM/YYYY HH:mm'
            });
        },
        //edit a group
        edit: function(id) {
             $('.appointment-panel').find('.panel-content').fadeOut(function() {
                $.ajax({
                    url: helper.baseUrl + "ajax/get_appointment",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        urn: record.urn,
                        id: id
                    }
                }).done(function(response) {
                   record.appointment_panel.load_form(response.data,id);
					$('.appointment-panel').find('form').fadeIn();
                });
            });
        },
        //add a new group
        create: function() {
			var $form = $('.appointment-panel').find('form');
            $form.trigger('reset');
            $form.find('input[type="hidden"]').not('input[name="urn"]').val('');
			$form.find('input[name="urn"]').val(record.urn);
            $('.appointment-panel').find('.panel-content').fadeOut(1000, function() {
                $form.fadeIn();
            });
        },
        //save a group
        save: function($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/save_appointment',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize()
            }).done(function(response) {
				if(response.success){
               record.appointment_panel.load_appointments();
                record.appointment_panel.hide_edit_form();
				$('.record-panel').find('.outcomepicker').find('li.disabled').each(function(){ $(this).removeClass('disabled'); });
				$('.record-panel').find('.outcomepicker').find('option:disabled').each(function(){ $(this).prop('disabled',false); });
                flashalert.success("Appointment saved");
				} else {
				flashalert.danger(response.msg);	
				}
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'records/delete_appointment',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
					urn:record.urn
                }
            }).done(function(response) {
                record.appointment_panel.load_appointments();
                if (response.success) {
                    flashalert.success("Appointment was deleted");
                } else {
                    flashalert.danger("Unable to delete user. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete user. Contact administrator");
            });

        }
	},
    recordings_panel: {
        init: function() {
            record.recordings_panel.load_panel();
            $(document).on('click', '.listen', function(e) {
                e.preventDefault();
                record.recordings_panel.convert_recording($(this), $(this).attr('data-id'),$(this).attr('data-path'))
            })
        },
        load_panel: function() {
            var $panel = $('.recordings-panel');
            $.ajax({
                url: helper.baseUrl + "recordings/find_calls",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                },
                beforeSend: function() {
                    $panel.html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                }
            }).fail(function(){
				$panel.html($('<p/>').text("Call recordings could not be found"));
			}).done(function(response) {
                $panel.empty();
				$body = "";
                if (response.data.length > 0) {
                    $.each(response.data, function(i, val) {
						var icon = "";
						if(val.transfer){
						icon = '<img style="vertical-align:top"  src="'+helper.baseUrl+'assets/img/icons/icon-transfer.png"/>'; }
                        $body += '<tr><td>' + val.calldate + '</td><td>' + val.duration + '</td><td>' + val.servicename + ' '+icon+'</td><td width="180"><a href="#" class="listen" data-id="' + val.id + '" data-path="'+val.filepath+'"><span class="speaker glyphicon glyphicon-play"></span> Listen</a> <span class="player-loading hidden">Please wait  <img src="' + helper.baseUrl + 'assets/img/ajax-load-black.gif"/></span></td></tr>';
                    });
                    $panel.html('<div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Call Date</th><th>Duration</th><th>Number</th><th>Options</th></tr></thead><tbody>' + $body + '</tbody></table></div>');
                } else {
                    $panel.html($('<p/>').text(response.msg));
                }
				
            });
        },
        convert_recording: function($btn, id, path) {
            $.ajax({
                url: helper.baseUrl + 'recordings/listen/' + id + '/' + path,
                type: "POST",
                dataType: "JSON",
                beforeSend: function() {
                    $btn.next('.player-loading').removeClass("hidden");
                }
            }).done(function(response) {
                $btn.next('.player-loading').addClass("hidden");
                if (response.success) {
                    modal.call_player(response.filename, response.filetype)
                } else {
                    flashalert.danger("There was a problem loading the recording");
                }
            });
        }
    },
    //attachment_panel_functions
    attachment_panel: {
        init: function() {
            this.config = {
                panel: '.attachment-panel'
            };
            record.attachment_panel.load_panel();

            /* initialize the delete attachment buttons */
            $(document).on('click', '.del-attachment-btn', function(e) {
                e.preventDefault();
                modal.delete_attachment($(this).attr('item-id'));
            });

            $(document).on('click', '.show-all-attachments-btn', function(e) {
                e.preventDefault();
                record.attachment_panel.show_all_attachments($(this));
            });
            $(document).on('click', '.close-attachment-all', function(e) {
                e.preventDefault();
                record.attachment_panel.close_all_attachments($(this));
            });
        },
        load_panel: function(attachment_id) {
            var $panel = $(record.attachment_panel.config.panel);
            $.ajax({
                url: helper.baseUrl + 'records/get_attachments',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    limit: record.limit
                }
            }).done(function(response) {
                $panel.find('.attachment-list').empty();
                var body = '';
                if (response.data.length>0) {
                    //Use the k var only to know if there are more than x records
                    var k = 0;
                    $.each(response.data, function (key, val) {
                        if (k <= record.limit-1) {
                            var remove_btn = '<span class="glyphicon glyphicon-trash del-attachment-btn marl" data-target="#modal" item-id="'+val.attachment_id+'" title="Delete attachment"></span>';
                            var download_btn = '<a style="color:black;" href="'+val.path+'"><span class="glyphicon glyphicon-download-alt"></span></a>';
                            body +=  '<tr class="'+val.attachment_id+'">' +
                            '<td>' + val.name +
                            '</td><td>' + val.date +
                            '</td><td>' + val.user +
                            '</td><td>' + download_btn +
                            '</td><td>' + remove_btn +
                            '</td></tr>' ;
                        }
                        k++;
                    });
                    if (k > record.limit-1) {
                        body += '<tr><td colspan="6"><a href="#"><span class="btn pull-right show-all-attachments-btn marl" data-target="#modal" title="Show All" >Show All</span></a></td></tr>';
                    }
                    $('.attachment-list').append('<div class="table-responsive"><table class="table table-striped table-condensed table-responsive"><thead><tr><th>Name</th><th>Date</th><th>Added by</th><th colspan="2">Options</th></tr></thead><tbody>' + body + '</tbody></table></div>');

                    if (attachment_id) {
                        $panel.find('.attachment-list').find('.'+attachment_id).fadeIn(500).delay(250).fadeOut(500).fadeIn(500).delay(250).fadeOut(500).fadeIn(500).delay(250).fadeOut(500).fadeIn(500);
                    }
                }
                else {
                    $('.attachment-list').append('<p>This record has no attachments</p>');
                }
				
            });
        },
        show_all_attachments: function(btn) {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop all-attachments in"></div>').appendTo(document.body).hide().fadeIn();
            $('.attachment-all-container').find('.attachment-all-panel').show();
            $('.attachment-all-content').show();
            $('.attachment-all-container').fadeIn()
            $('.attachment-all-container').animate({
                width: '600px',
                left: moveto,
                top: '10%'
            }, 1000);
            //Get attachment data
            $.ajax({
                url: helper.baseUrl + "records/get_attachments",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function(response) {
                var $thead = $('.attachment-all-table').find('thead');
                $thead.empty();

                var $tbody = $('.attachment-all-table').find('tbody');
                $tbody.empty();
                var body = "";

                if (response.data.length > 0) {
                    $.each(response.data, function (key, val) {
                        var remove_btn = '<span class="glyphicon glyphicon-trash del-attachment-btn marl" data-target="#modal" item-id="'+val.attachment_id+'" title="Delete attachment"></span>';
                        var download_btn = '<a style="color:black;" href="'+val.path+'"><span class="glyphicon glyphicon-download-alt"></span></a>';
                        body +=  '<tr class="'+val.attachment_id+'">' +
                        '<td>' + val.name +
                        '</td><td>' + val.date +
                        '</td><td>' + val.user +
                        '</td><td>' + download_btn +
                        '</td><td>' + remove_btn +
                        '</td></tr>' ;
                    });
                    $thead.append('<tr><th>Name</th><th>Date</th><th>Added by</th><th colspan="2">Options</th></tr>');
                    $tbody.append(body);
                } else {
                    $tbody.append('<p>This record has no attachments</p>');
                }
            });
        },
        close_all_attachments: function() {
            $('.modal-backdrop.all-attachments').fadeOut();
            $('.attachment-container').fadeOut(500, function() {
                $('.attachment-content').show();
                $('.attachment-select-form')[0].reset();
                $('.alert').addClass('hidden');
            });
            $('.attachment-all-container').fadeOut(500, function() {
                $('.attachment-all-content').show();
            });
        },
        delete_attachment: function(id) {
            $.ajax({
                url: helper.baseUrl + 'records/delete_attachment',
                type: "POST",
                dataType: "JSON",
                data: {
                    attachment_id: id
                }
            }).done(function(response) {
                if (response.success) {
                    record.attachment_panel.close_all_attachments();
                    record.attachment_panel.show_all_attachments();
                    record.attachment_panel.load_panel();
                    flashalert.warning("Attachment was deleted");
                };
            });
        },
        save_attachment: function(name, type, path) {
            $.ajax({
                url: helper.baseUrl + 'records/save_attachment',
                type: "POST",
                dataType: "JSON",
                data: {
                    name: name,
                    type: type,
                    path: path,
                    urn: record.urn
                }
            }).done(function(response) {
                if (response.success) {
                    record.attachment_panel.load_panel(response.attachment_id);
                    flashalert.success("Attachment was saved");
                }
                else {
                    flashalert.danger("ERROR: The attachment was NOT saved");
                }
            });
        }
    }
}

$(document).on('click','.nav-btn',function(e){
	e.preventDefault();
	//modal.confirm_move($(this).attr('href'));
	flashalert.danger("You must update the record first");
})
/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
confirm_move:function(moveUrl){
	   $('.modal-title').text('Are you sure?');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('You have not updated the record. Do you really want to continue?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            window.location.href=moveUrl
            $('#modal').modal('toggle');
        });
},
    delete_contact: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this contact?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.contact_panel.remove(id);
            $('#modal').modal('toggle');
        });
    },
	    delete_additional_item: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.additional_info.remove(id);
            $('#modal').modal('toggle');
        });
    },
    delete_company: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this company?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.company_panel.remove(id);
            $('#modal').modal('toggle');
        });
    },
    delete_email: function(email_id, modal) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this email?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.email_panel.remove_email(email_id, modal);
            $('#modal').modal('toggle');
        });
    },
    delete_attachment: function(attachment_id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this attachment?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.attachment_panel.delete_attachment(attachment_id);
            $('#modal').modal('toggle');
        });
    },
    delete_survey: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this survey and all the answers?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.surveys_panel.remove(id);
            $('#modal').modal('toggle');
        });
    },
	    delete_appointment: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this appointment?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.appointment_panel.remove(id);
            $('#modal').modal('toggle');
        });
    },
    call_player: function(url, filetype) {
        $(document).off("hidden.bs.modal").on("hidden.bs.modal", function() {
            wavesurfer.destroy();
			$('#modal').find('.modal-body').empty();
        });
        $(".confirm-modal").hide();
        $('.modal-title').text('Call Playback');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html('<div id="waveform"></div><div class="controls"><button class="btn btn-primary" id="playpause"><i class="glyphicon glyphicon-pause"></i>Pause</button> <button class="btn btn-primary" id="slowplay"><i class="glyphicon glyphicon-left"></i>Slower</button> <button class="btn btn-primary" id="speedplay"><i class="glyphicon glyphicon-right"></i>Faster</button> <a target="blank" class="btn btn-info" href="' + url.replace("ogg", "mp3") + '">Download</a> <span class="pull-right" id="duration"></span> <span id="audiorate" class="hidden">1</span></div>');
		modal.wavesurfer(url.replace('ogg','mp3'));
    },
	wavesurfer: function(fileurl){
		// Create an instance
wavesurfer = Object.create(WaveSurfer);
// Init & load audio file
var options = {
container : document.querySelector('#waveform'),
waveColor : 'violet',
progressColor : 'purple',
loaderColor : 'purple',
cursorColor : 'navy',
audioRate: 1
};
if (location.search.match('scroll')) {
options.minPxPerSec = 100;
options.scrollParent = true;
}
if (location.search.match('normalize')) {
options.normalize = true;
}
// Init
wavesurfer.init(options);
// Load audio from URL
wavesurfer.load(fileurl);
// Regions
if (wavesurfer.enableDragSelection) {
wavesurfer.enableDragSelection({
color: 'rgba(0, 255, 0, 0.1)'
});
}
;
// Play at once when ready
// Won't work on iOS until you touch the page
wavesurfer.on('ready', function () {
wavesurfer.play();
$('#duration').text(wavesurfer.getDuration()+'s');
});
// Report errors
wavesurfer.on('error', function (err) {
console.error(err);
});
// Do something when the clip is over
wavesurfer.on('finish', function () {
console.log('Finished playing');
});

$('#speedplay').click(function(){
	wavesurfer.setPlaybackRate(Number($('#audiorate').text())+0.2);
	$('#audiorate').text(Number($('#audiorate').text())+0.2);
});
$('#slowplay').click(function(){
	wavesurfer.setPlaybackRate(Number($('#audiorate').text())-0.2);
	$('#audiorate').text(Number($('#audiorate').text())-0.2);
});
$('#playpause').click(function(){
	if($('#playpause i').hasClass('glyphicon-pause')){
	$('#playpause').html('<i class="glyphicon glyphicon-play"></i> Play');
	wavesurfer.pause();
	console.log("Paused");
	} else {
	$('#playpause').html('<i class="glyphicon glyphicon-pause"></i> Pause');	
	wavesurfer.play();
	console.log("Playing");
	}
});
	},
    delete_history: function(history_id, modal) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this history record?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.history_panel.remove_history(history_id, modal);
            $('#modal').modal('toggle');
        });
    },
	    dead_line: function($btn) {
        $('.modal-title').text('Confirm Dead Line');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html('<p>You have set this record as a dead line but the history shows it has been dialed previously. There may be a telephony issue or we could be at full dialing capacity. Please try to dial again and confirm it\'s an actual dead line. If this is a B2B record please search for the company telephone number online and update the record with the correct number<p><p>Click confirm if you are sure this is a dead line otherwise click cancel</p>');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            record.update_panel.save($btn);
            $('#modal').modal('toggle');
        });
    },
	show_calendar: function(urn){
		 $(".confirm-modal").hide();
		var d = new Date();
		var time = d.getTime();
			$('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html('<img id="modal-loading" src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif"/><div class="responsive-calendar" style="display:none"><div class="controls"><a data-go="prev" class="pull-left"><div class="btn btn-primary">Prev</div></a><h4><span data-head-year=""></span> <span data-head-month=""></span></h4><a data-go="next" class="pull-right"><div class="btn btn-primary">Next</div></a></div><hr/><div class="day-headers"><div class="day header">Mon</div><div class="day header">Tue</div><div class="day header">Wed</div><div class="day header">Thu</div><div class="day header">Fri</div><div class="day header">Sat</div><div class="day header">Sun</div></div><div class="days" data-group="days"></div></div>');
		$('#modal').find('.modal-footer').html('<button class="btn btn-default close-modal pull-left" data-dismiss="modal" type="button">Close</button><select class="selectpicker"><option value="5">5 Miles</option><option value="10" selected>10 Miles</option><option value="15">15 Miles</option><option value="20">20 Miles</option><option value="30">30 Miles</option><option value="40">40 Miles</option><option value="50">50 Miles</option><option value="500">Any Distance</option></select>'); 
		$('#modal').find('.selectpicker').selectpicker().on('change',function(){
			modal.configure_calendar(urn,$(this).val(),true);
		});
		modal.configure_calendar(urn,10);
	},
	configure_calendar:function(urn,distance,renew){
	$('.modal-title').text('Scheduled appointments within '+distance+' miles');
		$.ajax({ url:helper.baseUrl+'calendar/get_events',
		data: { modal:true,urn: urn,distance:distance,
		campaigns:[record.campaign] },
		dataType:"JSON",
		type: "POST"}).done(function(response){	
		if(renew){
			 $('.responsive-calendar').responsiveCalendar('clearAll').responsiveCalendar('edit',response.result);
		} else {
			 $('.responsive-calendar').responsiveCalendar({
	 time: '2014-12',
    events: response.result
			 })
		}
		$('#modal-loading').hide();
		$('.responsive-calendar').slideDown();
		});	
	}

}