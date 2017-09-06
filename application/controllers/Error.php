<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Error extends MY_Controller 
{

	public function __construct()
    {
        parent::__construct();
    }
	
	public function index()
	{
        redirect('/', 'refresh');
	}

    public function pagenotfound()
    {
        $this->set_pagename('pagenotfound');
        $this->data['pagetitle'] = "Page Not Found";
        $this->template->render('new_errors/error_404', $this->data);
    }

}
