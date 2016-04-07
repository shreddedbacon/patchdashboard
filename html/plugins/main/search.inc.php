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
 $package = filter_var($_GET['package'],FILTER_SANITIZE_MAGIC_QUOTES);
 $count = 0;
 if (isset($_GET['exact']) && $_GET['exact'] == "true"){
	$sql1 = "SELECT * FROM patch_allpackages where package_name = '$package';";
 }
 else{
	$sql1 = "select * from patch_allpackages where package_name like '%$package%';";
 }
 $res1 = mysql_query($sql1);
 $base_path = BASE_PATH;
 while ($row1 = mysql_fetch_assoc($res1)){
     $count++;
     $package_name = $row1['package_name'];
     $package_version = $row1['package_version'];
     $server_name = $row1['server_name'];
     $sql_patch = "SELECT * FROM patches WHERE package_name = '$package_name' AND server_name = '$server_name'";
     $sql_patch_avail = mysql_query($sql_patch);
     $sql_patch_avail = mysql_fetch_assoc($sql_patch_avail);
     $update_avail = "";
     if (!empty($sql_patch_avail)) {
	$update_avail = " | <span class='label label-primary'>Update available : ".$sql_patch_avail['new']."</span>";
     }
     $table .= "                <tr>
		  <td><a href='${base_path}patches/server/$server_name' style='color:black'>$server_name</a></td>
                  <td><a href='${base_path}search/exact/$package_name' style='color:green'>$package_name</a>".$update_avail."</td>
		  <td>$package_version</td>
                </tr>
";
}
?>
<div class="col-sm-9 col-md-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Search</h1>
          <h3 class="sub-header">Results for search "<?php echo $package;?>" (<?php echo $count;?> found)</h3>
        <div class="container">
          <div class="table-responsive">
            <table class="table table-striped">
             <thead>
                <tr>
                  <th>Server Name</th>
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
