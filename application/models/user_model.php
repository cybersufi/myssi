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

	public function getAllUSer()
	{
		$uq = $this->db->query($this->sql['get_all_users']);
		if ($uq->num_rows() > 0) {
			$users = array();
			foreach ($uq->result() as $user) {
				$u['id'] = $user->id;
				$u['name'] = $user->name;
				$u['phone'] = $user->phone;
				$u['email'] = $user->email;
				$u['is_active'] = $user->active;
				$u['groups'] = array();

				$ug = $this->auth_lib->get_user_groups($user->id);
				if ($ug != null) {
					foreach ($ug as $g) {
						$gr['name'] = $g->name;
						$gr['color'] = $g->color;
						array_push($u['groups'], $gr);
					}
				}
				array_push($users, $u);
			}
			return $users;
		} else {
			return NULL;
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
		$user = $this->db->query($this->sql['get_user_by_id'], array((float)$userid))->row();
		
		$this->db->trans_begin();

		if (array_key_exists('email', $data) && $this->auth_lib->email_check($data['email']))
		{
			$uid = $this->db->query($this->sql['get_userid_by_email'], array($data['email']))->row();
			if ($uid->id != $userid) {
				$this->notification->set_error('Email Already Used or Invalid');
				$this->notification->set_error('Unable to Update Account Information');
				return FALSE;
			}
		}

		if (array_key_exists('photo', $data)) {
			$param = array ($data['name'], $data['email'], $data['phone'], $data['photo'], $userid);
			$this->db->query($this->sql['update_user_with_photo'], $param);
		} else {
			$param = array ($data['name'], $data['email'], $data['phone'], $userid);
			$this->db->query($this->sql['update_user_without_photo'], $param);
		}

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