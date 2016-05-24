<div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
        <p class="navbar-text" style="color:#fff; font-weight:700"><i class="fa fa-gear fa-fw"></i> <?php echo $title ?></p>
    </ul>
    <?php if (!isset($hide_filter)) { ?>
        <ul class="nav navbar-nav pull-right" id="submenu-filters">
            <li>
                <div class="navbar-btn">
                    <?php if($_SESSION['role'] == 1): ?>
                        <select data-title="Switch User" data-size="15" id="user-filter" class="selectpicker"></button>
                            <?php foreach($users as $row): ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php endforeach ?>
                        </select>

                    <?php endif ?>
                </div>
            </li>

        </ul>
    <?php } ?>
</div>