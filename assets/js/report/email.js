// JavaScript Document

// JavaScript Document
$(document).ready(function() {
    email.init()
});

var email = {
    init: function() {
        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "02/07/2014",
                maxDate: moment(),
                startDate: "02/07/2014",
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                email.email_panel()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".template-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="template"]').val($(this).attr('id'));
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            email.email_panel()
        });

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            email.email_panel()
        });
        $(document).on("click", ".agent-filter", function(e) {
            e.preventDefault();
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="team"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            email.email_panel()
        });
        $(document).on("click", ".team-filter", function(e) {
            e.preventDefault();
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="team"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="agent"]').val('');
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            email.email_panel()
        });
        $(document).on("click", ".source-filter", function(e) {
            e.preventDefault();
			$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="source"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            email.email_panel()
        });
        $(document).on('click', '.show-emails-btn', function(e) {
            e.preventDefault();
            email.show_emails($(this));
        });
        $(document).on('click', '.close-emails-all', function(e) {
            e.preventDefault();
            email.close_all_email($(this));
        });
        email.email_panel()
    },
    email_panel: function(email) {
        $.ajax({
            url: helper.baseUrl + 'reports/email_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            var $row = "";
            $tbody = $('.email-data .ajax-table').find('tbody');
    		$tbody.empty();
            if (response.success) {
				$('#email-name').text(response.email);
            	$.each(response.data, function(i, val) {
                    if (response.data.length) {

                        var style = "";
                        var success = "";
                        if (val.total_emails>0) {
                    		success = "success";
                    	}
                    	else if (val.campaign == "TOTAL") {
                    		style = "font-weight:bold;";
                    	}
                    	else {
                    		success = "warning";
                    	}

						$tbody
						.append("<tr class='"+success+"' style='"+style+"'><td class='email'>"
									+ val.id
								+ "</td><td class='name'>"
									+ val.name
								+ "</td><td class='emails_read'>"
								+ 	"<a href='#' class='show-emails-btn'>"+ val.emails_read + "</a>" +
                                    "<a href='" + val.emails_read_url + "' style='font-size:10px;'> (See records...)</a>"
								+ "</td><td class='total_emails'>"
                                + 	"<a href='#' class='show-emails-btn'>"+ val.total_emails + "</a>" +
                                 	"<a href='" + val.total_emails_url + "' style='font-size:10px;'> (See records...)</a>"
								+ "</td><td class='percent' style='percent'>"
									+ val.percent
								+ "</td></tr>");
                    }
                });
            } else {
            	$tbody
				.append("<tr><td colspan='6'>"
					+ response.msg
					+ "</td></tr>");
            }
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
    show_emails: function(btn) {
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
                urn: 1
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
                    $delete_option = '<span class="glyphicon glyphicon-trash pull-right del-email-btn marl" data-target="#modal" item-id="' + val.email_id + '" title="Delete email" ></span>';
                    $view_option = '<span class="glyphicon glyphicon-eye-open '+status+' pull-right view-email-btn pointer"  item-id="' + val.email_id + '" title="'+message+'"></span>';
                    body += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="'+val.send_to+'" >' + send_to + '</td><td title="'+val.subject+'" >' + subject + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
                });
                $thead.append('<tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr>');
                $tbody.append(body);
            } else {
                $tbody.append('<p>No emails have been sent for this record</p>');
            }
        });
    }
}