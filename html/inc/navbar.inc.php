<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
$patch_list_sql = "SELECT count(*) as total_found FROM `patches` p LEFT JOIN servers s on s.server_name = p.server_name WHERE s.trusted = 1 and p.upgraded=0 and p.package_name !='';";
$patch_list_link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$patch_list_link);
$patch_list_res = mysql_query($patch_list_sql);
$patch_list_row = mysql_fetch_array($patch_list_res);
$patches_to_apply_count = $patch_list_row['total_found'];
mysql_close($patch_list_link);
$data = "";
foreach ($navbar_array as $key=>$val){
    $plugin2 = $key;
    $plugin2_glyph = $val["glyph"];
    $plugin_name = ucwords($plugin2);
            $data .= "<div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h4 class='panel-title'>
                            <a data-toggle='collapse' data-parent='#accordion' href='#collapse$plugin2'><span class='$plugin2_glyph'>
                            &nbsp;&nbsp;</span>$plugin_name</a>
                        </h4>
                    </div>
                    <div id='collapse$plugin2' class='panel-collapse collapse in'>
                        <div class='panel-body'>
                            <table class='table'>";
    foreach ($val['page_and_glyph'] as $val2){
        $tmp_array = explode(",",$val2);
        $page_string = $tmp_array[0];
        $page_words = ucwords(str_replace("_"," ",$page_string));
        if (isset($tmp_array[1])){
            $page_glyph = "<span class=\"".$tmp_array[1]." text-primary\"></span>&nbsp;&nbsp;";
        }
        else{
            $page_glyph = "";
        }
        /*
         * Badge code:
         * <span class=\"badge\">42</span>
         * TODO: work the badge code in dynamically with patche count
         */
        if ($page_string == "patches"){
            $badge_code = "&nbsp;&nbsp;<span class=\"badge\">$patches_to_apply_count</span>";
        }
        else{
            $badge_code = "";
        }
        $data .= "                                <tr>
                                    <td>
                                        $page_glyph<a href=\"".BASE_PATH."$page_string\">$page_words</a>$badge_code
                                    </td>
                                </tr>";
    }
        $data .= "</ul>
                </li>";
}
if (!isset($_SESSION['error_notice'])){
    $error_html = "";
}
else{
    $error_message = $_SESSION['error_notice'];
    $error_html = "<div class='bs-example'><div class='alert alert-error'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        <strong>Error! </strong> $error_message
    </div></div>";
    unset($_SESSION['error_notice']);
    unset($error_message);
}

if (!isset($_SESSION['good_notice'])){
    $good_html = "";
}
else{
    $good_message = $_SESSION['good_notice'];
    $good_html = "<div class='container'><div class='row'><div class='span4'><div class='alert alert-success'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        <strong>Notice: </strong> $good_message
    </div></div></div></div>";
    unset($_SESSION['good_notice']);
    unset($good_message);
}

if (!isset($_SESSION['warning_notice'])){
    $warning_html = "";
}
else{
    $warning_message = $_SESSION['warning_notice'];
    $warning_html = "<div class='bs-example'><div class='alert alert-warning'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        <strong>Warning: </strong> $warning_message
    </div></div>";
    unset($_SESSION['warning_notice']);
    unset($warning_message);
}
$all_messages_to_send = "${warning_html}${good_html}${error_html}";
?>
    <div class="container-fluid">
<?php echo $all_messages_to_send;unset($all_messages_to_send);?>
      <div class="row">
        <div class="col-sm-2 col-md-2 sidebar">
            <div class="panel-group" id="accordion">
                <?php echo $data;?>
            </div>
        </div>
