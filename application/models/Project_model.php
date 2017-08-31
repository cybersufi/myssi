<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('dbquery/project_query', TRUE);
		//$this->lang->load('ion_auth');

		$this->sql = $this->config->item('dbquery/project_query');
	}

	public function hasViewOwnAccess($user_id, $project_id)
	{
		return TRUE;
	}
}

?>