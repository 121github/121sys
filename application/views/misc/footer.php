<?php 
$show_footer = false;
if (isset($_SESSION['current_campaign']) && in_array("show footer", $_SESSION['permissions'])) {
    $show_footer = true;
}
if ($show_footer) { ?>
    <div class="navbar-inverse footer-stats" style="z-index:1">
        <!--ajax generated footer stats go here -->
    </div>
<?php } ?>