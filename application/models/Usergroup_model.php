<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usergroup_model extends CI_Model
{
	protected $messages;
	protected $errors;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('dbquery/usergroup_query', TRUE);
		$this->sql = $this->config->item('dbquery/usergroup_query');
	}

	public function getGroupDetail($groupid) 
	{
		$query = $this->db->query($this->sql['get_group_detail_by_id'], array($groupid));
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		return false;
	}

	public function getGroupList()
	{
		$this->load->library('table');
		$query = $this->db->query($this->sql['get_all_groups']);
		if ($query->num_rows() > 0) {
			$groups = array();
			//print_r($query->result());
			foreach ($query->result() as $group) {
				$g['id'] = $group->id;
				$g['name'] = $group->name;
				$g['description'] = $group->description;
				$g['color'] = $group->color;
				$g['assigned'] = ($group->assigned != null) ? $group->assigned : 0;
				$g['privs'] = array(
					'projects' => $this->translateAccessScheme($group->projects_priv),
					'tasks' => $this->translateAccessScheme($group->tasks_priv),
					'tickets' => $this->translateAccessScheme($group->tickets_priv),
					'discussions' => $this->translateAccessScheme($group->discussions_priv)
				);
				$g['extra'] = array(
					'config' => $this->translateAccessScheme($group->config_priv),
					'users' => $this->translateAccessScheme($group->users_priv)
				);
				array_push($groups, $g);
			}
			return $groups;
		} else {
			return NULL;
		}	
	}

	private function translateAccessScheme($access) {
		switch($access)
		{    
			//none
			case 0:
			$schema = 'none';
			break;
			//full access
			case 1:     
			$schema = 'full access';
			break;     
			//view only             
			case 2:     
			$schema = 'view only';
			break;   
			//view own only       
			case 3:     
			$schema = 'view own only';
			break;
			//manage_own_only  
			case 4:     
			$schema = 'manage own only';
			break;
			default :
				$scheme = 'unknown access';
		}

		return $schema;
	}
}

?>