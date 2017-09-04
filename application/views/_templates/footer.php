<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Main Footer -->
  <?php if ($is_main_sidebar) { ?>
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?></p>
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2017 <a href="<?php echo base_url(); ?>"></a>.</strong> All rights reserved.
  </footer>
  <?php } ?>
</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url($plugins_dir . '/jQuery/jquery-2.2.3.min.js'); ?>"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url($frameworks_dir . '/bootstrap/js/bootstrap.min.js'); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url($frameworks_dir . '/adminlte/js/AdminLTE.min.js'); ?>"></script>

<script src="<?php echo base_url($plugins_dir . '/slimscroll/slimscroll.min.js'); ?>"></script>
<script src="<?php echo base_url($plugins_dir . '/fastclick/fastclick.min.js'); ?>"></script>
<!-- page related js -->
<?php
  if (isset($pluginsjs) && (count($pluginsjs) > 0)) {
    foreach ($pluginsjs as $value) {
      echo '<script src="'.base_url($plugins_dir . $value).'"></script>';
    }
  }

  if (isset($pagejs) && (strlen(trim($pagejs)) > 0))
  {
    echo '<script src="'.base_url($app_dir . $pagejs).'"></script>';
  }
?>
</body>
</html>