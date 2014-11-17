
<div  style="padding:0 50px 50px">
  <div class="page-header">

    <div class="pull-right form-inline">
    <div class="form-inline pull-right" style="padding-left:20px">
     <div class="form-group">
             <?php //if(count($campaigns)>1){ ?>
                               <select id="campaign-select" name="campaigns[]" class="selectpicker" data-width="100%" data-size="5">
                               <option value="">Filter by campaign</option>
                  <?php foreach($campaigns as $row): ?>
                  <?php if(in_array($row['id'],$_SESSION['campaign_access']['array'])):  ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['calendar-filter']['campaign_id'])){ echo "selected"; } else { echo (isset($_SESSION['current_campaign'])&&$_SESSION['current_campaign']==$row['id']?"selected":""); } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endif ?>
				  <?php endforeach; ?>
                </select>
                 <?php //} ?>
                 </div>
                 
                   <div class="form-group">
             <?php //if(count($campaigns)>1){ ?>
                               <select id="user-select" name="users[]" class="selectpicker" data-width="100%" data-size="5">
                               <option value="">Filter by user</option>
                  <?php foreach($users as $row): ?>   
                  <option <?php if(@in_array($row['id'],$_SESSION['calendar-filter']['user_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
				  <?php endforeach; ?>
                </select>
                 <?php //} ?>
                 </div>
    </div>
      <div class="btn-group">
        <button class="btn btn-primary" data-calendar-nav="prev"><< Prev</button>
        <button class="btn" data-calendar-nav="today">Today</button>
        <button class="btn btn-primary" data-calendar-nav="next">Next >></button>
      </div>
      <div class="btn-group">
        <button class="btn btn-info" data-calendar-view="year">Year</button>
        <button class="btn btn-info active" data-calendar-view="month">Month</button>
        <button class="btn btn-info" data-calendar-view="week">Week</button>
        <button class="btn btn-info" data-calendar-view="day">Day</button>
      </div>
    </div>
    <h3></h3>
    <small></small> </div>
  <div id="calendar"></div>
</div>

<div class="modal fade" id="events-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 class="modal-title">Event</h3>
</div>
<div class="modal-body" style="height: 400px">
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
		            "use strict";
			
            var options = {
                events_source: function(start,end){ 
				var events = [];
                $.ajax({
                    url: helper.baseUrl+'calendar/get_events',
                    dataType: 'JSON',
                    type:     'POST',
					async: false,
					data: {
					startDate: start.getTime(),
            		endDate: end.getTime(),	
					campaign: $('#campaign-select').selectpicker('val'),
					user: $('#user-select').selectpicker('val')
					}
					}).done(function (json) {
                         if(!json.success) {
                            $.error(json.error);
                        }
                        if(json.result) {
                        events =  json.result;
                        }
                     });
				return events;
},
				//modal: "#events-modal",
                view: 'month',
                tmpl_path: helper.baseUrl + 'assets/tmpls/',
                tmpl_cache: false,
                day: '<?php echo $date ?>',
                onAfterEventsLoad: function(events) {
                    if (!events) {
                        return;
                    }
                    var list = $('#eventlist');
                    list.html('');
                    $.each(events, function(key, val) {
                        $(document.createElement('li'))
                            .html('<a href="' + val.url + '">' + val.title + '</a>')
                            .appendTo(list);
                    });
                },
                onAfterViewLoad: function(view) {
                    $('.page-header h3').text(this.getTitle());
                    $('.btn-group button').removeClass('active');
                    $('button[data-calendar-view="' + view + '"]').addClass('active');
                },
                classes: {
                    months: {
                        general: 'label'
                    }
                }
            };

            var calendar = $('#calendar').calendar(options);

            $('.btn-group button[data-calendar-nav]').each(function() {
                var $this = $(this);
                $this.click(function() {
                    calendar.navigate($this.data('calendar-nav'));
                });
            });

            $('.btn-group button[data-calendar-view]').each(function() {
                var $this = $(this);
                $this.click(function() {
                    calendar.view($this.data('calendar-view'));
                });
            });

            $('#first_day').change(function() {
                var value = $(this).val();
                value = value.length ? parseInt(value) : null;
                calendar.setOptions({
                    first_day: value
                });
                calendar.view();
            });

            $('#language').change(function() {
                calendar.setLanguage($(this).val());
                calendar.view();
            });

            $('#events-in-modal').change(function() {
                var val = $(this).is(':checked') ? $(this).val() : null;
                calendar.setOptions({
                    modal: val
                });
            });
            $('#events-modal .modal-header, #events-modal .modal-footer').click(function(e) {
                //e.preventDefault();
                //e.stopPropagation();
            });

    });
</script>