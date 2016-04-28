<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active"){
  exit();
}
include 'inc/supressed_patches.inc.php';
if (!isset($supressed)){
  $supressed = array();
}
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
$package_count = 0;
$base_path=BASE_PATH;
mysql_select_db(DB_NAME,$link);
$server_name = filter_var($_GET['server'],FILTER_SANITIZE_MAGIC_QUOTES);
$distro_sql1 = "SELECT * from servers where server_name='$server_name';";
$distro_res1 = mysql_query($distro_sql1);
$distro_row1 = mysql_fetch_array($distro_res1);
$server_alias = $distro_row1['server_alias'];
$server_id = $distro_row1['id'];
$distro_id = $distro_row1['distro_id'];
$id = $distro_row1['id'];
$distro_sql2 = "SELECT * from distro where id=$distro_id limit 1;";
$distro_res2 = mysql_query($distro_sql2);
$distro_row2 = mysql_fetch_array($distro_res2);
$apt_cmd = $distro_row2['upgrade_command'];
$sql1 = "select * from patches where server_name='$server_name';";
$res1 = mysql_query($sql1);
$table = "";
while ($row1 = mysql_fetch_assoc($res1)){
  $package_name = $row1['package_name'];
  $package_name_orig = $package_name;
  if (in_array($package_name,$supressed)){
    $package_name .= " <strong>(SUPRESSED)</strong>";
  }
  else{
    $apt_cmd .= " $package_name";
    $package_count++;
  }
  $current = $row1['current'];
  $new = $row1['new'];
  $urgency = $row1['urgency'];
  $bug_url = $row1['bug_url'];
  if ($bug_url != ''){
    if (stristr($bug_url,'debian')){
      $url_array = explode("/",$bug_url);
      $cve = end($url_array);
      $url = "<td><a href='$bug_url' style='color:black'>Debian $cve</a></td>";
    }
    else{
      $url_array = explode("/",$bug_url);
      $bug = end($url_array);
      $url = "<td><a href='$bug_url' style='color:black'>Launchpad Bug #$bug</a></td>";
    }
  } else { $url = "<td>&nbsp;</td>"; }
  if (in_array($urgency,array('high','emergency'))){
    //$urgency = "<td style='color:red'><a href='http://www.ubuntuupdates.org/package/core/trusty/main/updates/$package_name_orig' style='color:red' target='_blank'>$urgency</a></td>";
    $urgency = '<td><span class="label label-danger">'.$urgency.'</span></td>';
  }
  elseif ($urgency == "medium"){
    //$urgency = "<td style='color:#FF8C00'><a href='http://www.ubuntuupdates.org/package/core/trusty/main/updates/$package_name_orig' style='color:#FF8C00' target='_blank'>medium</a></td>";
    $urgency = '<td><span class="label label-warning">'.$urgency.'</span></td>';
  }
  elseif ($urgency == "low") {
    //$urgency = "<td><a href='http://www.ubuntuupdates.org/package/core/trusty/main/updates/$package_name_orig' style='color:black' target='_blank'>$urgency</a></td>";
    $urgency = '<td><span class="label label-info">'.$urgency.'</span></td>';
  }
  else{
    //$urgency = "<td>$urgency</td>";
    $urgency = '<td><span class="label label-primary">'.$urgency.'</span></td>';
  }
  $table .= "                <tr>
  <td><input type='checkbox' name='patch_id[]' value='".$row1['id']."' class='flat' id='check_box'></td>
  <td><a href='${base_path}search/exact/$package_name_orig' style='color:green'>$package_name</a></td>
  <td>$current</td>
  <td>$new</td>
  $urgency " . (isset($url) ? $url : '') . "
  </tr>
  ";
}
if ($package_count == 0){
  $apt_cmd = "";
  $table .= "                <tr>
  <td colspan='6'><center>No Packages to Update</center></td>
  </tr>
  ";
}
else{
  $apt_cmd = "<pre class='pre-scrollable'>".$apt_cmd."</pre>";
}

$log_sql = "SELECT * FROM `log` AS l LEFT JOIN `log_body` AS lb ON l.id=lb.id WHERE `server_id`=$server_id ORDER BY l.created DESC LIMIT 10;";
$log_res = mysql_query($log_sql);
$logs = "";
while ($row1 = mysql_fetch_assoc($log_res)){
  $user_id = $row1['user_id'];
  $user_sql = "SELECT user_id, display_name FROM `users` WHERE `id`=$user_id LIMIT 1;";
  $user_res = mysql_query($user_sql);
  $user_info = mysql_fetch_assoc($user_res);
  if (is_null($user_info['display_name']) || empty($user_info['display_name'])) {
    $user_name = $user_info['user_id'];
  } else {
    $user_name = $user_info['display_name'];
  }
  $log_type = $row1['type'];
  $log_created = $row1['created'];
  $log_body = $row1['log_body'];
  $logs .= "<tr>
  <td>$log_type</td>
  <td>$log_created</td>
  <td>$user_name</td>
  <td>$log_body</td>
  </tr>";
}

if (empty($logs)) {
  $logs = "<tr>
  <td colspan='4'><center>No logs to display</center></td>
  </tr>";
}


