
                
                <div class="navbar submenu-default navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
    <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?></p>
    </ul>
    <?php if(!isset($hide_filter)){ ?>
       <ul class="nav navbar-nav pull-right" id="submenu-filters">
               <li>
         <div class="navbar-btn">
                     <div class="btn-group">
                    <a type="button" class="btn btn-default btn" data-modal="choose-columns"
                            data-table-id="1"><span
                            class="fa fa-table"></span> Views
                    </a>
                </div>
              <?php 
            if ($this->uri->segment(2)=="mapview") {
                $map_class = "btn-success";
            } else {
                $map_class = "btn-default";
            } ?>   
                
                <div class="btn-group">
                    <a class="btn btn-default btn" href="<?php echo base_url() ?>records/mapview"><span
                            class="fa fa-globe"></span> Map
                    </a>
                </div>
            <?php if (isset($global_filter)) {    
             if (isset($_SESSION['filter'])) {
                $filter_class = "btn-success";
            } else {
                $filter_class = "btn-default";
            } ?>
                  <div class="btn-group">
                    <a href="#global-filter" class="btn <?php echo $filter_class ?>"><span
                            class="fa fa-filter"></span> Filter
                    </a>
              <?php } ?>
                </div>
                
                    <div class="btn-group desktop-only">
         <input value="<?php echo @$_SESSION['filter']['values']['postcode'] ?>"  name="postcode" class="form-control" style="width:130px" placeholder="Enter Postcode"/>
         </div>
               <div class="btn-group desktop-only">
            <select name="distance" data-width="130" class="selectpicker">
             <option value="">Any Distance</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="1"){ echo "selected"; } ?>  value="1">1 Mile</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="3"){ echo "selected"; } ?>  value="3">3 Miles</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="5"){ echo "selected"; } ?> value="5">5 Miles</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="10"){ echo "selected"; } ?> value="10">10 Miles</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="20"){ echo "selected"; } ?> value="20">20 Miles</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="30"){ echo "selected"; } ?> value="30">30 Miles</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="50"){ echo "selected"; } ?> value="50">50 Miles</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="75"){ echo "selected"; } ?> value="75">75 Miles</option>
             <option <?php if($_SESSION['filter']['values']['distance']=="100"){ echo "selected"; } ?> value="100">100 Miles</option>
            </select>
            </div>
     <div class="btn-group desktop-only">
         <button class="btn btn-primary" id="submenu-filter-submit">Go</button>
         </div>
       </div>
        </li>
        
            </ul>
            <?php } ?>
            </div>
            
   <script>
   $(document).ready(function(){
	  $('#submenu-filter-submit').on('click',function(e){
		 e.preventDefault();
		 var postcode = $('#submenu-filters input[name="postcode"]').val();
		 var distance = $('#submenu-filters select[name="distance"]').val(); 
		 $('#global-filter-form').find('input[name="postcode"]').val(postcode);
		 $('#global-filter-form').find('select[name="distance"]').val(distance).selectpicker('refresh');
		 //submit the filter
		 $('#global-filter-form').find('.apply-filter').trigger('click');
	  });
   });
   
   </script>         