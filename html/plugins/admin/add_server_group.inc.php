<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
?>
        <div class="col-sm-9 col-md-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h3 class="text-center login-title">Add Server Group</h1>
            <div class="account-wall">
                <form id ="addServerGroup" method="POST" action="<?php echo BASE_PATH;?>plugins/admin/p_add_server_group.inc.php"
		        data-bv-message="This value is not valid"
      			data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
			data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
			data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
                    <div class="form-group"><label class="col-sm-5 control-label">Server Group</label><div class="col-sm-5"><input type="text" name="server_group" class="form-control" placeholder="Username" required autofocus ></div></div>
                    <div class="form-group"><label class="col-sm-5 control-label"></label><div class="col-sm-5"><button class="btn btn-lg btn-primary btn-block" type="submit">Add Server Group</button></div></div>
                </form>
            </div>
        </div>
