<?php
session_start();
include '../../lib/db_config.php';
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    if (isset($_POST)) {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $service_name = filter_input(INPUT_POST, 'service_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $service_cmd = filter_input(INPUT_POST, 'service_cmd', FILTER_SANITIZE_SPECIAL_CHARS);
        $server_ids = $_POST['server_id']; //filter_input_array(INPUT_POST, 'server_id', FILTER_SANITIZE_SPECIAL_CHARS);
        
        if (isset($service_name) && !empty($service_name) && isset($id) && !empty($id)){
            $replacement_parts = implode(", ", $sql_array);
            $sql = "UPDATE `service_list` SET service_name='$service_name', service_cmd='$service_cmd' WHERE `id`=$id LIMIT 1;";
            $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
            mysql_select_db(DB_NAME,$link);
            mysql_query($sql);
			
			$sql_del = "DELETE FROM `services` WHERE `service_id`=$id;";
			mysql_query($sql_del);
			
			//$sql_add = print_r($server_ids);
			for ($i=0;$i<count($server_ids);$i++) {
				$server_id = $server_ids[$i];
				$sql_add = "INSERT INTO `services` (`server_id`,`service_id`) VALUES ($server_id,$id);";
				mysql_query($sql_add);
			}		
			
            mysql_close($link);
			
            $_SESSION['good_notice'] = "$service_name modified! That wasn't so bad, now was it?";
            sleep(1);
            header('location:'.BASE_PATH."edit_services?id=$id");
        }
        else{
            $_SESSION['error_notice'] = "A required field was not filled in";
        }
    }
    else{
        header('location:'.BASE_PATH."edit_services?id=$id");
        exit();
    }
}
else{
    $_SESSION['error_notice'] = "You do not have permission to edit services. This even thas been logged, and the admin has been notified.";
    header('location:'.BASE_PATH);
    exit();
}
?>
