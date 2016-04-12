<?php

if(isset($_SESSION['error'])){
    $error = $_SESSION['error'];
    $error_html="<div class='bs-example'>
    <div class='alert alert-danger alert-error'>
        <a href='#' class='close' data-dismiss='alert'>&times;</a>
        <strong>Error!</strong> $error
    </div>
</div>";
    unset($_SESSION['error']);
    unset($error);
}
else{
    $error_html="";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <!-- Meta, title, CSS, favicons, etc. -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Patch Management Dashboard</title>

  <!-- Bootstrap core CSS -->
  <link href="<?php echo BASE_PATH;?>css/bootstrap.min.css" rel="stylesheet">

  <link href="<?php echo BASE_PATH;?>fonts/css/font-awesome.min.css" rel="stylesheet">
  <link href="<?php echo BASE_PATH;?>css/animate.min.css" rel="stylesheet">
  <!-- Custom styling plus plugins -->
  <link href="<?php echo BASE_PATH;?>css/custom.css" rel="stylesheet">
  <link href="<?php echo BASE_PATH;?>css/icheck/flat/green.css" rel="stylesheet">

  <script src="<?php echo BASE_PATH;?>js/jquery.min.js"></script>

  <!--[if lt IE 9]>
        <script src="../assets/js/ie8-responsive-file-warning.js"></script>
        <![endif]-->

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->

</head>

<body style="background:#F7F7F7;">

  <div class="">
    <div id="wrapper">
      <?php echo $error_html;?>
      <div id="login" class="animate form">
        <section class="login_content">
          <form method="POST" action="<?php echo BASE_PATH;?>inc/p_login.inc.php">
            <h1>Patch Dashboard</h1>
            <div>
              <input type="text" name="username" class="form-control" placeholder="Username" required="" />
            </div>
            <div>
              <input type="password" name="pass" class="form-control" placeholder="Password" required="" />
            </div>
            <div>
              <button class="btn btn-success submit" type="submit">Sign in</button>
            </div>
            <div class="clearfix"></div>
            <div class="separator">
              <div class="clearfix"></div>
            </div>
          </form>
          <!-- form -->
        </section>
        <!-- content -->
      </div>
    </div>
  </div>

</body>

</html>
