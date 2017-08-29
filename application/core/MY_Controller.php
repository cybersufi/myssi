<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
        public function __construct()
        {
                parent::__construct();

                /* COMMON :: ADMIN & PUBLIC */
                /* Load */
                $this->load->database();
                $this->load->config('myssi');           //our app related config.
                $this->load->config('templates');       //config for templates.
                $this->load->config('myssiapp', TRUE);           //our app related config.
                $this->load->config('bsplugins',TRUE);       //config for bootstrap templates.
                $this->load->library(array('form_validation', 'auth', 'template'));
                //$this->load->model('common/prefs_model');

                $this->bsplugins = $this->config->item('bsplugins');
                $this->apps = $this->config->item('myssiapp');

                /* Data */
                $this->data['frameworks_dir']   = $this->config->item('frameworks_dir');
                $this->data['plugins_dir']      = $this->config->item('plugins_dir');
                $this->data['app_dir']          = $this->config->item('app_dir');
                $this->data['avatar_dir']       = $this->config->item('avatar_dir');
                $this->data['upload_dir']       = $this->config->item('upload_dir');
                $this->data['attachment_dir']   = $this->config->item('attachment_dir');

                $this->data['title']            = $this->config->item('title');
                $this->data['title_lg']         = $this->config->item('title_lg');
                $this->data['title_mini']       = $this->config->item('title_mini');
                $this->data['pluginscss']       = array();
                $this->data['pluginsjs']        = array();
        }

        public function load_bsplugin($plugname)
        {
                array_push($this->data['pluginscss'], $this->bsplugins[$plugname]['css']);
                array_push($this->data['pluginsjs'], $this->bsplugins[$plugname]['js']);
        }

        public function set_pagename($pagename)
        {
                $this->data['pagecss'] = $this->apps[$pagename]['css'];
                $this->data['pagejs'] = $this->apps[$pagename]['js'];
                $this->data['is_main_header'] = $this->apps[$pagename]['main_header'];
                $this->data['is_main_sidebar'] = $this->apps[$pagename]['main_sidebar'];
        }

}
