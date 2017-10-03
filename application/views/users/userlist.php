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
          <div class="box">
            <!--<div class="box-header">
              <h3 class="box-title">Data Table With Full Features</h3>
            </div>-->
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>User Groups</th>
                  <th>Active?</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user)
                  {
                    $out = '<tr>';
                    $out .= '<td>'.$user['id'].'</td>';
                    $out .= '<td>'.$user['name'].'</td>';
                    $out .= '<td>'.$user['email'].'</td>';
                    $out .= '<td>'.$user['phone'].'</td>';
                    $out .= '<td>';
                    foreach ($user['groups'] as $group) {
                      $out .= '<span class="label bg-'.$group['color'].'">'.ucwords($group['name']).'</span>';
                    }
                    $out .= '</td>';
                    $out .= '<td>'; 
                    $out .= ($user['is_active'] == 1) ? '<span class="label bg-green">Active</span>' : '<span class="label bg-red">In-Active</span>';
                    $out .= '</td>';
                    $out .= '<td><a class="btn btn-default btn-xs '.$color.'" href="'.base_url('users/edit/'.$user['id']).'"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;<a class="btn btn-default btn-xs '.$color.'" href="'.base_url('users/delete/'.$user['id']).'"><i class="fa fa-trash-o"></i></a></td>';
                    $out .= '</tr>';
                    echo $out;
                  }
                ?>
                </tbody>
                <tfoot>
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>User Groups</th>
                  <th>Active?</th>
                  <th>Action</th>
                </tr>
                </tfoot>
              </table>
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