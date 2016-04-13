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

$table=''; //SET VARIABLE
if (isset($_GET['package'])) {
  $package_var = filter_var($_GET['package'],FILTER_SANITIZE_MAGIC_QUOTES);
} else {
  $package_var = "";
}

if (isset($_GET['group'])) {
  $group_var = filter_var_array($_GET['group'],FILTER_SANITIZE_MAGIC_QUOTES);
} else {
  $group_var = '';
}
$exact_checked = "";
if (isset($_GET['exact'])) {
  if ($_GET['exact'] == 'on') {
    $_GET['exact'] = true;
    $exact_checked = "checked";
  } else if ($_GET['exact'] == true) {
    $exact_checked = "checked";
  }
}
$update_checked = "";
if (isset($_GET['update'])) {
  $update_var = filter_var($_GET['update'],FILTER_SANITIZE_MAGIC_QUOTES);
  if ($update_var == 'on') {
    $update_var = true;
    $update_checked = "checked";
  }
} else {
  $update_var = false;
}
$package = $package_var;
$package_list = '';

$server_group="";
if (!empty($group_var)) {
  $sg_var = '';
  for ($i=0;$i<count($group_var);$i++) {
    $sg_var .= " '".$group_var[$i]."'";
  }
  $server_group = str_replace("' '",",",$sg_var);
  $server_group = str_replace("'","",$server_group);
  $server_group = str_replace(" ","",$server_group);
}
$updates_only = $update_var;
$package_group = $package_var;

$package_count = 0;
if (isset($package_group)) {
  //allow multiple packages
  $package_list = explode(",",$package_group);
  $package_group = '';
  $package_group_regex = '';
  $package_group_regex_exact = '';
  $package_group_exact = '';
  for ($i=0;$i<count($package_list);$i++) {
    $package_group .= " '".$package_list[$i]."'";
    $package_group_regex .= " '(.*)".$package_list[$i]."(.*)'";
    $package_group_regex_exact .= " '".$package_list[$i].":(.*)'";
    $package_group_exact .= " '".$package_list[$i]."'";
    $package_count++;
  }
  $package_group = str_replace("' '","', '", $package_group);
  $package_group_regex = str_replace("' '","|", $package_group_regex);
  $package_group_regex = str_replace("'","", $package_group_regex);
  $package_group_regex = str_replace(" ","", $package_group_regex);

  $package_group_regex_exact = str_replace("' '","|", $package_group_regex_exact);
  $package_group_regex_exact = str_replace("'","", $package_group_regex_exact);
  $package_group_regex_exact = str_replace(" ","", $package_group_regex_exact);


  $package_group_exact = str_replace("' '","', '", $package_group_exact);

  if ($package_count == 1) {
    $package = str_replace(" ","", $package_group);
    $package = str_replace("'","", $package);
  }
}

$server_group_map_sql = "SELECT * FROM server_group;";
$server_group_map_res = mysql_query($server_group_map_sql);
$select_html_sg = "<select class='form-control custom col-md-7 col-xs-12' name='group[]' multiple>";


$sg_array = explode(",",$server_group);

while ($server_group_map_row = mysql_fetch_assoc($server_group_map_res)) {
  $server_group_id = $server_group_map_row['id'];
  $server_group_name = $server_group_map_row['server_group'];
  $select_html_sg_2 = "<option value='".$server_group_name."'>".$server_group_name."</option>";
  for ($i=0;$i<count($sg_array);$i++) {
    if ($server_group_name == $sg_array[$i]) {
      $select_html_sg_2 = "<option value='".$server_group_name."' selected='selected'>".$server_group_name."</option>";
    }
  }
  $select_html_sg .= $select_html_sg_2;
}
$select_html_sg .= "\t\t\t\t</select>";

