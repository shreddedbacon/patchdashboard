<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
?>
<div class="col-sm-12 col-md-5 col-xs-12 main">
  <div class="x_panel" style="height:600px;">
    <div class="x_title">
      <h2>Add Server Group</h2>
      <div class="clearfix"></div>
    </div>
                <form id ="addServerGroup" method="POST" action="<?php echo BASE_PATH;?>plugins/admin/p_add_server_group.inc.php"
		        data-bv-message="This value is not valid"
      			data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
			data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
			data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
                    <div class="form-group col-sm-12"><label class="col-sm-6 control-label">Server Group</label><div class="col-sm-6"><input type="text" name="server_group" class="form-control" placeholder="Server group" required autofocus ></div></div>
                    <div class="form-group col-sm-12"><div class="col-sm-6 col-sm-offset-3"><button class="btn btn-md btn-success btn-block" type="submit">Add Server Group</button></div></div>
                </form>
            </div>
        </div>
