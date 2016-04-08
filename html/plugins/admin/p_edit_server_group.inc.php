<?php
session_start();
include '../../lib/db_config.php';
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    if (isset($_POST)) {
        $server_group = filter_input(INPUT_POST, 'server_group', FILTER_SANITIZE_SPECIAL_CHARS);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $sql_array = array();
        if (isset($server_group) && !empty($server_group) && isset($id) && !empty($id) && is_numeric($id)){
            $sql_array[] = "`server_group`='$server_group'";
            $replacement_parts = implode(", ", $sql_array);
            $sql = "UPDATE `server_group` SET $replacement_parts WHERE `id`='$id';";
            $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
            mysql_select_db(DB_NAME,$link);
            mysql_query($sql);
            mysql_close($link);
            $_SESSION['good_notice'] = "$server_group modified! I got no joke for this one. Sad day.";
            sleep(1);
            header('location:'.BASE_PATH."edit_server_group?id=$id");
        }
        else{
            $_SESSION['error_notice'] = "A required field was not filled in";
        }
    }
    else{
        header('location:'.BASE_PATH."edit_user?id=$id");
        exit();
    }
}
else{
    $_SESSION['error_notice'] = "You do not have permission to add users. This even thas been logged, and the admin has been notified.";
    header('location:'.BASE_PATH);
    exit();
}
?>
