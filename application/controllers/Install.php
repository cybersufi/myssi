<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends MY_Controller 
{

	public function __construct()
    {
        parent::__construct();
        $this->load->config('auth', TRUE);           //our app related config.
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'auth'), $this->config->item('error_end_delimiter', 'auth'));
    }

    public function index() 
    {
    	redirect('install/register', 'refresh');
    }

	public function register()
	{
        $this->set_pagename('auth');
		
		/* Valid form */
        $this->form_validation->set_rules('name', 'name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('password', 'password', 'required');

        if ($this->form_validation->run() == TRUE)
        {
            $name = $this->input->post('name');
            $email = $this->input->post('email');
            $pass = $this->input->post('password');
            $res = $this->auth_lib->register($email, $name, $pass, NULL, NULL, array('1'));
            if ($res) {
            	$this->session->set_flashdata('message', $this->auth_lib->messages());
            } else {
            	$this->session->set_flashdata('error', $this->auth_lib->errors());
            }
            redirect('install/register', 'refresh');
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
                'placeholder' => 'Admin Name'
            );

            $this->data['email'] = array(
                'name'        => 'email',
                'id'          => 'email',
                'type'        => 'email',
                'value'       => $this->form_validation->set_value('email'),
                'class'       => 'form-control',
                'placeholder' => 'Admin Email'
            );

            $this->data['password'] = array(
                'name'        => 'password',
                'id'          => 'password',
                'type'        => 'password',
                'class'       => 'form-control',
                'placeholder' => 'Admin Password'
            );
            /* Load Template */
            $this->template->render('install', $this->data);
        }
    }
}
