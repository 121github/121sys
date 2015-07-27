    <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Eldon Dashboard</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      
      
      <div class="row">

  <div class="col-md-12"> 
    <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-search fa-fw"></i> Search
            </div>
              <div class="panel-body search-panel">

<form class="form" id="filter-form")>

<div class="col-xs-12 col-sm-6 col-lg-2">
<div class="form-group">
<label>Campaign</label><br />
<select class="selectpicker" multiple data-width="100%" name="campaign_id[]">
   <?php foreach($campaigns as $row): ?>
<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
<?php endforeach; ?>
</select>
</div>
</div>


<div class="col-xs-12 col-sm-6 col-lg-2">
<div class="form-group">
<label>Company</label>
<input type="text" class="form-control" placeholder="Enter company name" name="coname"/>
</div>
</div>

<div class="col-xs-12 col-sm-6 col-lg-2">
<div class="form-group">
<label>Contact</label>
<input type="text" class="form-control" placeholder="Enter contact name" name="fullname"/>
</div>
</div>

<div class="col-xs-12 col-sm-6 col-lg-2">
<div class="form-group">
<label>Postcode</label>
<input type="text" class="form-control" placeholder="Enter postcode" name="postcode"/>
</div>
</div>

<div class="col-xs-12 col-sm-6 col-lg-2">
<div class="form-group">
<label>Reference number</label>
<input type="text" class="form-control" placeholder="The 4 digit code" name="client_ref"/>
</div>
</div>

<div class="col-xs-12 col-sm-6 col-lg-2">
<div class="form-group">
<label>&nbsp;</label>
<br />
<button class="btn btn-primary pull-left" id="search">Search</button> <button class="btn marl pull-left btn-default clear-filter">Clear</button>
</div>
</div>
</form>
<hr>

<div class="row">

</div>
<div class="col-md-12">
<div id="search-results">

Found: <span class="record-count"></span> <a href="#" class="submit-filter" >View Records</a>

</div>
</div>
</div>
   </div>
      <!-- /.row -->
     
      
      <div class="row">
        <div class="col-lg-12">
                  <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-clock-o fa-fw"></i> Overdue Visits
               <div class="pull-right"> <form id="overdue-filter" data-func="overdue_panel">
            	  <input type="hidden" name="date_from" value="">
                  <input type="hidden" name="date_to" value="">
                  <input type="hidden" name="campaign">
                  <input type="hidden" name="team">
                  <input type="hidden" name="agent">
                  <input type="hidden" name="source">
                  <div class="btn-group">
                  			      <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> Last updated </span></button></div>
                  <?php if(!isset($_SESSION['current_campaign'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" > <span class="glyphicon glyphicon-filter"></span> Campaign</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($campaigns as $row): ?>
	                    <li><a href="#" class="filter" data-ref="campaign" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                  <?php if(in_array("by agent",$_SESSION['permissions'])){ ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> User</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All USers</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                  </form>
                  </div>
                  </div>
              <div class="panel-body" id="overdue-panel">
             <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
            </div>
        </div>
        </div>
        </div>
        

<script src="<?php echo base_url() ?>assets/js/filter.js"></script> 


<script>
$(document).ready(function(){
	$('.bootstrap-select').css('margin',0);
		filter.count_records();
	$(document).on('click','#search',function(e){
		e.preventDefault();
		filter.count_records();
	})
	$(document).on('click', '.submit-filter', function(e) {
            e.preventDefault();
            filter.apply_filter();
  	});
});
</script>