<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active") {
    exit();
}
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!isset($id) || empty($id) || !is_numeric($id)) {
    $_SESSION['error_message'] = "Invalid user ID";
    ?>
    <div class="col-sm-9 col-md-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h3 class="text-center login-title">INVALID SERVER</h3>
        <div class="account-wall">
            Please try again. <a href="javascript:history.back()">Back</a>
        </div>
    </div>
    <?php
} else {
    $link_edit_user = mysql_connect(DB_HOST, DB_USER, DB_PASS);
    mysql_select_db(DB_NAME, $link_edit_user);

    $sql_edit_server = "SELECT * FROM `server_group` WHERE id=$id limit 1;";
    $res_edit_server = mysql_query($sql_edit_server);
    $row = mysql_fetch_array($res_edit_server);
    $id = $row['id'];
    $server_group = $row['server_group'];
    ?>
    <div class="col-sm-9 col-md-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h3 class="text-center login-title">Edit Server (<?php echo $server_group; ?>)</h3>
        <div class="account-wall">
            <form id ="editUser" method="POST" action="<?php echo BASE_PATH; ?>plugins/admin/p_edit_server_group.inc.php"><input type="hidden" name="id" value="<?php print $id; ?>" />
                <div class="form-group"><label class="col-sm-5 control-label">Server Group</label><div class="col-sm-5"><input value="<?php echo $server_group; ?>" type="text" name="server_group" class="form-control" placeholder="Server Group" ></div></div>
                <div class="form-group"><label class="col-sm-5 control-label"></label><div class="col-sm-5"><button class="btn btn-lg btn-primary btn-block" type="submit">Edit Server Group</button></div></div>
            </form>
        </div>
    </div>
    <?php
}