//$services_sql = "SELECT * FROM `services` WHERE `server_id`=$server_id;";
$services_sql = "SELECT sl.service_name, sl.service_cmd, s.id FROM `services` AS s JOIN `service_list` AS sl ON s.service_id=sl.id WHERE s.server_id=$server_id;";

$services_res = mysql_query($services_sql);
$services = "";
$service_count = 0;
while ($row1 = mysql_fetch_assoc($services_res)){
  $service_name = $row1['service_name'];
  $service_cmd = $row1['service_cmd'];
  $service_id = $row1['id'];
  $services .= "<tr>
  <td><input type='checkbox' name='service_id[]' value='$service_id' class='flat' id='check_box'></td>
  <td>$service_name</td>
  <td>$service_cmd</td>
  </tr>";
  $service_count++;
}

if (empty($services)) {
  $services = "<tr>
  <td colspan='3'><center>No services configured</center></td>
  </tr>";
}

mysql_close($link);
?>
<div class="col-sm-12 col-md-12 col-xs-12 main">
  <div class="x_panel">
    <div class="" role="tabpanel" data-example-id="togglable-tabs">
      <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
        <li role="presentation" class="active"><a href="#tab_content1" id="patch-tab" role="tab" data-toggle="tab" aria-expanded="true">Patches</a>
        </li>
        <li role="presentation" class=""><a href="#tab_content2" role="tab" id="services-tab" data-toggle="tab"  aria-expanded="false">Services</a>
        </li>
        <li role="presentation" class=""><a href="#tab_content3" role="tab" id="log-tab" data-toggle="tab"  aria-expanded="false">Logs</a>
        </li>
      </ul>
      <div id="myTabContent" class="tab-content">
        <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="patch-tab">
          <!--tab-->


          <div class="x_title">
            <h2>List Packages to Install</h2>
            <div class="clearfix"></div>
          </div>
          <h3><?php echo $server_alias;?>(<a href="<?php echo BASE_PATH;?>packages/server/<?php echo $server_name;?>">List all installed packages</a>)</h3>
          <form action="<?php echo BASE_PATH;?>plugins/main/install_all.inc.php" method="get"><input type="hidden" value="<?php echo $id;?>" name="id">
            <p align="center">
              <?php if ($package_count != 0) { ?>
              <button type="submit" class="btn btn-primary" name="selected">Install selected patches</button> | <a class="btn btn-success" href="<?php echo BASE_PATH;?>plugins/main/install_all.inc.php?id=<?php echo $id;?>">Install all patches not suppressed</a> | <a class="btn btn-danger" href="<?php echo BASE_PATH;?>plugins/main/install_all.inc.php?reboot=1&id=<?php echo $id;?>">Install all patches not suppressed and reboot</a>
              <?php } ?>
              </p>
              <div class="container">
                <div class="table-responsive">
                  <table class="table table-striped responsive-utilities jambo_table bulk_action">
                    <thead>
                      <tr>
                        <th><input type="checkbox" id="check-all" class="flat"></th>
                        <th class="column-title">Package Name</th>
                        <th class="column-title">Current Version</th>
                        <th class="column-title">New Version</th>
                        <th class="column-title">Urgency Level</th>
                        <th class="column-title">Bug Report Name/Page</th>
                        <th class="bulk-actions" colspan="5">
                          <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php echo $table;?>
                    </tbody>
                  </table></div></form>
                  <?php echo $apt_cmd;?>
                </div>
                <!--tab-->
              </div>

			  <div role="tabpanel" class="tab-pane fade in" id="tab_content2" aria-labelledby="services-tab">
                <!--tab-->
                <div class="x_title">
                  <h2>Services</h2>
                  <div class="clearfix"></div>
                </div>
                <form action="<?php echo BASE_PATH;?>plugins/main/service_restart.inc.php" method="post"><input type="hidden" value="<?php echo $id;?>" name="server_id">
                <p align="center">
                <?php if ($service_count != 0) { ?>
                <button type="submit" class="btn btn-primary" name="selected">Restart selected services</button>
                <?php } ?>
                </p>
                <div class="container">
                  <div class="table-responsive">
                    <table class="table table-striped jambo_table">
                      <thead>
                        <tr>
                          <th><input type="checkbox" id="check-all" class="flat"></th>
                          <th class="column-title">Service Name</th>
                          <th class="column-title">Service Cmd</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php echo $services;?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!--tab-->
              </div>

              <div role="tabpanel" class="tab-pane fade in" id="tab_content3" aria-labelledby="log-tab">
                <!--tab-->
                <div class="x_title">
                  <h2>Last 10 Log Entries</h2>
                  <div class="clearfix"></div>
                </div>
                <div class="container">
                  <div class="table-responsive">
                    <table class="table table-striped jambo_table">
                      <thead>
                        <tr>
                          <th class="column-title" width="10%">Type</th>
                          <th class="column-title" width="10%">Created</th>
                          <th class="column-title" width="10%">User</th>
                          <th class="column-title" width="70%">Log</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php echo $logs;?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!--tab-->
              </div>
            </div>
          </div>
        </div>
      </div>
