<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}

if (!isset($_SESSION['error_notice'])){
    $error_html = "";
}
else{
    $error_message = $_SESSION['error_notice'];
    $error_html = "<div class='bs-example'><div class='alert alert-error'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        <strong>Error! </strong> $error_message
    </div></div>";
    unset($_SESSION['error_notice']);
    unset($error_message);
}

if (!isset($_SESSION['good_notice'])){
    $good_html = "";
}
else{
    $good_message = $_SESSION['good_notice'];
    $good_html = "<div class='container'><div class='row'><div class='span4'><div class='alert alert-success'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        <strong>Notice: </strong> $good_message
    </div></div></div></div>";
    unset($_SESSION['good_notice']);
    unset($good_message);
}

if (!isset($_SESSION['warning_notice'])){
    $warning_html = "";
}
else{
    $warning_message = $_SESSION['warning_notice'];
    $warning_html = "<div class='bs-example'><div class='alert alert-warning'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        <strong>Warning: </strong> $warning_message
    </div></div>";
    unset($_SESSION['warning_notice']);
    unset($warning_message);
}
$all_messages_to_send = "${warning_html}${good_html}${error_html}";
?>

<?php echo $all_messages_to_send;unset($all_messages_to_send);?>
