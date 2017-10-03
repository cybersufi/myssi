<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usergroups extends MY_Controller 
{

	public function __construct()
    {
        parent::__construct();
        $this->load->model('usergroup_model');
    }
	
    public function index()
    {
        if ($this->auth_lib->logged_in() )
        {
            redirect('usergroups/lists', 'refresh');
        }
        else 
        {
            redirect('auth/login', 'refresh');
        }
    }

	public function lists()
	{
		if ($this->auth_lib->logged_in() )
        {
            $this->set_pagename('usergrouplist');
            $this->data['pagetitle'] = "User Groups";
            $this->load_bsplugin('dataTables'); 
            $this->data['groups'] = $this->usergroup_model->getGroupList();
            $this->template->render('usergroups/grouplist', $this->data);
        }
        else 
        {
            redirect('auth/login', 'refresh');
        }
	}

    public function add() {
        if ($this->auth_lib->logged_in() )
        {
            $this->set_pagename('groupdetail');
            $this->data['pagetitle'] = "Add New Groups";
            $this->template->render('usergroups/groupdetail', $this->data);
        }
        else 
        {
            redirect('auth/login', 'refresh');
        }
    }

    public function view($groupid) {
        return null;
    }

    public function delete($groupid) {
        return null;
    }

    public function edit($groupid) {
        return null;
    }

}
