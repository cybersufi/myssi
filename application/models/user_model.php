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
			$this->auth_lib->set_error('Email Already Used or Invalid');
			$this->auth_lib->set_error('Unable to Update Account Information');
			return FALSE;
		}

		if (array_key_exists('email', $data) || array_key_exists('password', $data))
		{
			if (array_key_exists('password', $data))
			{
				if( ! empty($data['password']))
				{
					$data['password'] = $this->hash_password($data['password'], $user->salt);
				}
				else
				{
					// unset password so it doesn't effect database entry if no password passed
					unset($data['password']);
				}
			}
		}

		$this->trigger_events('extra_where');
		$this->db->update($this->tables['users'], $data, array('id' => $user->id));

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();

			$this->trigger_events(array('post_update_user', 'post_update_user_unsuccessful'));
			$this->set_error('update_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();

		$this->trigger_events(array('post_update_user', 'post_update_user_successful'));
		$this->set_message('update_successful');
		return TRUE;
	}

}

?>