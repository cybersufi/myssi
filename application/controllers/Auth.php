<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller 
{

	public function __construct()
    {
        parent::__construct();
        $this->load->config('auth', TRUE);           //our app related config.
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'auth'), $this->config->item('error_end_delimiter', 'auth'));
    }
	
	public function index()
	{
		if ( ! $this->auth_lib->logged_in())
        {
            redirect('auth/login', 'refresh');
        }
        else
        {
            redirect('/', 'refresh');
        }
	}

	public function login()
	{
        if ( ! $this->auth_lib->logged_in())
        {
    		/* set necessity files */
    		$this->set_pagename('auth');
    		$this->load_bsplugin('iCheck');
    		
    		/* Valid form */
            $this->form_validation->set_rules('identity', 'Identity', 'required');
            $this->form_validation->set_rules('password', 'Password', 'required');

            $this->data['forgot_password']     = $this->config->item('forgot_password');
            
            if ($this->form_validation->run() == TRUE)
            {
                $remember = (bool) $this->input->post('remember');

                if ($this->auth_lib->login($this->input->post('identity'), $this->input->post('password'), $remember))
                {
                    $this->session->set_flashdata('message', $this->auth_lib->messages());
                    redirect('/', 'refresh');
                }
                else
                {
                    $this->session->set_flashdata('message', $this->auth_lib->errors());
    			    redirect('auth/login', 'refresh');
                }
            }
            else
            {
                $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                $this->data['identity'] = array(
                    'name'        => 'identity',
                    'id'          => 'identity',
                    'type'        => 'email',
                    'value'       => $this->form_validation->set_value('identity'),
                    'class'       => 'form-control',
                    'placeholder' => 'Your Email'
                );
                $this->data['password'] = array(
                    'name'        => 'password',
                    'id'          => 'password',
                    'type'        => 'password',
                    'class'       => 'form-control',
                    'placeholder' => 'Your Password'
                );
                /* Load Template */
                $this->template->render('auth/login', $this->data);
            }
        } else {
            redirect('/', 'refresh');
        }
    }

    public function logout($src = NULL)
    {
        $logout = $this->auth_lib->logout();
        $this->session->set_flashdata('message', $this->auth_lib->messages());
        redirect('/', 'refresh');
    }

}
