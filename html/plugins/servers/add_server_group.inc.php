<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active"){
  exit();
}
?>
<div class="col-md-4 col-md-6 col-sm-12 col-xs-12">
  <div class="x_panel">
    <div class="x_title">
      <h2>Add Server Group</h2>
      <div class="clearfix"></div>
    </div>
    <form id ="addServerGroup" method="POST" action="<?php echo BASE_PATH;?>plugins/servers/p_add_server_group.inc.php">
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Server Group</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="text" name="server_group" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="Server group" required autofocus >
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
          <button class="btn btn-md btn-success btn-block" type="submit">Add Server Group</button>
        </div>
      </div>
    </form>
  </div>
</div>
