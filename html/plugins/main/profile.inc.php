<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
$username = $_SESSION['user_id'];
$link_edit_user = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$link_edit_user);

$sql_edit_user = "SELECT * FROM `users` WHERE `user_id`='$username' limit 1;";
$res_edit_user = mysql_query($sql_edit_user) or die("ERROR<br /><br/><br/>".mysql_error());
$row_edit_user = mysql_fetch_array($res_edit_user);
$email_address = $row_edit_user['email'];
$admin = $row_edit_user['admin'];
$display_name = $row_edit_user['display_name'];
$seen = $row_edit_user['last_seen'];
if ($seen == "0000-00-00 00:00:00"){
    $last_seen = "Never";
}
else{
    $last_seen = $seen;
}
$alerts = $row_edit_user['receive_alerts'];

if ($admin == 1){
    $admin_checked = "checked";
}
else{
    $admin_checked = "";
}
if ($alerts == 1){
    $alerts_checked = "checked";
}
else{
    $alerts_checked = "";
}
?>
        <div class="col-sm-9 col-md-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center login-title">Edit/View Your Profile</h1>
            <div class="account-wall">
                <form id ="editUser" method="POST" action="<?php echo BASE_PATH;?>plugins/main/p_profile.inc.php"><input type="hidden" name="id" value="<?php print $id;?>" />
                    <div class="form-group"><label class="col-sm-5 control-label">Username</label><div class="col-sm-5"><input type="text" name="username" value="<?php echo $username;?>" class="form-control" readonly /></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label">Display Name</label><div class="col-sm-5"><input value="<?php echo $display_name;?>" type="text" name="display_name" class="form-control" placeholder="Nickname/Real Name" required autofocus ></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label">Password (Leave blank for no change)</label><div class="col-sm-5"><input type="password" name="password" class="form-control" placeholder="Password" /></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label">Confirm Password (Leave blank for no change)</label><div class="col-sm-5"><input type="password" name="confirmPassword" class="form-control" placeholder="Retype Password" /></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label">E-Mail Address</label><div class="col-sm-5"><input value="<?php echo $email_address;?>" type="text" name="email" class="form-control" placeholder="E-mail Address" required ></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label">Receive Alerts?</label><div class="col-sm-5"><input type="checkbox" name="alerts" class="form-control" <?php echo $alerts_checked;?>></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label"></label><div class="col-sm-5"><button class="btn btn-lg btn-primary btn-block" type="submit">Edit Profile</button></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label"></label><div class="col-sm-5"><label class="checkbox pull-left"></label></div></div>
                </form>
            </div>
        </div>
