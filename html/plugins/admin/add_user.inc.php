<?php
/*
* Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
*/
if (!isset($index_check) || $index_check != "active"){
  exit();
}
?>
<div class="col-sm-12 col-md-5 col-xs-12 main">
  <div class="x_panel">
    <div class="x_title">
      <h2>Add User</h2>
      <div class="clearfix"></div>
    </div>
    <form id ="addUser" method="POST" action="<?php echo BASE_PATH;?>plugins/admin/p_add_user.inc.php"
      data-bv-message="This value is not valid"
      data-bv-feedbackicons-valid="glyphicon glyphicon-ok"
      data-bv-feedbackicons-invalid="glyphicon glyphicon-remove"
      data-bv-feedbackicons-validating="glyphicon glyphicon-refresh">
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Username</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="text" name="username" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="Username" required autofocus >
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Display Name</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="text" name="display_name" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="Nickname/Real Name" required autofocus >
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Password</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="password" name="password" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="Password" required	data-bv-notempty="true" data-bv-notempty-message="The password is required and cannot be empty"
          data-bv-identical="true" data-bv-identical-field="confirmPassword" data-bv-identical-message="The password and its confirm are not the same"
          data-bv-different="true" data-bv-different-field="username" data-bv-different-message="The password cannot be the same as username" />
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Confirm Password</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="password" name="confirmPassword" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="Retype Password" required
          data-bv-notempty="true" data-bv-notempty-message="The confirm password is required and cannot be empty"
          data-bv-identical="true" data-bv-identical-field="password" data-bv-identical-message="The password and its confirm are not the same"
          data-bv-different="true" data-bv-different-field="username" data-bv-different-message="The password cannot be the same as username" />
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">E-Mail Address</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="text" name="email" class="form-control col-lg-12 col-md-7 col-xs-12" placeholder="E-mail Address" required >
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Are they an Admin?</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="checkbox" class="checkbox flat col-lg-12 col-md-7 col-xs-12" name="is_admin">
        </div>
      </div>
      <div class="form-group">
        <label class="control-label col-lg-12 col-md-4 col-sm-4 col-xs-12">Receive Alerts?</label>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
          <input type="checkbox" name="alerts" class="checkbox flat col-lg-12 col-md-7 col-xs-12">
        </div>
      </div>
      <div class="form-group">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-6 col-sm-offset-3 col-xs-6 col-xs-offset-3">
          <button class="btn btn-md btn-success btn-block" type="submit">Add User</button>
        </div>
      </div>
    </form>
  </div>
</div>
