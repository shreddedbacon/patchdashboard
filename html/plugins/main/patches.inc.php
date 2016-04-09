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
                  <td><a href='{$base_path}patches/server/$server_name'><img src='$dist_img' height='32' width='32' border='0'>&nbsp;$server_alias</a></td>
                  <td><span class='label label-success'>Up to date :)</span></td>
                </tr>
";
     } else {
     $table .= "                <tr>
                  <td><a href='{$base_path}patches/server/$server_name'><img src='$dist_img' height='32' width='32' border='0'>&nbsp;$server_alias</a></td>
                  <td><span class='label label-default'>Total $count</span> | <span class='label label-danger'>High $count4</span> | <span class='label label-warning'>Medium $count2</span> | <span class='label label-info'>Low $count3</span> | <span class='label label-primary'>Unknown $count5</span></td>
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
        <div class="col-sm-9 col-md-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Patch List</h1>
	    <div class="chart">
                <div class="percentage" data-percent="<?php echo $percent_good_to_go;?>"><span><?php echo $percent_good_to_go;?></span>%</div>
                <div class="label" style="color:#0000FF">Percent of servers not needing upgrades/patches</div>
            </div>
            <div class="goal-wrapper">
                                  <span class="gauge-value pull-left">$</span>
                                  <span id="gauge-text" class="gauge-value pull-left">3,200</span>
                                  <span id="goal-text" class="goal-value pull-right">$5,000</span>
                                </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Server Name (<?php echo $server_count;?> servers)</th>
                  <th>Patch Count (<?php echo $total_count;?> total patches available)</th>
                </tr>
              </thead>
              <tbody>
                <?php echo $table;?>
              </tbody>
            </table>
          </div>
        </div>
        <script type="text/javascript" src="<?php echo BASE_PATH; ?>js/gauge/gauge.min.js"></script>
        <script type="text/javascript" src="<?php echo BASE_PATH; ?>js/gauge/gauge_demo.js"></script>
