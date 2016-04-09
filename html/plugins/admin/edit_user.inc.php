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
        <h3 class="text-center login-title">INVALID USER</h3>
            <div class="account-wall">
                Please try again. <a href="javascript:history.back()">Back</a>
            </div>
    </div>
<?php
} else {
    $link_edit_user = mysql_connect(DB_HOST, DB_USER, DB_PASS);
    mysql_select_db(DB_NAME, $link_edit_user);

    $sql_edit_user = "SELECT * FROM `users` WHERE id=$id limit 1;";
    $res_edit_user = mysql_query($sql_edit_user) or die("ERROR<br /><br/><br/>" . mysql_error());

    $row_edit_user = mysql_fetch_array($res_edit_user);
    $username = $row_edit_user['user_id'];
    $email_address = $row_edit_user['email'];
    $admin = $row_edit_user['admin'];
    $display_name = $row_edit_user['display_name'];
    $seen = $row_edit_user['last_seen'];
    if ($seen == "0000-00-00 00:00:00") {
        $last_seen = "Never";
    } else {
        $last_seen = $seen;
    }
    $alerts = $row_edit_user['receive_alerts'];

    if ($admin == 1) {
        $admin_checked = "checked";
    } else {
        $admin_checked = "";
    }
    if ($alerts == 1) {
        $alerts_checked = "checked";
    } else {
        $alerts_checked = "";
    }
    ?>
    <div class="col-sm-12 col-md-5 col-xs-12 main">
      <div class="x_panel" style="height:600px;">
        <div class="x_title">
          <h2>Edit User (<?php echo $username; ?>)</h2>
          <div class="clearfix"></div>
        </div>
                <form id ="editUser" method="POST" action="<?php echo BASE_PATH; ?>plugins/admin/p_edit_user.inc.php"><input type="hidden" name="id" value="<?php print $id; ?>" />
                    <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Last Login</label><div class="col-sm-6"><input type="text" value="<?php echo $last_seen; ?>" class="form-control" readonly /></div></div>
                    <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Username</label><div class="col-sm-6"><input type="text" name="username" value="<?php echo $username; ?>" class="form-control" readonly /></div></div>
                    <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Display Name</label><div class="col-sm-6"><input value="<?php echo $display_name; ?>" type="text" name="display_name" class="form-control" placeholder="Nickname/Real Name" required autofocus ></div></div>
                    <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Password (Leave blank for no change)</label><div class="col-sm-6"><input type="password" name="password" class="form-control" placeholder="Password" /></div></div>
                    <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Confirm Password (Leave blank for no change)</label><div class="col-sm-6"><input type="password" name="confirmPassword" class="form-control" placeholder="Retype Password" /></div></div>
                    <div class="form-group col-sm-12"><label class="col-sm-6 control-label">E-Mail Address</label><div class="col-sm-6"><input value="<?php echo $email_address; ?>" type="text" name="email" class="form-control" placeholder="E-mail Address" required ></div></div>
                    <div class="form-group col-sm-12">
                      <label class="col-sm-7 control-label">Are they an Admin?</label>
                        <input type="checkbox" class="checkbox flat" name="is_admin" <?php echo $admin_checked; ?>>
                    </div>
                    <div class="form-group col-sm-12">
                      <label class="col-sm-7 control-label">Receive Alerts?</label>
                        <input type="checkbox" name="alerts" class="checkbox flat" <?php echo $alerts_checked; ?>>
                    </div>
                    <div class="form-group col-sm-12"><div class="col-sm-6 col-sm-offset-3"><button class="btn btn-md btn-success btn-block" type="submit">Edit User</button></div></div>
                </form>
            </div>
    </div>
    <?php
}
