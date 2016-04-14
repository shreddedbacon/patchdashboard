<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active"){
  exit();
}
$base_path=BASE_PATH;
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

$os_counts_sql = "SELECT count(distro_version.version_num) as count, distro_version.version_num, distro_version.distro_id from servers inner join distro_version on servers.distro_version=distro_version.id group by distro_version.version_num;";
$os_counts_res = mysql_query($os_counts_sql);
$os_table = '';
while ($os_counts_row = mysql_fetch_assoc($os_counts_res)) {
  $distro_id = $os_counts_row['distro_id'];
  $dist_sql = "SELECT icon_path FROM distro WHERE id='$distro_id';";
  $dist_res = mysql_query($dist_sql);
  $dist_row = mysql_fetch_array($dist_res);
  $dist_img = BASE_PATH.$dist_row['icon_path'];
  $os_table .= "                <tr>
  <td width='40px'><img src='$dist_img' class='avatar'></td>
  <td>".$os_counts_row['version_num']."</td>
  <td>".$os_counts_row['count']."</td>
  </tr>";
}

$total_servers = 0;
$trusted_servers = 0;
$deactivated_servers = 0;
$last_seen_servers = 0;
$last_seen_servers_dead = 0;

$total_counts_sql = "SELECT count(server_name) AS count FROM servers;";
$total_counts_res = mysql_query($total_counts_sql);
$total_counts_row = mysql_fetch_assoc($total_counts_res);
$total_servers = $total_counts_row['count'];

$trusted_counts_sql = "SELECT count(server_name) AS count FROM servers WHERE trusted=1;";
$trusted_counts_res = mysql_query($trusted_counts_sql);
$trusted_counts_row = mysql_fetch_assoc($trusted_counts_res);
$trusted_servers = $trusted_counts_row['count'];

$deactivated_counts_sql = "SELECT count(server_name) AS count FROM servers WHERE trusted=0;";
$deactivated_counts_res = mysql_query($deactivated_counts_sql);
$deactivated_counts_row = mysql_fetch_assoc($deactivated_counts_res);
$deactivated_servers = $deactivated_counts_row['count'];

$lastseen_counts_sql = "SELECT count(server_name) AS count FROM servers WHERE  trusted=1 AND last_seen > NOW() - INTERVAL 15 MINUTE;";
$lastseen_counts_res = mysql_query($lastseen_counts_sql);
$lastseen_counts_row = mysql_fetch_assoc($lastseen_counts_res);
$last_seen_servers = $lastseen_counts_row['count'];
$last_seen_servers_dead = $trusted_servers - $last_seen_servers;


$sg_counts_sql = "SELECT server_group.server_group, server_group.id, count(servers.server_group) as count FROM servers JOIN server_group WHERE servers.server_group=server_group.id GROUP BY server_group.server_group;";
$sg_counts_res = mysql_query($sg_counts_sql);
$sg_table = '';
while ($sg_counts_row = mysql_fetch_assoc($sg_counts_res)) {

  $patch_list_sql = "SELECT count(*) as total_found FROM `patches` p LEFT JOIN servers s on s.server_name = p.server_name WHERE s.trusted = 1 and p.upgraded=0 and p.package_name !='' and s.server_group=".$sg_counts_row['id'].";";
  $patch_list_res = mysql_query($patch_list_sql);
  $patch_list_row = mysql_fetch_array($patch_list_res);
  $patches_to_apply_count = $patch_list_row['total_found'];
  $btnclass = '';
  if ($patches_to_apply_count == 0) {
    $btnclass='btn-success';
  } else {
    $btnclass='btn-warning';
  }
  $sg_table .= "                <tr>
  <td><a href='{$base_path}patches?server_group[]=".$sg_counts_row['id']."'>".$sg_counts_row['server_group']."</td>
  <td>".$sg_counts_row['count']."</td>
  <td><a href='{$base_path}patches?server_group[]=".$sg_counts_row['id']."' class='btn btn-xs $btnclass'> $patches_to_apply_count </a></td>
  </tr>";
}

$sql1 = "select * from servers where trusted = 1;";
$res1 = mysql_query($sql1);
$table = "";
$total_count = 0;
$server_count = 0;
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
    <td><span class='btn btn-success btn-xs'>Up to date :)</span></td>
    <td></td>
    </tr>
    ";
  } else {
    $table .= "                <tr>
    <td width='40px'><img src='$dist_img' class='avatar'></td>
    <td><a href='{$base_path}patches/server/$server_name'>$server_alias</a></td>
    <td><a href='{$base_path}patches/server/$server_name' class='btn btn-default btn-xs'> $count</a></td>
    <td><span class='btn btn-danger btn-xs'> $count4 <i class='fa fa-exclamation-triangle'></i> </span>
    <span class='btn btn-warning btn-xs'> $count2 <i class='fa fa-exclamation-triangle'></i> </span>
    <span class='btn btn-info btn-xs'> $count3 <i class='fa fa-exclamation-triangle'></i> </span>
    <span class='btn btn-primary btn-xs'> $count5 <i class='fa fa-question-circle'></i> </span></td>
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
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><i class='fa fa-exclamation-triangle'></i> Requiring Updates</h2>
        <div class="clearfix"></div>
      </div>
      <div class="progress">
        <div class="progress-bar progress-bar-warning" data-transitiongoal="<?php echo $nsupressed_total;?>" aria-valuemax="<?php echo $total_servers;?>">
          <?php echo $nsupressed_total."/".$total_servers;?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><i class='fa fa-check'></i> Activated Servers</h2>
        <div class="clearfix"></div>
      </div>
      <div class="progress">
        <div class="progress-bar progress-bar-success" data-transitiongoal="<?php echo $trusted_servers;?>" aria-valuemax="<?php echo $total_servers;?>">
          <?php echo $trusted_servers."/".$total_servers;?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><i class='fa fa-ban'></i> Deactivated Servers</h2>
        <div class="clearfix"></div>
      </div>
      <div class="progress">
        <div class="progress-bar progress-bar-danger" data-transitiongoal="<?php echo $deactivated_servers;?>" aria-valuemax="<?php echo $total_servers;?>">
          <?php echo $deactivated_servers."/".$total_servers;?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><i class='fa fa-eye-slash'></i> Not Checked In</h2>
        <div class="clearfix"></div>
      </div>
      <div class="progress">
        <div class="progress-bar progress-bar-danger" data-transitiongoal="<?php echo $last_seen_servers_dead;?>" aria-valuemax="<?php echo $total_servers;?>">
          <?php echo $last_seen_servers_dead."/".$total_servers;?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><i class='fa fa-linux'></i> Operating Systems</h2>
        <div class="clearfix"></div>
      </div>
      <table class="table table-striped jambo_table">
        <thead>
          <tr>
            <th width='40px'></th>
            <th>OS</th>
            <th>Count</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $os_table; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
      <div class="x_title">
        <h2><i class='fa fa-files-o'></i> Server Groups</h2>
        <div class="clearfix"></div>
      </div>
      <table class="table table-striped jambo_table">
        <thead>
          <tr>
            <th>Server Group</th>
            <th>Count</th>
            <th>Patches</th>
          </tr>
        </thead>
        <tbody>
          <?php echo $sg_table; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
