<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('auth', TRUE);
		$this->load->config('dbquery/dashboard_query', TRUE);
		$this->sql = $this->config->item('dbquery/dashboard_query');
	}

	public function getUserInfo() {
		
	}
}