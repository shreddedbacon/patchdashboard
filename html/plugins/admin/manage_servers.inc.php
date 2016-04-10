<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$link);
$sql = "SELECT * FROM servers;";
$res = mysql_query($sql);
$table = "";
$distro_array = array();
$distro_map_sql = "SELECT d.distro_name as distro_name,dv.version_num as version_num, dv.id as version_id,d.id as distro_id FROM distro_version dv LEFT JOIN distro d on d.id=dv.distro_id;";
$distro_map_res = mysql_query($distro_map_sql);
while ($distro_map_row = mysql_fetch_assoc($distro_map_res)){
    $distro_array[$distro_map_row['distro_id']][$distro_map_row['version_id']] = str_replace("_"," ",$distro_map_row['distro_name']." ".$distro_map_row['version_num']);
}

$sg_sql = "SELECT * FROM server_group;";
$sg_res = mysql_query($sg_sql);
$sg_array = array();
$sg_count = 0;
while ($sg_row = mysql_fetch_assoc($sg_res)){
  $sg_array[$sg_count]['id'] = $sg_row['id'];
  $sg_array[$sg_count]['server_group'] = $sg_row['server_group'];
  $sg_count++;
}

while ($row = mysql_fetch_assoc($res)){
    $id = $row['id'];
    $server_name = $row['server_name'];
    $server_alias = $row['server_alias'];
    $server_group2 = $row['server_group'];
    for ($sg=0;$sg<=count($sg_array);$sg++) {
      if ($server_group2 == $sg_array[$sg]['id']) {
        $server_group = $sg_array[$sg]['server_group'];
      }
    }

    $distro_id = $row['distro_id'];
    $server_ip = $row['server_ip'];
    $distro_version = $row['distro_version'];
    $distro_name = $distro_array[$distro_id][$distro_version];
    $client_key = $row['client_key'];
    $trusted = $row['trusted'];
    if ($trusted == 0){
        $trust = "NO";
    }
    else{
        $trust = "YES";
    }
    $last_seen = $row['last_seen'];
    if ($last_seen == "0000-00-00 00:00:00"){
        $last_seen = "Never";
    }

        if ($trusted == 1){
                $active_action = "<a class='btn btn-xs btn-warning' href='".BASE_PATH."plugins/admin/deactivate_server.inc.php?id=$id'><i class='fa fa-ban'></i> Deactivate/Distrust </a>";
        }
        else{
                $active_action = "<a class='btn btn-xs btn-success' href='".BASE_PATH."plugins/admin/activate_server.inc.php?id=$id'><i class='fa fa-check'></i> Reactivate/Trust </a>";
        }
    $table .="                          <tr>
					<td><span title=$server_name>$server_alias</span></td>
					<td>$server_group</td>
                                        <td>$distro_name</td>
                                        <td>$server_ip</td>
                                        <td>$trust</td>
                                        <td>$last_seen</td>
                                        <td><a class='btn btn-xs btn-info' href='".BASE_PATH."edit_server?id=$id'><i class='fa fa-pencil'></i> Edit </a> $active_action <a class='btn btn-xs btn-danger' href='".BASE_PATH."plugins/admin/delete_server.inc.php?id=$id'><i class='fa fa-trash-o'></i> Delete </a></td>
                                </tr>
";
}
?>
<div class="col-sm-10 col-md-10 col-xs-12 main">
  <div class="x_panel">
    <div class="x_title">
      <h2>All Servers</h2>
      <div class="clearfix"></div>
    </div>
        <div class="container">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Server Name (Alias)</th>
                  <th>Server Group</th>
                  <th>Distro</th>
                  <th>Server IP</th>
                  <th>Trusted?</th>
                  <th>Last Check-in</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
<?php echo $table;?>
              </tbody>
            </table>
          </div>
        </div>
</div>
</div>
