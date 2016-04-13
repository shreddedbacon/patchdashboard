<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active"){
  exit();
}
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$link);
$sql = "SELECT * FROM users;";
$res = mysql_query($sql);
$table = "";
while ($row = mysql_fetch_assoc($res)){
  $id = $row['id'];
  $username = $row['user_id'];
  $active = $row['active'];
  $email = $row['email'];
  if ($row['admin'] == 1){
    $group = "Admin";
  }
  else{
    $group = "Standard User";
  }
  $last_seen = $row['last_seen'];
  if ($last_seen == "0000-00-00 00:00:00"){
    $last_seen = "Never";
  }
  if ($row['receive_alerts'] == 1){
    $alerts = "<i class='fa fa-check text-success'></i>";
  }
  else{
    $alerts = "<i class='fa fa-times text-danger'></i>";
  }
  if ($active == 1){
    $active_action = "<a class='btn btn-xs btn-warning' href='".BASE_PATH."plugins/admin/deactivate_user.inc.php?id=$id'><i class='fa fa-ban'></i> Deactivate </a>";
  }
  else{
    $active_action = "<a class='btn btn-xs btn-success' href='".BASE_PATH."plugins/admin/activate_user.inc.php?id=$id'><i class='fa fa-check'></i> Reactivate </a>";
  }
  $table .="                          <tr>
  <td>$username</td>
  <td>$email</td>
  <td>$group</td>
  <td>$last_seen</td>
  <td>$alerts</td>
  <td><a class='btn btn-xs btn-info' href='".BASE_PATH."edit_user?id=$id'><i class='fa fa-pencil'></i> Edit </a> | $active_action | <a class='btn btn-xs btn-danger' href='".BASE_PATH."plugins/admin/delete_user.inc.php?id=$id'><i class='fa fa-trash-o'></i> Delete </a></td>
  </tr>
  ";
}
?>
<div class="col-sm-12 col-md-12 col-xs-12 main">
  <div class="x_panel">
    <div class="x_title">
      <h2>List Users</h2>
      <div class="clearfix"></div>
    </div>
    <div class="container">
      <div class="table-responsive">
        <table class="table table-striped jambo_table">
          <thead>
            <tr>
              <th>Username</th>
              <th>E-mail</th>
              <th>Group</th>
              <th>Last Login</th>
              <th>Alerts?</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php echo $table;?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
