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
            $this->load_bsplugin('dataTables'); 
            $this->data['module_key'] = 'usergroups/lists';
            $this->data['pagetitle'] = "User Groups";
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
                $this->data['form_url'] = 'usergroups/add';
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
                    'rows'        => '3',
                    'placeholder' => 'Group description'
                );

                $this->data['color_value'] = array(
                    'id'        => 'color',
                    'class'     => 'form-control',
                    'options'   => array(
                        'red' =>  'Red',
                        'blue' =>  'Blue',
                        'black' =>  'Black',
                        'purple' =>  'Purple',
                        'yellow' =>  'Yellow',
                        'green' =>  'Green'
                    )
                );

                $this->data['projects'] = array(
                    'id'        => 'projects_priv',
                    'class'     => 'form-control',
                    'options'   => array(
                        '0' =>  'None',
                        '1' =>  'Full Access',
                        '2' =>  'View Only',
                        '3' =>  'View Own Only',
                        '4' =>  'Manage Own Only'
                    )
                );

                $this->data['tasks'] = array(
                    'id'        => 'tasks_priv',
                    'class'     => 'form-control',
                    'options'   => array(
                        '0' =>  'None',
                        '1' =>  'Full Access',
                        '2' =>  'View Only',
                        '3' =>  'View Own Only',
                        '4' =>  'Manage Own Only'
                    )
                );

                $this->data['tickets'] = array(
                    'id'        => 'tickets_priv',
                    'class'     => 'form-control',
                    'options'   => array(
                        '0' =>  'None',
                        '1' =>  'Full Access',
                        '2' =>  'View Only',
                        '3' =>  'View Own Only',
                        '4' =>  'Manage Own Only'
                    )
                );

                $this->data['discussions'] = array(
                    'id'        => 'discussions_priv',
                    'class'     => 'form-control',
                    'options'   => array(
                        '0' =>  'None',
                        '1' =>  'Full Access',
                        '2' =>  'View Only',
                        '3' =>  'View Own Only',
                        '4' =>  'Manage Own Only'
                    )
                );

                $this->data['configs'] = array(
                        'name'          => 'configs',
                        'id'            => 'configs',
                        'value'         => '1',
                        'checked'       => FALSE,
                        'style'         => 'margin:10px'
                );

                $this->data['users'] = array(
                        'name'          => 'users',
                        'id'            => 'users',
                        'value'         => '1',
                        'checked'       => FALSE,
                        'style'         => 'margin:10px'
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

    private function groupview($group = false) 
    {
        if ($this->form_validation->run() == TRUE)
        {
            $data['name']               = $this->input->post('name');
            $data['description']        = $this->input->post('description');
            $data['color']              = $this->input->post('color');
            $data['projects_priv']      = $this->input->post('projects_priv');
            $data['task_priv']          = $this->input->post('projects_priv');
            $data['tickets_priv']       = $this->input->post('projects_priv');
            $data['discussions_priv']   = $this->input->post('projects_priv');
            $data['config_priv']        = $this->input->post('projects_priv');
            $data['users_priv']         = $this->input->post('projects_priv');
            

            if ($this->user_model->updateUserData($id, $data))
            {
                $this->session->set_flashdata('message', $this->notification->messages());
                $this->session->set_flashdata('active','userprofile');
                redirect('users/myprofile', 'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', $this->notification->errors());
                $this->session->set_flashdata('active','userprofile');
                redirect('users/myprofile', 'refresh');
            }
        }
        else
        {
            $this->data['message'] = $this->session->flashdata('message');
            $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

            $this->data['name'] = array(
                'name'        => 'name',
                'id'          => 'name',
                'type'        => 'text',
                'value'       => $this->form_validation->set_value('name', ($group !== FALSE) ? $group->name : ''),
                'class'       => 'form-control',
                'placeholder' => 'Group Name'
            );

            $this->data['description'] = array(
                'name'        => 'description',
                'id'          => 'description',
                'value'       => $this->form_validation->set_value('description', ($group !== FALSE) ? $group->description : ''),
                'class'       => 'form-control',
                'rows'        => '3',
                'placeholder' => 'Group description'
            );

            $this->data['color_value'] = array(
                'id'        => 'color',
                'class'     => 'form-control',
                'options'   => array(
                    'red' =>  'Red',
                    'blue' =>  'Blue',
                    'black' =>  'Black',
                    'purple' =>  'Purple',
                    'yellow' =>  'Yellow',
                    'green' =>  'Green'
                ),
                'selected'  => ($group !== FALSE) ? $group->color : ''
            );

            $this->data['projects'] = array(
                'id'        => 'projects_priv',
                'class'     => 'form-control',
                'options'   => array(
                    '0' =>  'None',
                    '1' =>  'Full Access',
                    '2' =>  'View Only',
                    '3' =>  'View Own Only',
                    '4' =>  'Manage Own Only'
                ),
                'selected'  => ($group !== FALSE) ? $group->projects_priv : ''
            );

            $this->data['tasks'] = array(
                'id'        => 'tasks_priv',
                'class'     => 'form-control',
                'options'   => array(
                    '0' =>  'None',
                    '1' =>  'Full Access',
                    '2' =>  'View Only',
                    '3' =>  'View Own Only',
                    '4' =>  'Manage Own Only'
                ),
                'selected'  => ($group !== FALSE) ? $group->tasks_priv : ''
            );

            $this->data['tickets'] = array(
                'id'        => 'tickets_priv',
                'class'     => 'form-control',
                'options'   => array(
                    '0' =>  'None',
                    '1' =>  'Full Access',
                    '2' =>  'View Only',
                    '3' =>  'View Own Only',
                    '4' =>  'Manage Own Only'
                ),
                'selected'  => ($group !== FALSE) ? $group->tickets_priv : ''
            );

            $this->data['discussions'] = array(
                'id'        => 'discussions_priv',
                'class'     => 'form-control',
                'options'   => array(
                    '0' =>  'None',
                    '1' =>  'Full Access',
                    '2' =>  'View Only',
                    '3' =>  'View Own Only',
                    '4' =>  'Manage Own Only'
                ),
                'selected'  => ($group !== FALSE) ? $group->discussions_priv : ''
            );

            $this->data['configs'] = array(
                    'name'          => 'config_priv',
                    'id'            => 'config_priv',
                    'value'         => '1',
                    'checked'       => ($group !== FALSE) ? $group->config_priv : 0,
                    'style'         => 'margin:10px'
            );

            $this->data['users'] = array(
                    'name'          => 'users_priv',
                    'id'            => 'users_priv',
                    'value'         => '1',
                    'checked'       => ($group !== FALSE) ? $group->users_priv : 0,
                    'style'         => 'margin:10px'
            );
            
            /* Load Template */
            $this->template->render('usergroups/detail', $this->data);
        }
    }

    public function delete($groupid) {
        return null;
    }

    public function edit($groupid) {
        if ($this->auth_lib->logged_in() )
        {
            $this->set_pagename('groupdetail');
            $this->data['module_key'] = 'usergroups/lists';
            $this->data['pagetitle'] = "Group Detail";
            $this->data['form_url'] = 'usergroups/edit/'.$groupid;
            $group = $this->usergroup_model->getGroupDetail($groupid);
            if ($group !== FALSE) {
                $this->groupview($group);
            } else {
                redirect('usergroups/lists', 'refresh');
            }
        }
        else 
        {
            redirect('auth/login', 'refresh');
        }
    }

}
