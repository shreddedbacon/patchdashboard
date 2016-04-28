<?php
session_start();
include '../../lib/db_config.php';
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == "true") {
  $server_id = filter_input(INPUT_POST, 'server_id', FILTER_SANITIZE_NUMBER_INT);
//  if (isset($_GET['service_id'])) {
//    $service_ids = $_GET['service_id'];
//  }
  if (isset($_POST['service_id'])) {
    $service_ids = $_POST['service_id'];
  }

  if (isset($service_ids) && !empty($service_ids) && isset($server_id) && !empty($server_id)) {

    for ($i=0;$i<count($service_ids);$i++){
      $service_ids_where[] = "'" . $service_ids[$i] . "'";
    }
    $service_id_list = implode(", ", $service_ids_where);
    $sql = "SELECT * FROM `services` AS s JOIN `service_list` AS sl ON s.service_id=sl.id WHERE s.server_id=$server_id AND s.id IN ($service_id_list);";
    $sql2 = "UPDATE `services` SET service_run=1 WHERE server_id=$server_id AND service_id IN ($service_id_list);";
    $sql3 = "UPDATE `servers` SET services_restart=1 WHERE id=$server_id;";
    $link = mysql_connect(DB_HOST, DB_USER, DB_PASS);
    mysql_select_db(DB_NAME, $link);

    $message_injection = "";
    $log_type_value = "service_restart";

    mysql_query($sql2);
    mysql_query($sql3);
    $res = mysql_query($sql);
    $services = array();
    while ($row = mysql_fetch_assoc($res)) {
      $services[] = $row['service_cmd'];
    }


    $user_id = $_SESSION['user_id_num'];
    $sql4 = "SELECT server_name FROM servers WHERE id=$server_id LIMIT 1;";
    $res4 = mysql_query($sql4);
    $row4 = mysql_fetch_assoc($res4);
    $server_name = $row4['server_name'];
    $log_body = 'Services restarted:';
    $log_services = "";
    foreach ($services as $service) {
      $log_services .= " '".$service."'";
    }
    $log_services = str_replace("' '", ", ", $log_services);
    $log_services = str_replace("'", "", $log_services);
    $log_body .= $log_services;
    $log_body .= ".".$message_injection;
    $datetime = date_create()->format('Y-m-d H:i:s');
    $log_sql = "INSERT INTO `log` (`type`,`user_id`,`server_id`, `created`) VALUES ('$log_type_value',$user_id,$server_id, '".$datetime."');";
    $log_sql2 = "INSERT INTO `log_body` (`id`,`log_body`) VALUES (LAST_INSERT_ID(),'".$log_body."');";
    mysql_query($log_sql);
    mysql_query($log_sql2);

    $_SESSION['good_notice'] = "All restarting services on <strong>$server_name</strong>.$message_injection Bionic machine closer than I thought.";
    header('location:' . BASE_PATH . "dashboard");
    exit();

  }
  mysql_close($link);

} else {
  session_unset();
  header('location:' . BASE_PATH);
  exit();
}
