<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <!-- Dashboard -->
        <li>
          <a href="<?php echo base_url('dashboard'); ?>">
            <i class="fa fa-home"></i> <span>Dashboard</span>
          </a>
        </li>
        <!-- /Dashboard -->
        <?php if ($user_priv['projects_priv']['schema']['view'])
          {
        ?>
        <!-- Projects -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-sitemap"></i> <span>Projects</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.html"><i class="fa fa-circle-o"></i>View All</a></li>
            <?php if ($user_priv['projects_priv']['schema']['insert']) {?>
            <li><a href="index2.html"><i class="fa fa-circle-o"></i>Add Project</a></li>
            <?php } ?>
          </ul>
        </li>
        <!-- /Projects -->
        <?php
          }

          if ($user_priv['tasks_priv']['schema']['view'])
          {
        ?>
        <!-- Task -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-tasks"></i> <span>Tasks</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.html"><i class="fa fa-circle-o"></i>View All</a></li>
            <?php if ($user_priv['tasks_priv']['schema']['insert']) {?>
            <li><a href="index2.html"><i class="fa fa-circle-o"></i>Add Task</a></li>
            <?php } ?>
          </ul>
        </li>
        <!-- /Task -->
        <?php
          }

          if ($user_priv['tickets_priv']['schema']['view'])
          {
        ?>
        <!-- Tickets -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-bell"></i> <span>Tickets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.html"><i class="fa fa-circle-o"></i>View All</a></li>
            <?php if ($user_priv['tickets_priv']['schema']['insert']) {?>
            <li><a href="index2.html"><i class="fa fa-circle-o"></i>Add Ticket</a></li>
            <?php } ?>
          </ul>
        </li>
        <!-- /Ticket -->
        <?php
          }

          if ($user_priv['discussions_priv']['schema']['view'])
          {
        ?>
        <!-- Discussion -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-comments"></i> <span>Discussions</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.html"><i class="fa fa-circle-o"></i>View All</a></li>
            <?php if ($user_priv['discussions_priv']['schema']['insert']) {?>
            <li><a href="index2.html"><i class="fa fa-circle-o"></i>Add Discussions</a></li>
            <?php } ?>
          </ul>
        </li>
        <!-- /Discussion -->
        <?php
          }

          if ($user_priv['users_priv']['schema']['view'])
          {
        ?>
        <!-- Users -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Users</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url('users'); ?>"><i class="fa fa-circle-o"></i>View All</a></li>
            <?php if ($user_priv['users_priv']['schema']['insert']) {?>
            <li><a href="<?php echo base_url('users/adduser'); ?>"><i class="fa fa-circle-o"></i>Add Users</a></li>
            <?php } ?>
          </ul>
        </li>
        <!-- /Users -->
        <?php
          }

          if ($user_priv['config_priv']['schema']['view'])
          {
        ?>
        <!-- Config -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-gear"></i> <span>Config</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li>
              <a href="#"><i class="fa fa-circle-o"></i> General
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i> General</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i> Feature</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i> Email Options</a></li>
                <li><a href="#"><i class="fa fa-circle-o"></i> Login Page</a></li>
              </ul>
            </li>
            <li>
              <a href="#"><i class="fa fa-circle-o"></i> User
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu">
                <li><a href="<?php echo base_url('usergroups'); ?>"><i class="fa fa-circle-o"></i> User Groups</a></li>
              </ul>
            </li>
          </ul>
        </li>
        <!-- /Config -->
        <!-- Tools -->
        <li class="treeview">
          <a href="#">
            <i class="fa fa-wrench"></i> <span>Tools</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.html"><i class="fa fa-circle-o"></i>Backups</a></li>
          </ul>
        </li>
        <!-- /Tools -->
        <?php 
          }
        ?>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>