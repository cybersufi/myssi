<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo site_url() ?>"><b>My</b>SSI</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Sign in :</p>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <b><i class="icon fa fa-ban"></i>Error!!</b> Danger alert preview.
    </div>
    <?php echo form_open('auth/login');?>
      <div class="form-group has-feedback">
        <?php echo form_input($identity);?>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <?php echo form_password($password);?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"'); ?> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <?php echo form_submit('submit', 'Sign In', array('class' => 'btn btn-primary btn-block btn-flat'));?>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <a href="#">I forgot my password</a><br>

  </div>
  <!-- /.login-box-body -->
