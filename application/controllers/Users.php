<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller 
{

	public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
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

    public function myProfile() 
    {
        if ( ! $this->auth_lib->logged_in() )
        {
            redirect('/', 'refresh');
        }
        else
        {
            $this->set_pagename('myprofile');
            $this->data['pagetitle'] = "My Profil";

            $this->data['message'] = $this->session->flashdata('message');
            $this->data['error'] = $this->session->flashdata('error');

            $usr = $this->user_model->getUserById($this->auth_lib->get_user_id());

            $this->data['name'] = array(
                'name'        => 'name',
                'id'          => 'name',
                'type'        => 'text',
                'value'       => $usr->name,
                'class'       => 'form-control',
                'placeholder' => 'Your Name'
            );

            $this->data['email'] = array(
                'name'        => 'email',
                'id'          => 'email',
                'type'        => 'email',
                'value'       => $usr->email,
                'class'       => 'form-control',
                'placeholder' => 'Your Email'
            );

            $this->data['phone'] = array(
                'name'        => 'phone',
                'id'          => 'phone',
                'type'        => 'text',
                'value'       => $usr->phone,
                'class'       => 'form-control',
                'placeholder' => 'Your Phone Number'
            );

            $this->data['photo'] = array(
                'name'        => 'photo',
                'id'          => 'photo',
                'type'        => 'file',
                'placeholder' => 'Your Photo'
            );

            $this->data['oldpassword'] = array(
                'name'        => 'oldpassword',
                'id'          => 'oldpassword',
                'type'        => 'password',
                'value'       => $this->form_validation->set_value('oldpassword'),
                'class'       => 'form-control',
                'placeholder' => 'Your Old Password'
            );

            $this->data['newpassword'] = array(
                'name'        => 'newpassword',
                'id'          => 'newpassword',
                'type'        => 'password',
                'value'       => $this->form_validation->set_value('newpassword'),
                'class'       => 'form-control',
                'placeholder' => 'Your New Password'
            );

            $this->data['confpassword'] = array(
                'name'        => 'confpassword',
                'id'          => 'confpassword',
                'type'        => 'password',
                'value'       => $this->form_validation->set_value('confpassword'),
                'class'       => 'form-control',
                'placeholder' => 'Re-type New Password'
            );

            /* Load Template */
            $this->template->render('users/myprofile', $this->data);
            
        }
    }

    public function updateMyProfile() 
    {
        if ( ! $this->auth_lib->logged_in())
        {   
            /* Valid form */
            $this->form_validation->set_rules('name', 'Name', 'required');
            $this->form_validation->set_rules('email', 'Email', 'required');

            if ($this->form_validation->run() == TRUE)
            {
                $id = $this->auth_lib->get_user_id();
                $data['name'] = $this->input->post('name');
                $data['email'] = $this->input->post('email');
                $data['phone'] = $this->input->post('phone');

                if ($this->user_model->updateUserData($id, $data))
                {
                    $this->session->set_flashdata('message', $this->notification->messages());
                    redirect('users/myprofile', 'refresh');
                }
                else
                {
                    $this->session->set_flashdata('error', $this->notification->errors());
                    redirect('users/myprofile', 'refresh');
                }
            }
            else
            {
                redirect('users/myprofile', 'refresh');
            }
        } else {
            redirect('/', 'refresh');
        }
    }

    public function updateMyPass() {
        return null;
    }

}
