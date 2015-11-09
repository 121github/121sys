<?php
function get_theme($theme_id) {
$theme_query = mysql_query("select * from tbl_themes where theme_id = '$theme_id'");
$theme = mysql_fetch_assoc($theme_query);
if (mysql_num_rows($theme_query) == 1) {
$_SESSION['theme_nav'] = $theme['nav_bg_colour'];
$_SESSION['theme_header'] = $theme['head_bg_colour'];
$_SESSION['theme_table'] = $theme['table_bg_colour'];
$_SESSION['theme_footer'] = $theme['footer_bg_colour'];
$_SESSION['theme_nav_text'] = $theme['nav_text_colour'];
$_SESSION['theme_header_text'] = $theme['head_text_colour'];
$_SESSION['theme_table_text'] = $theme['table_text_colour'];
$_SESSION['theme_footer_text'] = $theme['footer_text_colour'];
$_SESSION['theme_dd_bg'] = $theme['nav_dropdown_bg'];
$_SESSION['theme_dd_text'] = $theme['nav_dropdown_text'];
$_SESSION['theme_dd_hover'] = $theme['nav_dropdown_hover'];
$_SESSION['theme_bg'] = $theme['background_img'];
$_SESSION['theme_logo'] = 	$theme['logo_img'];
}
}
if (empty($_SESSION['system_url']))
{ $theme = "default"; }
else 
{ $theme = $_SESSION['system_url'];}
?>