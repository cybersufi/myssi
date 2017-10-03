<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User Profile
      </h1>
    </section>
    <?php if (! empty($error) || ! empty($messages)) { ?>
    <div class="pad margin no-print">
      <?php if (!empty($error)) { ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo $error; ?>
      </div>
      <?php 
        }

        if (!empty($message)) {
      ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo $message; ?>
      </div>
      <?php } ?>
    </div>
    <?php } ?>

    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url($photo_dir .'/'. $userphoto); ?>" alt="User profile picture">

              <h3 class="profile-username text-center"><?php echo $username ?></h3>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item" >
                  <strong>User Groups</strong>
                  <p class="pull-right">
                    <?php
                      foreach ($usergroups as $gr) {
                        echo '<span class="label bg-'.$gr->color.'">'.ucwords($gr->name).'</span>';
                      }
                    ?>
                  </p>
                </li>
                <li class="list-group-item">
                  <b>Member Since</b> <a class="pull-right"><?php echo $membersince; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Last Login</b> <a class="pull-right"><?php echo $lastlogin; ?></a>
                </li>
                <li class="list-group-item">
                  <b>From</b> <a class="pull-right"><?php echo $lastloginfrom; ?></a>
                </li>
              </ul>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li <?php if ($active == 'userprofile') echo 'class="active"'; ?>><a href="#user-global" data-toggle="tab">General</a></li>
              <li <?php if ($active == 'userpass') echo 'class="active"'; ?>><a href="#user-config" data-toggle="tab">Password</a></li>
            </ul>
            <div class="tab-content">
              <div class="<?php if ($active == 'userprofile') echo 'active';?> tab-pane" id="user-global">
                <?php echo form_open_multipart('users/myprofile', 'class="form-group form-horizontal has-feedback" id="myprofileform"'); ?>
                  <div class="form-group has-feedback">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                      <?php echo form_input($name);?>
                      <span class="form-control-feedback"></span>
                    </div>
                  </div>
                  <div class="form-group has-feedback">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <?php echo form_input($email);?>
                      <span class="form-control-feedback"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Phone</label>
                    <div class="col-sm-10">
                      <?php echo form_input($phone);?>
                      <span class="form-control-feedback"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Photo</label>
                    <div class="col-sm-10">
                      <?php echo form_upload($photo);?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <?php 
                        echo form_hidden('action', 'update_info');
                        echo form_submit('submit', 'Save', array('class' => 'btn btn-danger btn-flat'));
                        echo form_submit('reset', 'Cancel', array('class' => 'btn btn-warning btn-flat'));
                      ?>
                    </div>
                  </div>
                <?php echo form_close(); ?>
              </div>
              <!-- /.tab-pane -->
              <div class="<?php if ($active == 'userpass') echo 'active';?> tab-pane" id="user-config">
                <?php echo form_open('users/myprofile', 'class="form-group form-horizontal has-feedback" id="mypassform"'); ?>
                  <div class="form-group has-feedback">
                    <label for="inputoldpassword" class="col-sm-3 control-label">Old Password</label>
                    <div class="col-sm-9">
                      <?php echo form_input($oldpassword);?>
                      <span class="form-control-feedback"></span>
                    </div>
                  </div>
                  <div class="form-group has-feedback">
                    <label for="inputnewpassword" class="col-sm-3 control-label">New Password</label>
                    <div class="col-sm-9">
                      <?php echo form_input($newpassword);?>
                      <span class="form-control-feedback"></span>
                    </div>
                  </div>
                  <div class="form-group has-feedback">
                    <label for="inputconfpassword" class="col-sm-3 control-label">Re-New Password</label>
                    <div class="col-sm-9">
                      <?php echo form_input($confpassword);?>
                      <span class="form-control-feedback"></span>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <?php 
                        echo form_hidden('action', 'change_pass');
                        echo form_submit('submit', 'Save', array('class' => 'btn btn-danger btn-flat'));
                        echo form_submit('reset', 'Cancel', array('class' => 'btn btn-warning btn-flat'));
                      ?>
                    </div>
                  </div>
                <?php echo form_close(); ?>
              </div>
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->