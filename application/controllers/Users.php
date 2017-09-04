<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller 
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
            $this->data['pagetitle'] = "Users";
            $this->load_bsplugin('dataTables');
    
            $this->template->render('admin/dashboard/index', $this->data);
        }
	}

    public function addUser() {
        return null;
    }

    public function userDetail() {

    }

    public function myProfile() {
        if ( ! $this->auth_lib->logged_in() )
        {
            redirect('/', 'refresh');
        }
        else
        {
            $this->set_pagename('myprofile');
            $this->data['pagetitle'] = "My Profil";

            /* Valid form */
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                /*$remember = (bool) $this->input->post('remember');

                if ($this->auth_lib->login($this->input->post('identity'), $this->input->post('password'), $remember))
                {
                    $this->session->set_flashdata('message', $this->auth_lib->messages());
                    redirect('/', 'refresh');
                }
                else
                {
                    $this->session->set_flashdata('message', $this->auth_lib->errors());
                    redirect('auth/login', 'refresh');
                }*/
            }
            else
            {
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['name'] = array(
                    'name'        => 'name',
                    'id'          => 'name',
                    'type'        => 'text',
                    'value'       => $this->form_validation->set_value('name'),
                    'class'       => 'form-control',
                    'placeholder' => 'Your Name'
                );

                $this->data['email'] = array(
                    'name'        => 'email',
                    'id'          => 'email',
                    'type'        => 'email',
                    'value'       => $this->form_validation->set_value('email'),
                    'class'       => 'form-control',
                    'placeholder' => 'Your Email'
                );

                $this->data['phone'] = array(
                    'name'        => 'phone',
                    'id'          => 'phone',
                    'type'        => 'text',
                    'value'       => '',
                    'class'       => 'form-control',
                    'placeholder' => 'Your Phone Number'
                );

                $this->data['photo'] = array(
                    'name'        => 'photo',
                    'id'          => 'photo',
                    'type'        => 'file',
                    'class'       => 'form-control',
                    'placeholder' => 'Your Photo'
                );
                /* Load Template */
                $this->template->render('users/myprofile', $this->data);
            }
        }
    }

}
