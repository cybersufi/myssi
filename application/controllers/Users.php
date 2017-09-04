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

            $this->data['message'] = $this->session->flashdata('message');

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

    public function updateMyProfile() {
        return null;
    }

    public function updateMyPass() {
        return null;
    }

}
