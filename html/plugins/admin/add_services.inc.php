<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active") {
  exit();
}
  $link_edit_service = mysql_connect(DB_HOST, DB_USER, DB_PASS);
  mysql_select_db(DB_NAME, $link_edit_service);

  $service_array_sql = "SELECT server_id FROM `services` WHERE service_id=$id;";
  $service_array_res = mysql_query($service_array_sql);
  while ($service_row = mysql_fetch_assoc($service_array_res)) {
	$services[] = $service_row['server_id'];
  }

  $servers_applied_sql = "SELECT id,server_name FROM `servers`";
  $servers_applied_res = mysql_query($servers_applied_sql);
  $server_list="";
  while ($servers_row = mysql_fetch_assoc($servers_applied_res)) {
  	$server_id = $servers_row['id'];
  	$server_name = $servers_row['server_name'];
  	$server_list .="
  	<tr>
  	<td><input type='checkbox' class='checkbox flat' name='server_id[]' id='check_box' value='$server_id'></td>
  	<td>$server_name</td>
  	</tr>";
  }
  ?>
  <form id ="editServices" method="POST" action="<?php echo BASE_PATH; ?>plugins/admin/p_add_services.inc.php">
  <div class="col-sm-12 col-md-5 col-xs-12 main">
    <div class="x_panel">
      <div class="x_title">
        <h2>Add Service</h2>
        <div class="clearfix"></div>
      </div>
        <div class="form-group">
          <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Service Name</label>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <input type="text" name="service_name" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="Service Name" required autofocus >
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Command</label>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <input type="text" name="service_cmd" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="service x restart" required >
          </div>
        </div>
        <div class="form-group">
          <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
            <button class="btn btn-md btn-success btn-block" type="submit">Add Service</button>
          </div>
        </div>
    </div>
  </div>


  <div class="col-sm-12 col-md-5 col-xs-12 main">
    <div class="x_panel">
      <div class="x_title">
        <h2>Applied Servers</h2>
        <div class="clearfix"></div>
      </div>
    <div class="container">
      <div class="table-responsive">
        <table class="table table-striped responsive-utilities jambo_table bulk_action">
          <thead>
            <tr>
              <th><input type="checkbox" id="check-all" class="flat"></th>
              <th>Server Name</th>
            </tr>
          </thead>
          <tbody>
            <?php echo $server_list;?>
          </tbody>
        </table>
      </div>
    </div>
    </div>
  </div>

  </form>