if (!empty($server_group)) {
  //allow multiple groups
  $server_group = "'".str_replace(",","', '",$server_group)."'";
  $sg_sql = "SELECT * FROM server_group WHERE server_group IN (".$server_group.");";
  $sg_res = mysql_query($sg_sql);
  $server_group = "";
  while ($sg_row = mysql_fetch_assoc($sg_res)) {
    $server_group .= " '".$sg_row['id']."'";
  }
  $server_group = str_replace("' '",", ",$server_group);
  $server_group = str_replace("'","",$server_group);

  $sql_server_group = "SELECT server_name from servers WHERE server_group IN (".$server_group.");";
  $sql_server_group_query = mysql_query($sql_server_group);
  $server_names = "";
  while ($rows2 = mysql_fetch_assoc($sql_server_group_query)) {
    $server_names .= " '".$rows2['server_name']."'";
  }
  $server_names = str_replace("' '","', '",$server_names);
}

$count = 0;
$allpackages=false;
if (empty($package)) {
  if (isset($server_names)) {
    $sql1 = "SELECT package_name, package_version, server_name FROM patch_allpackages where server_name IN (".$server_names.");";
    $allpackages=true;
    $res1 = mysql_query($sql1);
  }
} else {
  if (isset($_GET['exact']) && $_GET['exact'] == "true"){
    if($package_count > 1 && isset($server_names)) {
      $sql1 = "SELECT * FROM patch_allpackages where (package_name IN ($package_group_exact) or package_name REGEXP '$package_group_regex_exact') and server_name IN (".$server_names.");";
    } else if (isset($server_names)) {
      $sql1 = "SELECT * FROM patch_allpackages where (package_name = '$package' or package_name like '$package:%') and server_name IN (".$server_names.");";
    } else if($package_count > 1) {
      $sql1 = "SELECT * FROM patch_allpackages where package_name IN ($package_group_exact) or package_name REGEXP '$package_group_regex_exact';";
    } else {
      $sql1 = "SELECT * FROM patch_allpackages where package_name = '$package' or package_name like '$package:%';";
    }
  } else {
    if($package_count > 1 && isset($server_names)) {
      $sql1 = "SELECT * FROM patch_allpackages where package_name REGEXP '$package_group_regex' and server_name IN (".$server_names.");";
    } else if (isset($server_names)) {
      $sql1 = "SELECT * FROM patch_allpackages where package_name like '%$package%' and server_name IN (".$server_names.");";
    } else if($package_count > 1) {
      $sql1 = "SELECT * FROM patch_allpackages where package_name REGEXP '$package_group_regex';";
    } else {
      $sql1 = "select * from patch_allpackages where package_name like '%$package%';";
    }
  }
  $res1 = mysql_query($sql1);
}

