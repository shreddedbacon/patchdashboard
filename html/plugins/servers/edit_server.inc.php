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

    $distro_map_sql = "SELECT d.distro_name as distro_name,dv.version_num as version_num, dv.id as version_id,d.id as distro_id FROM distro_version dv LEFT JOIN distro d on d.id=dv.distro_id;";
    $distro_map_res = mysql_query($distro_map_sql);
    $select_html = "<select class='form-control custom' name='distro_ver_id'>";

    $server_group_map_sql = "SELECT * FROM server_group;";
    $server_group_map_res = mysql_query($server_group_map_sql);

    $select_html_sg = "<select class='form-control custom' name='server_group'>";

    $sql_edit_server = "SELECT * FROM `servers` WHERE id=$id limit 1;";
    $res_edit_server = mysql_query($sql_edit_server);
    $row = mysql_fetch_array($res_edit_server);
    $id = $row['id'];
    $server_name = $row['server_name'];
    $server_alias = $row['server_alias'];

    $server_group = $row['server_group'];

    $distro_id_main = $row['distro_id'];
    $server_ip = $row['server_ip'];
    $last_checkin = $row['last_checked'];
    $seen = $row['last_seen'];
    $trusted = $row['trusted'];
    $check_interval = $row['check_interval'];
    $distro_version_main = $row['distro_version'];
    while ($distro_map_row = mysql_fetch_assoc($distro_map_res)) {
        $distro_id = $distro_map_row['distro_id'];
        $distro_ver_id = $distro_map_row['version_id'];
        $distro_ver_name = str_replace("_", " ", $distro_map_row['distro_name'] . " " . $distro_map_row['version_num']);
        if ("${distro_id}-${distro_ver_id}" == "${distro_id_main}-${distro_version_main}") {
            $select_html .= "\t\t\t\t\t<option value='${distro_id}-${distro_ver_id}' selected='selected'>$distro_ver_name</option>\n";
        } else {
            $select_html .= "\t\t\t\t\t<option value='${distro_id}-${distro_ver_id}'>$distro_ver_name</option>\n";
        }
        $distro_array[$distro_map_row['distro_id']][$distro_map_row['version_id']] = $distro_ver_name;
    }

    while ($server_group_map_row = mysql_fetch_assoc($server_group_map_res)) {
        $server_group_id = $server_group_map_row['id'];
        $server_group_name = $server_group_map_row['server_group'];
        if ("$server_group_id" == "$server_group") {
            $select_html_sg .= "\t\t\t\t\t<option value='$server_group_id' selected='selected'>$server_group_name</option>\n";
        } else {
            $select_html_sg .= "\t\t\t\t\t<option value='$server_group_id'>$server_group_name</option>\n";
        }
    }


    if ($trusted == 1) {
        $trusted_checked = "checked";
    } else {
        $trusted_checked = "";
    }
    $select_html .= "\t\t\t\t</select>";
    $select_html_sg .= "\t\t\t\t</select>";
    $distro_name = $distro_array[$distro_id_main][$distro_version_main];
    $client_key = $row['client_key'];
    if ($seen == "0000-00-00 00:00:00") {
        $last_seen = "Never";
    } else {
        $last_seen = $seen;
    }
    if ($last_checkin == "2001-01-01 00:00:00"){
        $last_check = "Never";
    }
    else{
        $last_check = $last_checkin;
    }
    ?>
    <div class="col-sm-12 col-md-5 col-xs-12 main">
      <div class="x_panel">
        <div class="x_title">
          <h2>Edit Server (<?php echo $server_name; ?>)</h2>
          <div class="clearfix"></div>
        </div>
            <form id ="editUser" method="POST" action="<?php echo BASE_PATH; ?>plugins/servers/p_edit_server.inc.php"><input type="hidden" name="id" value="<?php print $id; ?>" />
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Last Seen</label><div class="col-sm-6"><input type="text" value="<?php echo $last_seen; ?>" class="form-control" readonly /></div></div>
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Last Checked for updates</label><div class="col-sm-6"><input type="text" name="last_checkin" value="<?php echo $last_check; ?>" class="form-control" readonly /></div></div>
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Server Name</label><div class="col-sm-6"><input value="<?php echo $server_name; ?>" type="text" name="server_name" class="form-control" placeholder="Server Name" required autofocus ></div></div>
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Server Alias</label><div class="col-sm-6"><input value="<?php echo $server_alias; ?>" type="text" name="server_alias" class="form-control" placeholder="Server Alias" required autofocus ></div></div>
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Server Group</label><div class="col-sm-6"><?php echo $select_html_sg;?></div></div>
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">IP Address</label><div class="col-sm-6"><input type="text" name="server_ip" value="<?php echo $server_ip; ?>" class="form-control" placeholder="IP Address" /></div></div>
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Check Interval (Hours)</label><div class="col-sm-6"><input value="<?php echo $check_interval; ?>" type="text" name="check_interval" class="form-control" placeholder="2" required autofocus ></div></div>
                <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Distro</label><div class="col-sm-6"><?php echo $select_html;?></div></div>
                <div class="form-group col-sm-12">
                  <label class="col-sm-7 control-label">Trusted?</label>
                    <input type="checkbox" name="trusted" class="checkbox flat" <?php echo $trusted_checked; ?>>
                </div>
                <div class="form-group col-sm-12"><div class="col-sm-6 col-sm-offset-3"><button class="btn btn-md btn-success btn-block" type="submit">Edit Server</button></div></div>
            </form>
        </div>
    </div>
    <?php
}
