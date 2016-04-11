<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
 $supressed = array("nadda");
 $supressed_list = "";
 foreach($supressed as $val){
	$supressed_list .= " '$val'";
 }
	$supressed_list = str_replace("' '","', '",$supressed_list);
 $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
 mysql_select_db(DB_NAME,$link);
 $nsupressed_sql = "SELECT COUNT(DISTINCT(`server_name`)) AS total_needing_patched FROM `patches` WHERE `package_name` NOT IN (SELECT `package_name` FROM `supressed`) AND package_name !='';";
 $nsupressed_res = mysql_query($nsupressed_sql);
 $nsupressed_row = mysql_fetch_array($nsupressed_res);
 $nsupressed_total = $nsupressed_row['total_needing_patched'];
 $sql1 = "select * from servers where trusted = 1;";
 $res1 = mysql_query($sql1);
 $table = "";
 $total_count = 0;
 $server_count = 0;
 $base_path=BASE_PATH;
 while ($row1 = mysql_fetch_assoc($res1)){
     $server_count++;
     $server_name = $row1['server_name'];
     $server_alias = $row1['server_alias'];
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
                  <td><span class='btn btn-success btn-xs'>Up to date :)</span></td></td>
                  <td></td>
                </tr>
";
     } else {
     $table .= "                <tr>
                  <td width='40px'><img src='$dist_img' class='avatar'></td>
                  <td><a href='{$base_path}patches/server/$server_name'>$server_alias</a></td>
                  <td><a href='{$base_path}patches/server/$server_name' class='btn btn-default btn-xs'> $count</a></td>
                  <td><span class='btn btn-danger btn-xs'>High $count4</span> | <span class='btn btn-warning btn-xs'>Medium $count2</span> | <span class='btn btn-info btn-xs'>Low $count3</span> | <span class='btn btn-primary btn-xs'>Unknown $count5</span></td>
                </tr>
";
     }
 }
 mysql_close($link);
$percent_needing_upgrade = round((($nsupressed_total / $server_count)*100));
$percent_good_to_go = 100 - $percent_needing_upgrade;
if ($percent_good_to_go < 0){
    $percent_good_to_go = 0;
}
?>

<div class="col-md-2 col-sm-2 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>Servers needing updates</h2>
      <div class="clearfix"></div>
    </div>
    <div class="progress">
       <div class="progress-bar progress-bar-danger" data-transitiongoal="<?php echo $nsupressed_total;?>" aria-valuemax="<?php echo $server_count;?>">
	<?php echo $nsupressed_total."/".$server_count;?>
       </div>
    </div>
  </div>
</div>
        <div class="col-sm-10 col-md-10 col-xs-12 main">
          <div class="x_panel">
            <div class="x_title">
              <h2>Patch List</h2>
              <div class="clearfix"></div>
            </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th width='40px'></th>
                  <th>Server Name</th>
                  <th>Patches</th>
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
