<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active"){
  exit();
}
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$link);
$sql = "SELECT * FROM `service_list`";
$res = mysql_query($sql);
$table = "";
while ($row = mysql_fetch_assoc($res)){
  $s_id = $row['id'];
  $s_name = $row['service_name'];
  $s_cmd = $row['service_cmd'];
  $table .="                          <tr>
  <td>$s_id</td>
  <td>$s_name</td>
  <td>$s_cmd</td>
  <td><a class='btn btn-xs btn-info' href='".BASE_PATH."edit_service?id=$s_id'><i class='fa fa-pencil'></i> Edit </a></td>
  </tr>";
}
?>
<div class="col-sm-12 col-md-12 col-xs-12 main">
  <div class="x_panel">
    <div class="x_title">
      <h2>List Services</h2>
      <div class="clearfix"></div>
    </div>
    <div class="container">
      <div class="table-responsive">
        <table class="table table-striped jambo_table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Service Name</th>
              <th>Service CMD</th>
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
