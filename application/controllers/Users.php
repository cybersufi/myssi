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
        if ($this->auth_lib->logged_in() )
        {
            redirect('users/userlist', 'refresh');
        }
        else 
        {
            redirect('auth/login', 'refresh');
        }
    }

	public function userlist()
	{
		if ($this->auth_lib->logged_in() )
        {
            $this->set_pagename('userlist');
            $this->data['pagetitle'] = "Users";
            $this->load_bsplugin('dataTables'); 
            
            $this->data['users'] = $this->user_model->getAllUSer();


            $this->template->render('users/userlist', $this->data);
        }
        else 
        {
            redirect('auth/login', 'refresh');
        }
	}

    public function addUser() {
        return null;
    }

    public function userDetail() {

    }

    public function myProfile() 
    {
        if ( $this->auth_lib->logged_in() )
        {
            $this->set_pagename('myprofile');
            $this->data['pagetitle'] = "My Profil";
            $action = $this->input->post('action');

            if ($action == 'update_info') {
                $this->form_validation->set_rules('name', 'Name', 'required');
                $this->form_validation->set_rules('email', 'Email', 'required');
            }
            else if ($action == 'change_pass')
            {
                $this->form_validation->set_rules('oldpassword', 'Old Password', 'required');
                $this->form_validation->set_rules('newpassword', 'New Password', 'required');
                $this->form_validation->set_rules('confpassword', 'Confim Password', 'required|matches[newpassword]');
            }

            if ($this->form_validation->run() == TRUE)
            {
                if ($action == 'update_info') 
                {
                    $photo = $this->do_upload_foto();
                    if (!$photo) {
                        $this->session->set_flashdata('error', $this->notification->errors());
                    } else {
                        $data['photo'] = $photo;
                    }

                    $id = $this->auth_lib->get_user_id();
                    $data['name'] = $this->input->post('name');
                    $data['email'] = $this->input->post('email');
                    $data['phone'] = $this->input->post('phone');

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
                else if ($action == 'change_pass')
                {
                    $id = $this->auth_lib->get_user_id();
                    $email = $this->auth_lib->get_user_email();

                    $oldpassword = $this->input->post('oldpassword');
                    $newpassword = $this->input->post('newpassword');

                    if ($this->auth_lib->change_password($email, $oldpassword, $newpassword))
                    {
                        $this->session->set_flashdata('message',$this->auth_lib->messages());
                        $this->session->set_flashdata('active','userpass');
                        redirect('users/myprofile', 'refresh');
                    } 
                    else 
                    {
                        $this->session->set_flashdata('error',$this->auth_lib->errors());
                        $this->session->set_flashdata('active','userpass');
                        redirect('users/myprofile', 'refresh');
                    }
                }
            }
            else
            {
                $this->data['message'] = $this->session->flashdata('message');
                $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->data['active'] = ($this->session->flashdata('active')) ? $this->session->flashdata('active') : 'userprofile';

                $usr = $this->user_model->getUserById($this->auth_lib->get_user_id());

                $this->data['name'] = array(
                    'name'        => 'name',
                    'id'          => 'name',
                    'type'        => 'text',
                    'value'       => $this->form_validation->set_value('name', $usr->name),
                    'class'       => 'form-control',
                    'placeholder' => 'Your Name'
                );

                $this->data['email'] = array(
                    'name'        => 'email',
                    'id'          => 'email',
                    'type'        => 'email',
                    'value'       => $this->form_validation->set_value('email', $usr->email),
                    'class'       => 'form-control',
                    'placeholder' => 'Your Email'
                );

                $this->data['phone'] = array(
                    'name'        => 'phone',
                    'id'          => 'phone',
                    'type'        => 'text',
                    'value'       => $this->form_validation->set_value('phone', $usr->phone),
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
        else
        {
            redirect('auth/login', 'refresh');
        }
    }

    public function do_upload_foto()
    {
        $config['upload_path']          = './'.$this->config->item('photo_dir').'/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 100;
        $config['max_width']            = 1024;
        $config['max_height']           = 768;
        $config['overwrite']            = TRUE;
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('photo'))
        {
            $this->notification->set_error($this->upload->display_errors('',''));
            return false;
        }
        else
        {
            return $this->upload->data('file_name');
        }
    }

}
