<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo site_url() ?>"><b>My</b>SSI</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Register an Admin:</p>
    <?php if (isset($error)) { ?>
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <!--<b><i class="icon fa fa-ban"><?php echo "Error!!</b>".$error."."; ?>.-->
      <?php echo $message; ?>
    </div>
    <?php } ?>
    <?php if (isset($message)) { ?>
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <!--<b><i class="icon fa fa-ban"><?php echo "Error!!</b>".$message."."; ?>.-->
      <?php echo $message; ?>
    </div>
    <?php } ?>
    <?php echo form_open('install/register');?>
      <div class="form-group has-feedback">
        <?php echo form_input($name);?>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <?php echo form_input($email);?>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <?php echo form_password($password);?>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-4">
          <?php echo form_submit('submit', 'Submit', array('class' => 'btn btn-primary btn-block btn-flat'));?>
        </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
  <!-- /.login-box-body -->