// $res1 = mysql_query($sql1);
$base_path = BASE_PATH;
if (!empty($package_var) || $allpackages == true) {

  $sql_patch = "SELECT new,id,package_name,server_name FROM patches;";
  $sql_patch_avail = mysql_query($sql_patch);
  $patch_arr=0;
  while ($sql_patch_avail2 = mysql_fetch_assoc($sql_patch_avail)) {
    $sql_patch_array[$patch_arr] = $sql_patch_avail2;
    $patch_arr++;
  }


  function multidimensional_search($parents, $searched) {
    if (empty($searched) || empty($parents)) {
      return false;
    }

    foreach ($parents as $key => $value) {
      $exists = true;
      foreach ($searched as $skey => $svalue) {
        $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
      }
      if($exists){ return $key; }
    }

    return false;
  }

  while ($row1 = mysql_fetch_assoc($res1)){

    $package_name = explode(":",$row1['package_name'])[0];
    $package_version = $row1['package_version'];
    $server_name = $row1['server_name'];
    /*     $sql_patch = "SELECT new,id FROM patches WHERE package_name = '$package_name' AND server_name = '$server_name';";
    $sql_patch_avail = mysql_query($sql_patch);
    $sql_patch_avail = mysql_fetch_assoc($sql_patch_avail);
    */
    $sql_patch_new = '';
    /*     for ($i=0;$i<count($sql_patch_array);$i++) {
    if ($sql_patch_array[$i]['package_name'] == $package_name && $sql_patch_array[$i]['server_name'] == $server_name) {
    $sql_patch_new = $sql_patch_array[$i]['new'];
    $sql_patch_id = $sql_patch_array[$i]['id'];
  }
}
*/

//     $patch_array_key = array_search($server_name, array_column($sql_patch_array, 'server_name'));
$patch_array_key = multidimensional_search($sql_patch_array, array('server_name' => $server_name, 'package_name' => $package_name));
if (!empty($patch_array_key)) {
  $sql_patch_new = $sql_patch_array[$patch_array_key]['new'];
  $sql_patch_id = $sql_patch_array[$patch_array_key]['id'];
}

//     print_r($patch_array_key);
//     echo "<br>";
$patch_array_key = "";
$sql_server_id = "SELECT id FROM servers WHERE server_name = '$server_name';";
$sql_server_id2= mysql_query($sql_server_id);
$sql_server_id2 = mysql_fetch_assoc($sql_server_id2);

$update_avail = "";
$update_checkbox = "";
if (isset($updates_only)) {
  if ($updates_only == 'true') {
    $display_table = 'false';
  } else {
    $display_table = 'true';
  }
} else {
  $display_table = 'true';
}
//     if (!empty($sql_patch_avail)) {
if (!empty($sql_patch_new)) {
  $update_avail = " | <span class='label label-primary'>Update available : ".$sql_patch_new."</span>";
  $update_checkbox = "<input type='checkbox' name='p_id[".$count."]' value='".$sql_patch_id.":".$sql_server_id2['id']."' class='flat' id='check_box'>";
  $display_table = 'true';
} else {
  $update_avail = " | <span class='label label-success'>Up to date :)</span>";
}

if ($display_table == 'true') {
  $count++;
  $table .= "                <tr>
  <td>".$update_checkbox."<!--input type='hidden' name='p_id[".$count."][server_id]' value='".$sql_server_id2['id']."'--></td>
  <td><a href='${base_path}patches/server/$server_name' style='color:black'>$server_name</a></td>
  <td><a href='${base_path}search/exact/$package_name' style='color:green'>$package_name</a>".$update_avail."</td>
  <td>$package_version</td>
  </tr>";
}

}
}

mysql_close($link);

?>
<div class="col-lg-3 col-md-8 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>Advanced Search</h2>
      <div class="clearfix"></div>
    </div>
    <form action="<?php echo BASE_PATH;?>search" class="form-label-left">
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Package(s):</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="text" name="package" class="form-control col-lg-12 col-md-7 col-xs-12" required value='<?php echo $package;?>'>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Server Group:</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <?php echo $select_html_sg;?>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Updates only:</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="checkbox" name="update" class="checkbox flat" <?php echo $update_checked; ?>>
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Exact match:</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="checkbox" name="exact" class="checkbox flat" <?php echo $exact_checked; ?>>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
          <button class="btn btn-md btn-success btn-block" type="submit">Search</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>Search</h2>
      <div class="clearfix"></div>
    </div>
    <h3 class="sub-header">Results for search "<?php echo $package;?>" (<?php echo $count;?> found)</h3>
    <form action="<?php echo BASE_PATH;?>plugins/main/install_all.inc.php" method="get">
      <div class="table-responsive">
        <button type="submit" class="btn btn-primary" name="search">Install selected patches</button>
        <table class="table table-striped jambo_table bulk_action">
          <thead>
            <tr>
              <th><input type="checkbox" id="check-all" class="flat"></th>
              <th class="column-title">Server Name</th>
              <th class="column-title">Package Name</th>
              <th class="column-title">Package Version</th>
              <th class="bulk-actions" colspan="3">
                <a class="antoo" style="color:#fff; font-weight:500;">Bulk Actions ( <span class="action-cnt"> </span> ) <i class="fa fa-chevron-down"></i></a>
              </th>
            </tr>
          </thead>
          <tbody>
            <?php echo $table;?>
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>
