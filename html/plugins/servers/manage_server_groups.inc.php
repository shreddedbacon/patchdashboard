<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active"){
  exit();
}
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$link);
$sql = "SELECT * FROM server_group;";
$res = mysql_query($sql);
$table = "";
while ($row = mysql_fetch_assoc($res)){
  $id = $row['id'];
  $server_group = $row['server_group'];

  $sql2 = "SELECT count(*) as total_found FROM servers WHERE server_group='".$id."';";
  $res2 = mysql_query($sql2);
  $server_group_list = mysql_fetch_array($res2);
  $server_group_count = $server_group_list['total_found'];

  $delete="";
  if ($server_group_count == 0) {
    $delete=" <a class='btn btn-xs btn-danger' href='".BASE_PATH."plugins/servers/delete_server_group.inc.php?id=$id'><i class='fa fa-trash-o'></i> Delete </a>";
  }
  $table .="                          <tr>
  <td>$server_group</td>
  <td>$server_group_count</td>
  <td><a class='btn btn-xs btn-info' href='".BASE_PATH."edit_server_group?id=$id'><i class='fa fa-pencil'></i> Edit </a>$delete</td>
  </tr>
  ";
}
?>
<div class="col-sm-12 col-md-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>Server Groups</h2>
      <div class="clearfix"></div>
    </div>
    <div class="container">
      <div class="table-responsive">
        <table class="table table-striped jambo_table">
          <thead>
            <tr>
              <th>Server Group</th>
              <th>Servers in Group</th>
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
