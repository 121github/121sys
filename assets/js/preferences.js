   $(document).ready(function () {

			$('#open-quicksearch').on('click',function(e){
				e.preventDefault();
				$('#nav-menu-btn').trigger('click');
				$('#quicksearch-btn').trigger('click');
			});

            $('#color-box').on('click', '.color-btn', function () {
                var mheader = "Appearance";
                var report_btn= '' ;
                if (helper.permissions['export data'] > 0) {
                    report_btn = '<span type="button" class="btn btn-default report-settings-btn">' +
                                    '<p>Report Settings</p>' +
                                    '<span class="fa fa-area-chart fa-3x"></span>' +
                                 '</span>';
                }

                var last_messages= '' ;
                var display_messages = (helper.role == "1" ?"":"display:none");

                if (localStorage.getItem("messages")) {
                    $.each(JSON.parse(localStorage.getItem("messages")), function (i, val) {
                        var date = new Date(val[3]);
                        var date_show = date.toLocaleString().replace(",","");
                        var title = val[1];
                        var msg = val[2];
                        var msg_short = (msg && msg.length>40?msg.substring(0,40)+"...":msg);
                        var tooltip = '<h3>'+title+'</h3><div>'+date_show+'</div><div>'+msg+'</div>'
                        last_messages += '<tr class="'+(val[0]?"success":"danger")+' pointer last-messages" data-toggle="tooltip" data-placement="top" title="'+tooltip+'">' +
                            '<td style="font-weight: bold">'+title+'</td>' +
                            '<td style="word-break:break-all">'+msg_short+'</td>' +
                            '<td style="text-align: right">'+date_show+'</td>' +
                            '</tr>';
                    });
                }

				if(helper.campaign_id){
				var layout_panel =   '<input type="hidden" name="current_camp" value="'+helper.campaign_id+'"><span type="button" class="btn btn-default layout-settings-btn" data-layout="2col.php">' +
                                            '<p>Grid View</p>' +
                                            '<span class="fa fa-th-large fa-3x"></span>' +
                                        '</span>' +
										 '<span type="button" class="btn btn-default layout-settings-btn" data-layout="accordian.php">' +
                                            '<p>List View</p>' +
                                            '<span class="fa fa-bars fa-3x"></span>' +
                                        '</span>' +
											 '<span type="button" class="btn btn-default layout-settings-btn" data-layout="default">' +                                            '<p>Default View</p>' +
                                            '<span class="fa fa-th fa-3x"></span>' +
                                        '</span>';
										} else {
					var layout_panel = "<p>You must select a campaign to set the layout</p>";
									 } 
                var navtabs = '<ul id="tabs" class="nav nav-tabs" role="tablist"><li class="active"><a role="tab" data-toggle="tab" href="#theme-tab">Theme</a></li>'+(helper.permissions['change layout']>0?'<li><a role="tab" data-toggle="tab" href="#layout-tab"> Layout</a>':'')+'</li><li><a role="tab" data-toggle="tab" href="#dashboards-tab"> Dashboard</a></li><li style="'+display_messages+'"><a role="tab" data-toggle="tab" href="#last-messages-tab">Last actions</a></li></ul>';
                var tabpanels = '<div class="tab-content" style="overflow-y: scroll; max-height: 400px">' +
                                    '<div role="tabpanel" class="tab-pane active" id="theme-tab">' +
                                        '<p>Fancy something different? Pick a new colour!</p>' +
                                        '<select id="color-changer" class="color-changer selectpicker">' +
                                            '<option value="'+helper.theme_color+'">--Change color--</option>' +
                                            '<option value="voice">Bright Blue</option>' +
                                            '<option value="hsl">Deep Blue</option>' +
                                            '<option value="coop">Dark Blue</option>' +
                                            '<option value="smartprospector">Green</option>' +
                                            '<option value="default">Orange</option>' +
                                            '<option value="pelican">Red</option>' +
                                            '<option value="eldon">Purple</option>' +
                                        '</select>' +
                                    '</div>' +
									         '<div role="tabpanel" class="tab-pane" id="layout-tab">' +
                                      layout_panel +
                                        
                                    '</div>' +
                                    '<div role="tabpanel" class="tab-pane" id="dashboards-tab">' +
                                        '<span type="button" class="btn btn-default dashboard-settings-btn">' +
                                            '<p>Dashboard Settings</p>' +
                                            '<span class="fa fa-dashboard fa-3x"></span>' +
                                        '</span>' +
                                        report_btn +
                                    '</div>' +
                                    '<div role="tabpanel" class="tab-pane" id="last-messages-tab">' +
                                        '<table class="table table-hover small">' +
                                            '<thead>' +
                                                '<th>Title</th>' +
                                                '<th>Message</th>' +
                                                '<th>Date</th>' +
                                            '</thead>' +
                                            '<tbody>' +
                                                last_messages +
                                            '</tbody>' +
                                        '</table>' +
                                    '</div>' +
                                '</div>';
                var mbody = navtabs+tabpanels;
                var mfooter = '<button data-dismiss="modal" class="btn btn-primary close-modal pull-left">OK</button>'

                modals.load_modal(mheader, mbody, mfooter);
                modal_body.css('overflow', 'visible')
                $modal.find('.color-changer').change(function () {
                    var value = $(this).val();
                    $('#theme-css').attr('href', helper.baseUrl + 'assets/themes/colors/' + value + '/bootstrap-theme.css');
                    $.post(helper.baseUrl + 'ajax/change_theme', {theme: value});
                    if (device_type !== "default") {
                        window.location.reload();
                    }
                });

                $('.last-messages').tooltip({
                    html: true
                });
            });

            $modal.on("click",".dashboard-settings-btn",function()
            {
                window.location = helper.baseUrl + 'dashboard/settings';
            });

            $modal.on("click",".report-settings-btn",function()
            {
                window.location = helper.baseUrl + 'exports';
            });
			
			  $modal.on("click",".layout-settings-btn",function()
            {	var campaign = $modal.find('input[name="current_camp"]').val();
				var layout = $(this).attr('data-layout');
                $.ajax({ url: helper.baseUrl+'user/layout',
				type:"POST",
				dataType:"JSON",
				data:{layout:layout,campaign:campaign }
            }).done(function(response){
				location.reload();
			});
  });
        });