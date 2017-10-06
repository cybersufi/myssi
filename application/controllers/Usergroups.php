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
            $this->template->render('usergroups/lists', $this->data);
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
            $this->data['pagetitle'] = "Add New Group";

            if ($this->form_validation->run() == TRUE)
            {
                return false;
            }
            else
            {
                $this->data['message'] = $this->session->flashdata('message');
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

                $this->data['name'] = array(
                    'name'        => 'name',
                    'id'          => 'name',
                    'type'        => 'text',
                    'value'       => $this->form_validation->set_value('name'),
                    'class'       => 'form-control',
                    'placeholder' => 'Group Name'
                );

                $this->data['description'] = array(
                    'name'        => 'description',
                    'id'          => 'description',
                    'value'       => $this->form_validation->set_value('description'),
                    'class'       => 'form-control',
                    'placeholder' => 'Group description'
                );

                

                /* Load Template */
                $this->template->render('usergroups/detail', $this->data);
            }
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
