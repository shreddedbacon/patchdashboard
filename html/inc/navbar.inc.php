<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}

$data = "";
foreach($navbar_array as $key=>$val){
    $plugin = $key;
    $plugin_name = ucwords($plugin);
    $data .="<div class=\"panel panel-default\">
                    <div class=\"panel-heading\">
                        <h4 class=\"panel-title\">
                            <a data-toggle=\"collapse\" data-parent=\"#accordion\" href=\"#collapse$plugin\"><span class=\"glyphicon glyphicon-home\">
                            &nbsp;&nbsp;</span>Main</a>
                        </h4>
                    </div>
                    <div id=\"collapse$plugin\" class=\"panel-collapse collapse in\">
                        <div class=\"panel-body\">
                            <table class=\"table\">";
    foreach ($val as $val2){
        $val2_words = str(replace("_"," ",$val2));
        $val2_caps = ucwords($val2_words);
        if ($requested_page == $val2){
            $data .= "                                <tr>
                                    <td>
                                        <span class=\"glyphicon glyphicon-warning-sign text-primary\"></span>&nbsp;&nbsp;<a href=\"$val2\">$val2_caps</a>
										<span class=\"badge\">42</span>
                                    </td>
                                </tr>";
        }
        else{
            $data .= "\t\t\t\t\t<li> <a href='$val2'>$val2_caps</a></li>\n";
        }
        $data .= "</table>
                        </div>
                    </div>
                </div>";
    }
}
?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <p><img src="<?php echo BASE_PATH;?>img/report.png"/></p>
                <ul class="nav nav-sidebar">
<?php echo $data;?>
            </ul>
        </div>
          
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-3">
            <div class="panel-group" id="accordion">
                <?php echo $data;?>
            </div>
        </div>