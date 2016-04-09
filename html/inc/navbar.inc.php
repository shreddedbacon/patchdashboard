<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
?>
<!-- PNotify -->
<script type="text/javascript" src="<?php echo BASE_PATH;?>js/notify/pnotify.core.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH;?>js/notify/pnotify.buttons.js"></script>
<script type="text/javascript" src="<?php echo BASE_PATH;?>js/notify/pnotify.nonblock.js"></script>
<?php
if (!isset($_SESSION['error_notice'])){
    $error_html = "";
}
else{
    $error_message = $_SESSION['error_notice'];
    $error_html = "<script type='text/javascript'>
            var permanotice, tooltip, _alert;
            $(function () {
              new PNotify({
              title: 'Error!',
              text: '".$error_message."',
              type: 'error'
              });
            });
        </script>";
    unset($_SESSION['error_notice']);
    unset($error_message);
}

if (!isset($_SESSION['good_notice'])){
    $good_html = "";
}
else{
    $good_message = $_SESSION['good_notice'];
    $good_html = "<script type='text/javascript'>
        var permanotice, tooltip, _alert;
        $(function () {
          new PNotify({
          title: 'Notice:',
          text: '".$good_message."',
          type: 'success'
          });
        });
    </script>";
    unset($_SESSION['good_notice']);
    unset($good_message);
}

if (!isset($_SESSION['warning_notice'])){
    $warning_html = "";
}
else{
    $warning_message = $_SESSION['warning_notice'];
    $warning_html = "<script type='text/javascript'>
        var permanotice, tooltip, _alert;
        $(function () {
          new PNotify({
          title: 'Warning:',
          text: '".$warning_message."'
          });
        });
    </script>";
    unset($_SESSION['warning_notice']);
    unset($warning_message);
}
$all_messages_to_send = "${warning_html}${good_html}${error_html}";
?>

<?php echo $all_messages_to_send;unset($all_messages_to_send);?>
