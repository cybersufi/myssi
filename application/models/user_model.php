<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
	protected $messages;
	protected $errors;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('dbquery/user_query', TRUE);
		$this->sql = $this->config->item('dbquery/user_query');
	}

	public function set_error($error)
	{
		$this->errors[] = $error;
		return $error;
	}

	public function set_message($message)
	{
		$this->messages[] = $message;
		return $message;
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

	public function getUserByID($userid)
	{
		$query = $this->db->query($this->sql['get_user_by_id'], array($userid));
		if ($query->num_rows() > 0) {
			return $query->row();
		} else {
			return null;
		}
	}

	public function updateUserData($userid, array $data) 
	{
		$user = $this->db->query($this->sql['get_user_by_id'], array((float)$id))->row();
		
		$this->db->trans_begin();

		if (array_key_exists('email', $data) && $this->auth_lib->email_check($data['email']))
		{
			$this->notification->set_error('Email Already Used or Invalid');
			$this->notification->set_error('Unable to Update Account Information');
			return FALSE;
		}

		$param = array ($data['name'], $data['email'], $data['phone'], $data['photo'], $userid);

		$this->db->query($this->sql['update_user_by_id'], $param);

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->notification->set_error('Unable to Update Account Information');
			return FALSE;
		}

		$this->db->trans_commit();
		$this->notification->set_message('Account Information Successfully Updated');
		return TRUE;
	}
}

?>