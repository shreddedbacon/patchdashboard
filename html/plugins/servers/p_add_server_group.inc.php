<?php
session_start();
include '../../lib/db_config.php';
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) {
    if (isset($_POST)) {
        $server_group = filter_input(INPUT_POST, 'server_group', FILTER_SANITIZE_SPECIAL_CHARS);
        if (isset($server_group) && !empty($server_group)){
            $sql = "INSERT INTO `server_group`(`server_group`) VALUES('$server_group');";
            $link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
            mysql_select_db(DB_NAME,$link);
            mysql_query($sql);
            mysql_close($link);
            $_SESSION['good_notice'] = "$server_group added! Now, I hope you aren't paying them too much...";
            header('location:'.BASE_PATH.'add_server_group');
        }
        else{
            $_SESSION['error_notice'] = "A required field was not filled in";
        }
    }
    else{
        header('location:'.BASE_PATH."add_user");
        exit();
    }
}
else{
    $_SESSION['error_notice'] = "You do not have permission to add users. This even thas been logged, and the admin has been notified.";
    header('location:'.BASE_PATH);
    exit();
}
?>
