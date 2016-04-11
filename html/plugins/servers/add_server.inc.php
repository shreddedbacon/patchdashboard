<?php
if (!empty($_SERVER['HTTPS'])){
    $protocol = 'https://';
}
else{
    $protocol = 'http://';
}
if ($protocol == 'https://'){
    $advice = "<h3><p style='color:green'> Because you're using HTTPS, we advise using the pull method.</h3></p>";
    $curl_cmd = "curl -s -k";
}
else{
    $advice = "<h3><p style='color:red'> Because you're not using HTTPS, we <strong>HIGHLY</strong> advise against the pull method.</h3></p>";
    $curl_cmd = "curl";
}
include '../lib/db_config.php';
$SERVER_URI = $protocol.$_SERVER['HTTP_HOST'].BASE_PATH;
?>
<div class="col-sm-12 col-md-5 col-xs-12 main">
  <div class="x_panel">
    <div class="x_title">
      <h2>Adding a server</h2>
      <div class="clearfix"></div>
    </div>
          <div class="error-template">
                <h2>Howto:</h2>
                <div class="error-details">
                    <?php echo $advice;?>
                    <p>The Pull Method, if you are running this via HTTPS, or you implicitly trust all traffic on your network (from each guest machine/node):</p>
                    <pre>sudo -i
<?php echo $curl_cmd;?> <?php echo "${SERVER_URI}client/client_installer.php";?> | bash</pre>
                </div>
            </div>
        </div>
    </div>
