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
            <div class="box-header">
              <a class="btn btn-primary btn-sm" href="<?php echo base_url('usergroups/add');?>"><i class="fa fa-user-plus"></i> Add</a>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="usergroups" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th width="100">Action</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Group Color</th>
                  <th>Group Access</th>
                  <th>Extra Access</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($groups as $group)
                  {
                    $out = '<tr>';
                    $out .= '<td><a class="btn btn-default btn-xs '.$color.'" href="'.base_url('usergroups/edit/'.$group['id']).'"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;<a class="btn btn-default btn-xs '.$color.'" href="'.base_url('usergroups/delete/'.$group['id']).'"><i class="fa fa-trash-o"></i></a></td>';
                    $out .= '<td><b>'.ucwords($group['name']).'</b><br><span style="font-size:11px;"> - Assigned Users: '.$group['assigned'].'</span></td>';
                    $out .= '<td>'.$group['description'].'</td>';
                    $out .= '<td><span class="label bg-'.$group['color'].'">'.ucwords($group['color']).'</span></td>';
                    $out .= '<td><table class="listingDataTable">';
                    foreach ($group['privs'] as $privs => $access) {
                      $out .= '<tr>
                              <td style="white-space:normal" width="100"><b>'.ucwords($privs).':</b></td>
                              <td style="white-space:normal">'.ucwords($access).'</td>
                             </tr>';
                    }
                    $out .= '</table></td>';
                    $out .= '<td><table class="listingDataTable">';
                    foreach ($group['extra'] as $privs => $access) {
                      $out .= '<tr>
                              <td style="white-space:normal" width="50"><b>'.ucwords($privs).':</b></td>
                              <td style="white-space:normal">'.ucwords($access).'</td>
                             </tr>';
                    }
                    $out .= '</table></td>';
                    $out .= '</tr>';

                    echo $out;
                  }
                ?>
                </tbody>
                <tfoot>
                <tr>
                  <th widht="100">Action</th>
                  <th>Name</th>
                  <th>Description</th>
                  <th>Group Color</th>
                  <th>Group Access</th>
                  <th>Extra Access</th>
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