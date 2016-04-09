<?php
$link = mysql_connect(DB_HOST,DB_USER,DB_PASS);
mysql_select_db(DB_NAME,$link);
$sql = "SELECT * FROM company;";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
define('YOUR_COMPANY',$row['display_name']);
mysql_close($link);
