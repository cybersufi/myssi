<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('dbquery/user_query', TRUE);
		$this->sql = $this->config->item('dbquery/user_query');
	}

	public function getUserList()
	{
		$this->load->library('table');
		$query = $this->db->query($this->sql['get_all_users']);
		return false;		
	}

	public function getUserColor($user_id)
	{
		$query = $this->db->query($this->sql['get_user_color'], array($user_id));
		if ($query->num_rows() > 0) {
			return $query->row()->color;
		} else {
			return 'blue';
		}
	}

}

?>