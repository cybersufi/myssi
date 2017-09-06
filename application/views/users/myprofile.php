<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User Profile
      </h1>
    </section>

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
              <li class="active"><a href="#user-global" data-toggle="tab">General</a></li>
              <li><a href="#user-config" data-toggle="tab">Password</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="user-global">
                <?php echo form_open_multipart('users/updateMyProfile', 'class="form-horizontal" id="myprofileform"'); ?>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                      <?php echo form_input($name);?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <?php echo form_input($email);?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Phone</label>
                    <div class="col-sm-10">
                      <?php echo form_input($phone);?>
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
                      <button type="submit" class="btn btn-danger">Update</button>
                      <button type="reset" class="btn btn-warning">Revert</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="user-config">
                <?php echo form_open('users/updatemypass', 'class="form-horizontal" id="mypassform"'); ?>
                  <div class="form-group">
                    <label for="inputoldpassword" class="col-sm-3 control-label">Old Password</label>
                    <div class="col-sm-9">
                      <?php echo form_input($oldpassword);?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputnewpassword" class="col-sm-3 control-label">New Password</label>
                    <div class="col-sm-9">
                      <?php echo form_input($newpassword);?>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputconfpassword" class="col-sm-3 control-label">Re-New Password</label>
                    <div class="col-sm-9">
                      <?php echo form_input($confpassword);?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Update</button>
                      <button type="reset" class="btn btn-warning">Revert</button>
                    </div>
                  </div>
                </form>
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