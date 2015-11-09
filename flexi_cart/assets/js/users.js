// JavaScript Document

var dashboard = {
    users_panel: function(campaign) {
        $.ajax({
            url: helper.baseUrl + 'admin/user_data',
            type: "POST",
            dataType: "JSON",
			beforeSend: function(){
			            $('.user-data').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
			 $('.user-data').empty();
            var $row = "";
            if (response.success) {
                $.each(response.data, function (i, val) {
					$row += '<td>' + val.user_id + '</td><td>' + val.name + '</td><td>' + val.username + '</td><td>' + val.group_name + '</td><td>' + val.role_name + '</td><td>' + val.user_status + '</td></tr>';
                });
                $('.user-data').append('<table class="table table-striped table-responsive"><thead><th>ID</th><th>Name</th><th>Username</th><th>Group</th><th>Role</th><th>Status</th></thead><tbody>' + $row + '</tbody></table>');
            } else {
                $('.target-data').append('<p>' + response.msg + '</p>');
            }
        });
    }
}