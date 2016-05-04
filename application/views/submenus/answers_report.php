
                
                <div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
    <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?></p>
    </ul>
    <?php if(!isset($hide_filter)){ ?>
       <ul class="nav navbar-nav pull-right">
             <li>
             <div class="navbar-btn">
  <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"> Survey Filter <span class="caret"></span> </button>
                  <ul class="dropdown-menu pull-right" role="menu">
                    <?php foreach($surveys as $row): ?>
                    <li><a href="#" id="answers-filter" data-id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                    <?php endforeach ?>
                    <li class="divider"></li>
                    <li><a id="answers-filter" ref="#">All Surveys</a> </li>
                  </ul>
                </div>
                </div>
            </li>
            </ul>
            <?php } ?>
            </div>