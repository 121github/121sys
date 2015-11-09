  <div class="row">

  <div class="col-md-12"> 
    <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-search fa-fw"></i> Search
            </div>
              <div class="panel-body search-panel">

<form class="form">

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
<input type="text" class="form-control" placeholder="Enter company name" name="company"/>
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
<input type="text" class="form-control" placeholder="The 4 digit code" name="c4"/>
</div>
</div>

<div class="col-xs-12 col-sm-6 col-lg-1">
<div class="form-group">
<label>&nbsp;</label>
<br />
<button class="btn btn-primary" id="search">Search</button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>