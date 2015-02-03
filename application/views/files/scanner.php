  <div class="row">
        <div class="col-lg-12">
          <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>File Browser</div>
            <div class="pull-right">
              <div class="btn-group">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> Filter <span class="caret"></span> </button>
                <ul class="dropdown-menu pull-right" role="menu">
                  <?php foreach($folders as $row): ?>
                  <li><a href="#" class="folder-filter" id="<?php echo $row['folder_id'] ?>"><?php echo $row['folder_name'] ?></a> </li>
                  <?php endforeach ?>
                  <li class="divider"></li>
                  <li><a class="folder-filter" ref="#">Show All</a> </li>
                </ul>
              </div>
            </div>
            
            <div class="panel-body browser-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> 
            </div>
          </div>
        </div>
</div>
  <div class="row">
        <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>File Search</div>
            <div class="panel-body search-panel"> 
             <form>
            <div class="row">
            
            
            <div class="col-lg-6">
              <div class="form-group">
               <label for="addPosition">Add Position</label>
<input class="form-control" name="Add Position" id="addPosition" style="width:150px"/>
</div>




  <div class="form-group">
Search 1 (Positions)
<select class="form-control"   multiple rows="10">
<option value="web developer">web developer</option>
</select>
</div>

</div>

 <div class="col-lg-6">
  <div class="form-group">
               <label for="keyword">Keyword</label>
<input class="form-control" name="kkeyword" id="keyword" style="width:150px"/>
</div>
 
Search 2 (Sectors)
  <div class="form-group">
<select class="form-control"  multiple rows="10">
<option value="food and drink">food and drink</option>
</select>
</div>

<input type="submit" class="btn btn-default" value="submit">

             </div>
             
             
             
          </div>
          
          </form>  
        </div>
        </div>
             </div>
          <div class="col-lg-6">
          <div class="panel panel-primary">
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Search Results</div>
            <div class="panel-body search-panel"> 
            

            
            </div>
          </div>
        </div>
        
</div>
