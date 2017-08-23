<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MY_Controller 
{

	public function __construct()
    {
        parent::__construct();
    }
	
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function login()
	{
		/* set necessity files */
		$this->set_pagename('auth');
		$this->load_bsplugin('iCheck');
		
		/* Valid form */
        $this->form_validation->set_rules('identity', 'Identity', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        $this->data['forgot_password']     = $this->config->item('forgot_password');
        
        /*if ($this->form_validation->run() == TRUE)
        {
            $remember = (bool) $this->input->post('remember');

            if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember))
            {
                if ( ! $this->ion_auth->is_admin())
                {
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect('/', 'refresh');
                }
                else
                {
                    /* Data 
                    $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

                    /* Load Template 
                    $this->template->auth_render('auth/choice', $this->data);
                }
            }
            else
            {
                $this->session->set_flashdata('message', $this->ion_auth->errors());
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
                'placeholder' => lang('auth_your_email')
            );
            $this->data['password'] = array(
                'name'        => 'password',
                'id'          => 'password',
                'type'        => 'password',
                'class'       => 'form-control',
                'placeholder' => lang('auth_your_password')
            );*/
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
}
