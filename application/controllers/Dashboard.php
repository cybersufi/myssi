<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller 
{

	public function __construct()
    {
        parent::__construct();
    }
	
	public function index()
	{
		if ( ! $this->auth_lib->logged_in() )
        {
            redirect('auth/login', 'refresh');
        }
        else
        {
            $this->set_pagename('dashboard');
            $this->data['pagetitle'] = "Dashboard";
            $this->data['user_priv'] = $this->auth_lib->getUserPriviledge($this->auth_lib->get_user_id());
            
            /* Load Template */
            $this->template->render('dashboard', $this->data);
        }
	}

}
