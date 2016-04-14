<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
 $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
 mysql_select_db(DB_NAME,$link);

 if (isset($_GET['server_group'])) {
   $server_group_id = filter_var_array($_GET['server_group'],FILTER_SANITIZE_MAGIC_QUOTES);
   $sg_var ='';
   for ($i=0;$i<count($server_group_id);$i++) {
     $sg_var .= " '".$server_group_id[$i]."'";
   }
   $server_group = str_replace("' '",",",$sg_var);
   $server_group = str_replace("'","",$server_group);
   $server_group = str_replace(" ","",$server_group);
   $sql1 = "SELECT * FROM servers WHERE trusted = 1 AND server_group IN (".$server_group.");";

   $sg_sql = "SELECT * FROM server_group WHERE id IN (".$server_group.");";
   $sg_res = mysql_query($sg_sql);
   $sg_name = "";

   while ($sg_row = mysql_fetch_assoc($sg_res)) {
	    $sg_name .= " '".$sg_row['server_group']."'";
   }
   $sg_name = str_replace("' '",", ",$sg_name);
   $sg_name = str_replace("'","",$sg_name);

   $page_title='Patch List for Server Group: '.$sg_name;
 } else {
   $sql1 = "SELECT * FROM servers WHERE trusted = 1;";
   $page_title='Patch List - All Servers';
 }

 $supressed = array("nadda");
 $supressed_list = "";
 foreach($supressed as $val){
	$supressed_list .= " '$val'";
 }
	$supressed_list = str_replace("' '","', '",$supressed_list);

 $nsupressed_sql = "SELECT COUNT(DISTINCT(`server_name`)) AS total_needing_patched FROM `patches` WHERE `package_name` NOT IN (SELECT `package_name` FROM `supressed`) AND package_name !='';";
 $nsupressed_res = mysql_query($nsupressed_sql);
 $nsupressed_row = mysql_fetch_array($nsupressed_res);
 $nsupressed_total = $nsupressed_row['total_needing_patched'];

 $res1 = mysql_query($sql1);
 $table = "";
 $total_count = 0;
 $server_count = 0;
 $base_path=BASE_PATH;
 while ($row1 = mysql_fetch_assoc($res1)){
     $server_count++;
     $server_name = $row1['server_name'];
     $server_alias = $row1['server_alias'];
     $server_checked = $row1['last_checked'];
	 $distro_id = $row1['distro_id'];
	 $dist_sql = "SELECT * FROM distro WHERE id='$distro_id';";
	 $dist_res = mysql_query($dist_sql);
	 $dist_row = mysql_fetch_array($dist_res);
	 $dist_img = BASE_PATH.$dist_row['icon_path'];
     $sql2 = "SELECT COUNT(*) as `total` FROM patches where server_name='$server_name' and package_name NOT IN($supressed_list) and package_name != '';";
     $res2 = mysql_query($sql2);
     $row2 = mysql_fetch_array($res2);
     $count = $row2['total'];

     $sql3 = "SELECT COUNT(*) as `total` FROM patches where server_name='$server_name' and package_name NOT IN($supressed_list) and package_name != '' and urgency='medium';";
     $res3 = mysql_query($sql3);
     $row3 = mysql_fetch_array($res3);
     $count2 = $row3['total'];

     $sql4 = "SELECT COUNT(*) as `total` FROM patches where server_name='$server_name' and package_name NOT IN($supressed_list) and package_name != '' and urgency='low';";
     $res4 = mysql_query($sql4);
     $row4 = mysql_fetch_array($res4);
     $count3 = $row4['total'];

     $sql5 = "SELECT COUNT(*) as `total` FROM patches where server_name='$server_name' and package_name NOT IN($supressed_list) and package_name != '' and urgency='high';";
     $res5 = mysql_query($sql5);
     $row5 = mysql_fetch_array($res5);
     $count4 = $row5['total'];

     $sql6 = "SELECT COUNT(*) as `total` FROM patches where server_name='$server_name' and package_name NOT IN($supressed_list) and package_name != '' and urgency='unknown';";
     $res6 = mysql_query($sql6);
     $row6 = mysql_fetch_array($res6);
     $count5 = $row6['total'];

     $total_count = $total_count + $count;
     if ($count == 0) {
     $table .= "                <tr>
                  <td width='40px'><img src='$dist_img' class='avatar'></td>
                  <td><a href='{$base_path}patches/server/$server_name'>$server_alias</a></td>
                  <td><span class='btn btn-success btn-xs'>Up to date :)</span></td>
                  <td>$server_checked</td>
                  <td></td>
                </tr>
";
     } else {
     $table .= "                <tr>
                  <td width='40px'><img src='$dist_img' class='avatar'></td>
                  <td><a href='{$base_path}patches/server/$server_name'>$server_alias</a></td>
                  <td><a href='{$base_path}patches/server/$server_name' class='btn btn-default btn-xs'> $count</a></td>
                  <td>$server_checked</td>
                  <td><span class='btn btn-default btn-xs'> $count4 <i class='fa fa-exclamation-triangle text-danger'></i> </span>
                  <span class='btn btn-default btn-xs'> $count2 <i class='fa fa-exclamation-triangle text-warning'></i> </span>
                  <span class='btn btn-default btn-xs'> $count3 <i class='fa fa-exclamation-triangle text-info'></i> </span>
                  <span class='btn btn-default btn-xs'> $count5 <i class='fa fa-question-circle text-primary'></i> </span></td>
                </tr>
";
     }
 }
 mysql_close($link);

?>
        <div class="col-sm-12 col-md-12 col-xs-12 main">
          <div class="x_panel">
            <div class="x_title">
              <h2><?php echo $page_title; ?></h2>
              <div class="clearfix"></div>
            </div>
          <div class="table-responsive">
            <table class="table table-striped jambo_table">
              <thead>
                <tr>
                  <th width='40px'></th>
                  <th>Server Name</th>
                  <th>Patches</th>
                  <th>Last Patch Check</th>
                  <th>Urgency Breakdown</th>
                </tr>
              </thead>
              <tbody>
                <?php echo $table; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
