<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active") {
    exit();
}

$patch_list_sql = "SELECT count(*) as total_found FROM `patches` p LEFT JOIN servers s on s.server_name = p.server_name WHERE s.trusted = 1 and p.upgraded=0 and p.package_name !='';";
$patch_list_link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$patch_list_link);
$patch_list_res = mysql_query($patch_list_sql);
$patch_list_row = mysql_fetch_array($patch_list_res);
$patches_to_apply_count = $patch_list_row['total_found'];
mysql_close($patch_list_link);
$data = "";
foreach ($navbar_array as $key=>$val){
    $plugin2 = $key;
    $plugin2_glyph = $val["glyph"];
    $plugin_name = ucwords($plugin2);
            $data .= "<li><a><i class='$plugin2_glyph'></i> $plugin_name <span class='fa fa-chevron-down'></span></a>
              <ul class='nav child_menu' style='display: none'>";
    foreach ($val['page_and_glyph'] as $val2){
        $tmp_array = explode(",",$val2);
        $page_string = $tmp_array[0];
        $page_words = ucwords(str_replace("_"," ",$page_string));
        if (isset($tmp_array[1])){
            $page_glyph = "<span class=\"".$tmp_array[1]." text-primary\"></span>&nbsp;&nbsp;";
        }
        else{
            $page_glyph = "";
        }
        /*
         * Badge code:
         * <span class=\"badge\">42</span>
         * TODO: work the badge code in dynamically with patche count
         */
        if ($page_string == "patches"){
            $badge_code = "&nbsp;&nbsp;<span class=\"badge\">$patches_to_apply_count</span>";
        }
        else{
            $badge_code = "";
        }
        $data .= "                                <li><a href=\"".BASE_PATH."$page_string\">$page_glyph$page_words$badge_code</a>
                                    </li>";
    }
        $data .= "</ul>
                </li>";
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
  <link rel="shortcut icon" href="<?php echo BASE_PATH; ?>favicon.ico">
  <title>Gentallela Alela! | </title>
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


<body class="nav-md">

  <div class="container body">
    <div class="main_container">

      <div class="col-md-3 left_col">
        <div class="left_col scroll-view">

          <div class="navbar nav_title" style="border: 0;">
            <a href="<?php echo BASE_PATH; ?>" class="site_title"><span>Patch MD</span></a>
          </div>
          <div class="clearfix"></div>
          <!-- sidebar menu -->
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

            <div class="menu_section">
              <ul class="nav side-menu">
                <?php echo $data;?>
              </ul>
            </div>

          </div>
          <!-- /sidebar menu -->
        </div>
      </div>

      <!-- top navigation -->
      <div class="top_nav">

        <div class="nav_menu">
          <nav class="" role="navigation">
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  John Doe
                  <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu animated fadeInDown pull-right">
                  <li><a href="javascript:;">  Profile</a>
                  </li>
                  <li><a href="<?php echo BASE_PATH; ?>inc/logout.inc.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a>
                  </li>
                </ul>
              </li>
              <li style="width: 250px; margin-top: 10px;">
                <div class="input-group form-group pull-right top_search">
                <input type="text" class="form-control" placeholder="Search for..." onkeydown="if (event.keyCode == 13)
                NewURL(this.value);">
                </div>
              <li>
            </ul>
          </nav>
        </div>

      </div>
      <!-- /top navigation -->
      <!-- page content -->
      <div class="right_col" role="main">
        <div class="">
          <div class="page-title">
          </div>
          <div class="clearfix"></div>

          <div class="row">
