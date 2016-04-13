<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
include 'inc/supressed_patches.inc.php';
 $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
 mysql_select_db(DB_NAME,$link);
 $server_name = filter_var($_GET['server'],FILTER_SANITIZE_MAGIC_QUOTES);
 $table = "";
 $sql0 = "SELECT * from servers where server_name='$server_name';";
 $res0 = mysql_query($sql0);
 $row0 = mysql_fetch_array($res0);
 $server_alias = $row0['server_alias'];
 $sql1 = "select * from patch_allpackages where server_name='$server_name';";
 $res1 = mysql_query($sql1);
 $base_path = BASE_PATH;
 while ($row1 = mysql_fetch_assoc($res1)){
     $package_name = $row1['package_name'];
     $package_version = $row1['package_version'];
     $table .= "                <tr>
                  <td><a href='${base_path}search/exact/$package_name' style='color:green'>$package_name</a></td>
		  <td>$package_version</td>
                </tr>
";
}
?>

          <div class="col-sm-12 col-md-12 col-xs-12 main">
            <div class="x_panel">
              <div class="x_title">
                <h2>Full Package List</h2>
                <div class="clearfix"></div>
              </div>
              <h3>Server: <?php echo $server_alias;?></h3>
          <div class="table-responsive">
            <table class="table table-striped">
             <thead>
                <tr>
                  <th>Package Name</th>
                  <th>Package Version</th>
                </tr>
              </thead>
              <tbody>
<?php echo $table;?>
              </tbody>
            </table>
          </div>
        </div>
</div>
