 <?php if(isset($collapsable)){ ?>
     
        <div id="appointment-panel" class="panel panel-primary">
      <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-target="#appointment-panel-slide" href="#appointment-panel-slide" aria-expanded="true" aria-controls="appointment-panel-slide">
 Appointments<?php if(in_array("add appointments",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pointer pull-right marl" data-modal="create-appointment" data-urn="<?php echo $record['urn'] ?>"><span class="glyphicon glyphicon-plus"></span> New</button><?php } ?> <button class="btn btn-default btn-xs pull-right pointer view-calendar"><span class="glyphicon glyphicon-calendar"></span> Calendar</button>
    </div>
       <div id="appointment-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
      <div class="panel-body appointment-panel"> 
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
      </div>
    </div>
    
    <?php } else { ?>
 
    <div id="appointment-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Appointments  <?php if(in_array("add appointments",$_SESSION['permissions'])){ ?><button class="btn btn-default btn-xs pointer pull-right marl" data-modal="create-appointment" data-urn="<?php echo $record['urn'] ?>"><span class="glyphicon glyphicon-plus"></span> New</button><?php } ?> <a href="#calendar-right" class="btn btn-default btn-xs pull-right pointer"><span class="glyphicon glyphicon-calendar"></span> Calendar</a></div>
      <div class="panel-body appointment-panel"> 
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
    </div>
 <?php } ?>
 <style>
.tooltip-inner { text-align:left !important; max-width:400px; }
#calendar .tt-month { color:#fff;background:#FF0000;border-radius:5px; padding:2px 4px }
#calendar .tt-week { color:#FF0000;  }
.fc-toolbar .bootstrap-select { text-align:left;width:auto !important; max-width:200px}
.context-menu-icon-updated { padding-left:8px !important }
#calendar-right {
    width:900px !important; min-width:900px !important
}
html.mm-right.mm-opening .mm-slideout {
    transform: translate(-900px, 0px);
}
</style>
 <script>
 $(document).ready(function(){
 $('body').append('<nav id="calendar-right" style="display:none" class="mm-menu mm--horizontal mm-offcanvas"><div id="calendar"></div></nav>');
  $('nav#calendar-right').mmenu({
            navbar: {
                title: "Showing Calendar <span class='text-primary'>Booking View</span>"
            },
            extensions: ["pageshadow", "effect-menu-slide", "effect-listitems-slide", "pagedim-black"],
            offCanvas: {
                position: "right", zposition : "front"
            }
        }, {
classNames: {
fixedElements: {
fixed: "isFixed"
}
}
});
	var api = $('nav#calendar-right').data('mmenu');
api.bind('opened', function () {
	$('#calendar-right').fadeIn(400,function(){
    calendar.init();
	$modal.css('z-index','9999999');
	});
});
api.bind('closing', function(){ 
	$('#calendar-right').fadeOut(200,function(){
    calendar.destroy();
	});
});
  });
 </script>
 