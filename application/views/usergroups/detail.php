  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $pagetitle; ?>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-warning">
            <!-- /.box-header -->
            <div class="box-body">
              <?php echo form_open($form_url, 'class="form-group has-feedback" id="usergroups"'); ?>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Group Name</label>
                      <?php echo form_input($name); ?>
                    </div>
                    <!-- textarea -->
                    <div class="form-group">
                      <label>Group Description</label>
                      <?php echo form_textarea($description);?>
                    </div>
                    <div class="form-group">
                      <label>Group Color</label>
                      <?php echo form_dropdown($color_value);?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <!-- select -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <h3 class="page-header">Basic Access</h3>
                      <label>Projects</label>
                      <?php echo form_dropdown($projects); ?>
                      <label>Tasks</label>
                      <?php echo form_dropdown($tasks); ?>
                      <label>Tickets</label>
                      <?php echo form_dropdown($tickets); ?>
                      <label>Discussions</label>
                      <?php echo form_dropdown($discussions); ?>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <h3 class="page-header">Extra Access Configuration</h3>
                    <div class="form-group">
                      <label>Configuration</label>
                      <?php echo form_checkbox($configs); ?>
                      <br><label>Users</label>
                      <?php echo form_checkbox($users); ?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <?php
                      echo form_submit('submit', 'Save', array('class' => 'btn btn-danger btn-flat'));
                      echo form_submit('reset', 'Cancel', array('class' => 'btn btn-warning btn-flat'));
                    ?>
                  </div>
                </div>
              <?php echo form_close(); ?>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->