<?php
session_start();
$index_check = "active";
include 'lib/db_config.php';
/*
 * @author Jon Harris
 * @license Apache License v2.0
 */
# Will add Offline feature later
#define("OFFLINE", false);
#if (OFFLINE == true){
#    include 'inc/offline.inc.php';
#}
$plugin_dir = ltrim(BASE_PATH."plugins",'/');
$link = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf-8', ''.DB_USER.'', ''.DB_PASS.'');
$plugin_installed_sql = "SELECT pm.page_name as name, p.name as plugin_name,p.glyph as plugin_glyph,pm.glyph as page_glyph,pm.on_navbar as on_navbar FROM page_maps pm LEFT JOIN plugins p ON pm.plugin_parent = p.id where p.installed = 1 and p.disabled = 0 and p.is_admin=0";
$plugin_installed_sql_res = $link->query($plugin_installed_sql);
$plugin_row = $plugin_installed_sql_res->fetchAll(PDO::FETCH_ASSOC);
$allowed_pages = array();
$navbar_array = array();
foreach($plugin_row as $val){
    $plugin_name = $val['plugin_name'];
    $navbar_array[$plugin_name]['glyph'] = $val['plugin_glyph'];
    $is_on_navbar = $val['on_navbar'];
    if ($is_on_navbar == "1"){
        $navbar_array[$plugin_name]['page_and_glyph'][] = $val['name'].",".$val['page_glyph'];
    }
    $allowed_pages[] = $val['name'];
}
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true){
#    $allowed_pages[] = "admin";
    $admin_plugins_sql = "SELECT pm.page_name as name, p.name as plugin_name,p.glyph as plugin_glyph,pm.glyph as page_glyph,pm.on_navbar as on_navbar FROM page_maps pm LEFT JOIN plugins p ON pm.plugin_parent = p.id where p.installed = 1 and p.disabled = 0 and p.is_admin=1";
    $admin_plugins_res = $link->query($admin_plugins_sql);
    $admin_plugin_row = $admin_plugins_res->fetchAll(PDO::FETCH_ASSOC);
    foreach ($admin_plugin_row as $val){
    $plugin_name = $val['plugin_name'];
    $navbar_array[$plugin_name]['glyph'] = $val['plugin_glyph'];
    $is_on_navbar = $val['on_navbar'];
    if ($is_on_navbar == "1"){
        $navbar_array[$plugin_name]['page_and_glyph'][] = $val['name'].",".$val['page_glyph'];
    }
    $allowed_pages[] = $val['name'];
    }
}
$requested_page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING);
if (!isset($requested_page) || is_null($requested_page) || empty($requested_page)){
    $requested_page = "patches";
}
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != true){
    include 'inc/login.inc.php';
    exit();
}
if (!in_array($requested_page, $allowed_pages)){
    include 'inc/404_header.inc.php';
    include 'inc/navbar.inc.php';
    include 'inc/404_body.inc.php';
    include 'inc/404_footer.inc.php';
}
else{
    $url_sql = "SELECT p.name as plugin_name,pm.real_file as plugin_file FROM page_maps pm LEFT JOIN plugins p ON p.id = pm.plugin_parent WHERE pm.page_name = '$requested_page' LIMIT 1;";
    $url_res = $link->query($url_sql);
    $url_row = $url_res->fetchAll(PDO::FETCH_ASSOC);
    if (count($url_row) == 0){
        include 'inc/404_header.inc.php';
        include 'inc/navbar.inc.php';
        include 'inc/404_body.inc.php';
        include 'inc/404_footer.inc.php';
    }
    else{
        $final_plugin = $url_row[0]['plugin_name'];
        $file = $url_row[0]['plugin_file'];
        $link = NULL;
        include 'inc/header.inc.php';
        include 'inc/navbar.inc.php';
        include "plugins/${final_plugin}/$file";
        include 'inc/footer.inc.php';
    }
}
