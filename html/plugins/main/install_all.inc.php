<?php

session_start();

include '../../lib/db_config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == "true") {

  $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

  $reboot = filter_input(INPUT_GET, 'reboot', FILTER_SANITIZE_NUMBER_INT);

  $selected = filter_input(INPUT_GET, 'selected', FILTER_SANITIZE_NUMBER_INT);

  if (isset($_GET['patch_id'])) {

    $patch_ids = $_GET['patch_id'];

  }

  $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_NUMBER_INT);





  if (isset($search) && isset($_GET['p_id'])) {



    foreach ($_GET['p_id'] as $value) {



      $srv_id = explode(":",$value)[1];

      $patch_id = explode(":",$value)[0];





      $sql = "SELECT * FROM `servers` WHERE `id`=$srv_id LIMIT 1;";

      $sql2 = "SELECT package_name FROM `patches` WHERE `id`=$patch_id LIMIT 1;";

      $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);

      mysql_select_db(DB_NAME, $link);

      if (isset($reboot) && !empty($reboot) && $reboot == 1) {

        $reboot_sql = "UPDATE `servers` SET `reboot_cmd_sent`=1 WHERE `id`=$id LIMIT 1;";

        mysql_query($reboot_sql);

        $message_injection = "Server also set to reboot after installing patches.";

      } else {

        $message_injection = "";

      }

      $res = mysql_query($sql);

      $row1 = mysql_fetch_array($res);

      $server_name = $row1['server_name'];



      $res2 = mysql_query($sql2);

      $row2 = mysql_fetch_array($res2);



      $patch_update_array[$srv_id]['server_name'] = $server_name;

      $patch_update_array[$srv_id]['server_id'] = $srv_id;

      $patch_update_array[$srv_id]['packages'][] = $row2['package_name'];



      $suppression_sql = "SELECT * FROM `supressed` WHERE `server_name` IN (0,'$server_name');";

      $suppression_res = mysql_query($sql);

      $suppression_array = array();

      while ($suppression_row = mysql_fetch_assoc($suppression_res)) {

        $suppression_array[] = "'" . $suppression_row['package_name'] . "'";

      }

      $suppression_list = implode(", ", $suppression_array);

      $sql3 = "UPDATE `patches` SET `to_upgrade`=1 WHERE `to_upgrade`=0 AND `server_name`='$server_name' AND `upgraded`=0 AND `id`=$patch_id;";



      mysql_query($sql3);

    }



    foreach ($patch_update_array as $server) {

      $server_id = $server['server_id'];

      $user_id = $_SESSION['user_id_num'];

      $log_body = 'Packages updated:';

      $log_packages = "";

      foreach ($server['packages'] as $package) {

        $log_packages .= " '".$package."'";

      }

      $log_packages = str_replace("' '", " ,", $log_packages);

      $log_packages = str_replace("'", "", $log_packages);

      $log_body .= $log_packages;

      $datetime = date_create()->format('Y-m-d H:i:s');

      $log_sql = "INSERT INTO `log` (`type`,`user_id`,`server_id`, `created`) VALUES ('package_update',$user_id,$server_id, '".$datetime."');";

      $log_sql2 = "INSERT INTO `log_body` (`id`,`log_body`) VALUES (LAST_INSERT_ID(),'".$log_body."');";

      mysql_query($log_sql);

      mysql_query($log_sql2);

    }

    $_SESSION['good_notice'] = "All selected packages set to upgrade on selected machines. $message_injection Bionic machine closer than I thought.";

    header('location:' . BASE_PATH . "patches");

    exit();



  }





  if (isset($id) && !empty($id) && is_numeric($id)) {

    $sql = "SELECT * FROM `servers` WHERE `id`=$id LIMIT 1;";

    $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);

    mysql_select_db(DB_NAME, $link);

    if (isset($reboot) && !empty($reboot) && $reboot == 1) {

      $reboot_sql = "UPDATE `servers` SET `reboot_cmd_sent`=1 WHERE `id`=$id LIMIT 1;";

      mysql_query($reboot_sql);

      $message_injection = " Server also set to reboot after installing patches.";

      $log_type_value = "package_update_reboot";

    } else {

      $message_injection = "";

      $log_type_value = "package_update";

    }

    $res = mysql_query($sql);

    $row1 = mysql_fetch_array($res);

    $server_name = $row1['server_name'];

    $server_id = $row1['id'];

    if (isset($selected)) {

      for ($i=0;$i<count($patch_ids);$i++){

        $patch_id_where[] = "'" . $patch_ids[$i] . "'";

      }

      $patch_id_list = implode(", ", $patch_id_where);

      $sql3 = "UPDATE `patches` SET `to_upgrade`=1 WHERE `to_upgrade`=0 AND `server_name`='$server_name' AND `upgraded`=0 AND `id` IN ($patch_id_list);";

      $package_list_sql = "SELECT package_name from `patches` where `to_upgrade`=0 AND `server_name`='$server_name' AND `upgraded`=0 AND `id` IN ($patch_id_list);";

    } else {

      $suppression_sql = "SELECT * FROM `supressed` WHERE `server_name` IN (0,'$server_name');";

      $suppression_res = mysql_query($sql);

      $suppression_array = array();

      while ($suppression_row = mysql_fetch_assoc($suppression_res)) {

        $suppression_array[] = "'" . $suppression_row['package_name'] . "'";

      }

      $suppression_list = implode(", ", $suppression_array);

      $sql3 = "UPDATE `patches` SET `to_upgrade`=1 WHERE `to_upgrade`=0 AND `server_name`='$server_name' AND `upgraded`=0 AND `package_name` NOT IN ($suppression_list);";

      $package_list_sql = "SELECT package_name from `patches` where `to_upgrade`=0 AND `server_name`='$server_name' AND `upgraded`=0 AND `package_name` NOT IN ($suppression_list);";

    }



    $res2 = mysql_query($package_list_sql);

    $packages = array();

    while ($row2 = mysql_fetch_array($res2)) {

      $packages[] = $row2['package_name'];

    }



    mysql_query($sql3);



    $user_id = $_SESSION['user_id_num'];

    $log_body = 'Packages updated:';

    $log_packages = "";

    foreach ($packages as $package) {

      $log_packages .= " '".$package."'";

    }

    $log_packages = str_replace("' '", ", ", $log_packages);

    $log_packages = str_replace("'", "", $log_packages);

    $log_body .= $log_packages;

    $log_body .= ".".$message_injection;

    $datetime = date_create()->format('Y-m-d H:i:s');

    $log_sql = "INSERT INTO `log` (`type`,`user_id`,`server_id`, `created`) VALUES ('$log_type_value',$user_id,$server_id, '".$datetime."');";

    $log_sql2 = "INSERT INTO `log_body` (`id`,`log_body`) VALUES (LAST_INSERT_ID(),'".$log_body."');";

    mysql_query($log_sql);

    mysql_query($log_sql2);



    //echo $log_sql." ".$log_body;

    $_SESSION['good_notice'] = "All non-suppressed packages set to upgrade on <strong>$server_name</strong>.$message_injection Bionic machine closer than I thought.";

    header('location:' . BASE_PATH . "patches/server/$server_name");

    exit();



  }

  mysql_close($link);



} else {

  session_unset();

  header('location:' . BASE_PATH);

  exit();

}


