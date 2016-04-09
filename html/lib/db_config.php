<?php
define('DB_HOST',"localhost");
define('DB_USER',"DB_USER");
define('DB_PASS',"DB_PASS");
define('DB_NAME',"DB_NAME");
define('BASE_PATH',"/");
define('PW_SALT','W[62~L41|]CU15b');
/*
 * SET OFFLINE to TRUE if you want to disable the site.  All functionality will cease until you re-enable the site by setting OFFLINE back to FALSE
 */
define('OFFLINE','FALSE');

//use database company name instead of what was previously defined here
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$link);
$sql = "SELECT * FROM company;";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
define('YOUR_COMPANY',$row['display_name']);
