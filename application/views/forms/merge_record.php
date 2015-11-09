<div class="merge-container">
<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
    <li class="active"><a id="load-options" href="#merge-options" class="tab" data-toggle="tab">Options</a></li>
    <li><a href="#merge-preview" id="load-preview" class="tab" data-toggle="tab">Preview</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="merge-options">
    <form id="merge-form">
    <div class="row">
    <div class="col-xs-4">
    <div class="input-group">
    <label>Source URN</label>
    <input class="form-control" name="source" />
    </div>
     </div>
     <div class="col-xs-1">
     <div class="input-group">
     </div><h2><span class="glyphicon glyphicon-arrow-right"></span></h2>
      </div>
      <div class="col-xs-4">
     <div class="input-group">
     <label>Target URN</label>
     <input class="form-control" name="target" />
    </div>
    </div>
    </div>
    
     <div class="row">
     <div class="col-xs-6">
    <h4>Company Details</h4>
    <div class="input-group">
    <select name="company_details" class="selectpicker">
    <option value="1">Ignore</option>
     <option selected value="2">Fill the blanks</option>
      <option value="3">Overwrite</option>
    </select>
    </div>
    </div>
    <div class="col-xs-6">
        <h4>Contact Details</h4>
    <div class="input-group">
    <select name="contact_details" class="selectpicker">
    <option value="1">Ignore</option>
     <option selected value="2">Fill the blanks</option>
      <option value="3">Overwrite</option>
    </select>
    </div>
    </div>
    </div>
     <div class="row">
     <div class="col-xs-6">
          <h4>History</h4>
    <div class="input-group">
    <select class="selectpicker">
    <option value="1">Ignore</option>
     <option value="2">Merge</option>
    </select>
    </div>
    </div>
      <div class="col-xs-6">
              <h4>Custom Info</h4>
    <div class="input-group">
    <select class="selectpicker">
    <option value="1">Ignore</option>
    <option value="2">Merge</option>
    <option value="2">Add to sticky</option>
    </select>
    </div>
    </div>
    </div>
    <div class="row">
     <div class="col-xs-12">
     <hr />
    <p>What should happen to the source record when the data has been merged?</p>
     <div class="input-group">
    <select class="selectpicker">
    <option value="1">Do nothing</option>
    <option selected value="2">Set as duplicate and remove</option>
    </select>
    </div>
    </div>
    </div>
    </form>
    </div>
    <div class="tab-pane" id="merge-preview">
    </div>
    
    </div>