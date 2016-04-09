<?php
/*
 * Fail-safe check. Ensures that they go through the main page (and are authenticated to use this page
 */
if (!isset($index_check) || $index_check != "active"){
    exit();
}
?>
</div> <!--row-->
</div>
<!-- footer content -->
<footer>
  <div class="copyright-info">
    <p class="pull-right">Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
    </p>
  </div>
  <div class="clearfix"></div>
</footer>
<!-- /footer content -->

</div>
<!-- /page content -->
</div>

</div>

<div id="custom_notifications" class="custom-notifications dsp_none">
<ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
</ul>
<div class="clearfix"></div>
<div id="notif-group" class="tabbed_notifications"></div>
</div>

<script src="<?php echo BASE_PATH;?>js/bootstrap.min.js"></script>

<!-- bootstrap progress js -->
<script src="<?php echo BASE_PATH;?>js/progressbar/bootstrap-progressbar.min.js"></script>
<script src="<?php echo BASE_PATH;?>js/nicescroll/jquery.nicescroll.min.js"></script>
<!-- icheck -->
<script src="<?php echo BASE_PATH;?>js/icheck/icheck.min.js"></script>

<script src="<?php echo BASE_PATH;?>js/custom.js"></script>

<!-- pace -->
<script src="<?php echo BASE_PATH;?>js/pace/pace.min.js"></script>
<script type="text/javascript">
    function NewURL(val){
            base = '<?php echo BASE_PATH;?>search/';
            window.location.assign(base + val);
    }
</script>
</body>

</html>
