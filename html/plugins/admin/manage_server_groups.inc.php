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

    $sql2 = "SELECT count(*) as total_found FROM servers WHERE server_group='".$server_group."';";
    $res2 = mysql_query($sql2);
    $server_group_list = mysql_fetch_array($res2);
    $server_group_count = $server_group_list['total_found'];
    $table .="                          <tr>
					<td>$server_group</td>
          <td>$server_group_count</td>
          <td><a href='".BASE_PATH."edit_server_group?id=$id'>Edit</a> | $active_action | <a href='".BASE_PATH."plugins/admin/delete_server_group.inc.php?id=$id'>Delete</a></td>
          </tr>
";
}
?>

<div class="col-sm-9 col-md-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Server Groups</h1>
        <div class="container">
          <div class="table-responsive">
            <table class="table table-striped">
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