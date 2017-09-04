<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php echo $title; ?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?php echo base_url($frameworks_dir . '/bootstrap/css/bootstrap.min.css'); ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url($frameworks_dir . '/font-awesome/css/font-awesome.min.css'); ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo base_url($frameworks_dir . '/ionicons/css/ionicons.min.css'); ?>">
  <!-- jvectormap -->
  <link rel="stylesheet" href="<?php echo base_url($plugins_dir . '/jvectormap/jquery-jvectormap-1.2.2.css'); ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url($frameworks_dir . '/adminlte/css/AdminLTE.min.css'); ?>">
  <link rel="stylesheet" href="<?php echo base_url($frameworks_dir . '/adminlte/css/skins/skin-'.$color.'.min.css'); ?>">
  <!-- custom Plugins & page stylesheet -->
  <?php
    if (isset($pluginscss) && (count($pluginscss) > 0)) {
      foreach ($pluginscss as $value) {
        echo '<link rel="stylesheet" href="'.base_url($plugins_dir . $value).'">';
      }
    }

    if (isset($pagecss) && (strlen(trim($pagecss)) > 0))
    {
      echo '<link rel="stylesheet" href="'.base_url($app_dir . $pagecss).'">';
    }
  ?>
</head>