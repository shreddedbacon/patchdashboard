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
      <canvas width="150" height="80" id="foo" class="" style="width: 160px; height: 100px;"></canvas>
      <div class="goal-wrapper">
          <span class="gauge-value pull-right">%</span>
          <span id="gauge-text" class="gauge-value pull-left">0</span>
          <span id="goal-text" class="goal-value pull-right">100%</span>
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
        <script type="text/javascript" src="<?php echo BASE_PATH; ?>js/gauge/gauge.min.js"></script>
        <script type="text/javascript">
        var opts = {
            lines: 12, // The number of lines to draw
            angle: 0, // The length of each line
            lineWidth: 0.4, // The line thickness
            pointer: {
                length: 0.75, // The radius of the inner circle
                strokeWidth: 0.042, // The rotation offset
                color: '#1D212A' // Fill color
            },
            limitMax: 'false', // If true, the pointer will not go past the end of the gauge
            colorStart: '#1ABC9C', // Colors
            colorStop: '#1ABC9C', // just experiment with them
            strokeColor: '#F0F3F3', // to see which ones work best for you
            generateGradient: true
        };
        var target = document.getElementById('foo'); // your canvas element
        var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
        gauge.maxValue = 100; // set max gauge value
        gauge.animationSpeed = 32; // set animation speed (32 is default value)
        gauge.set(1); // set pre value to fix 0 issue
        gauge.set(<?php echo $percent_needing_upgrade;?>); // set actual value
        gauge.setTextField(document.getElementById("gauge-text"));
        </script>
