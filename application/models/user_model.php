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

	public function checkAccess($c,$access,$module,$sf_user,$projects_id=false)
	{
		if(!Users::hasAccess($access,$module,$sf_user,$projects_id))
		{
		  $c->redirect('accessForbidden/index');
		}
	}

	public static function hasProjectsAccess($access, $sf_user, $projects)
	{
		if(Users::hasAccess($access,'projects',$sf_user,$projects->getId()) and Projects::hasViewOwnAccess($sf_user,$projects))
		{
		  return true;
		}
		else
		{
		  return false;
		}
	}

	public static function hasTasksAccess($access, $sf_user, $tasks, $projects)
	{
	if(Users::hasAccess($access,'tasks',$sf_user,$projects->getId()) and Tasks::hasViewOwnAccess($sf_user,$tasks,$projects))
	{
	  return true;
	}
	else
	{
	  return false;
	}
	}

	public static function hasTicketsAccess($access, $sf_user, $tasks, $projects=null)
	{
	if($projects)
	{
	  if(Users::hasAccess($access,'tickets',$sf_user,$projects->getId()) and Tickets::hasViewOwnAccess($sf_user,$tasks,$projects))
	  {
	    return true;
	  }
	  else
	  {
	    return false;
	  }
	}
	else
	{
	  if(Users::hasAccess($access,'tickets',$sf_user) and Tickets::hasViewOwnAccess($sf_user,$tasks))
	  {
	    return true;
	  }
	  else
	  {
	    return false;
	  }
	}
	}

	public static function hasDiscussionsAccess($access, $sf_user, $discussions, $projects)
	{
	if(Users::hasAccess($access,'discussions',$sf_user,$projects->getId()) and Discussions::hasViewOwnAccess($sf_user,$discussions,$projects))
	{
	  return true;
	}
	else
	{
	  return false;
	}
	}

	public static function hasAccess($access,$module,$sf_user,$projects_id=false)
	{
	$schema = Users::getAccessSchema($module,$sf_user,$projects_id);
	      
	if(strstr($access,'|'))
	{
	  foreach(explode('|',$access) as $a)
	  {
	    if($schema[$a])
	    {
	      return true;
	    }
	  }
	}
	elseif($schema[$access])
	{
	  return true;
	}

	return false;    
	}

	public function getAccessSchema($module, $userid ,$projects_id=false)
	{
		$access = array();
		$custom_access = array();

		$schema = array('view'      =>false,
						'view_own'  =>false,                    
						'insert'    =>false,
						'edit'      =>false,
						'delete'    =>false);

		$ugroups = $this->db->query($this->sql['get_user_gid'], array($userid));

		if(empty($ugroups->result()) )
		{
			return $schema;
		}

		foreach ($ugroups as $group) {

		}

		$user = $sf_user->getAttribute('user');
		$usersGroups = $user->getUsersGroups();

		switch($module)
		{
			case 'projects':          
			$access = $usersGroups->getAllowManageProjects();           
			break;
			case 'tasks':          
			$access = $usersGroups->getAllowManageTasks();           
			break;
			case 'tickets':          
			$access = $usersGroups->getAllowManageTickets();           
			break;
			case 'discussions':          
			$access = $usersGroups->getAllowManageDiscussions();           
			break;
			case 'projectsComments':
			$access = $usersGroups->getAllowManageProjects();
			break;
			case 'tasksComments':
			$access = $usersGroups->getAllowManageTasks();
			break;
			case 'ticketsComments':
			$access = $usersGroups->getAllowManageTickets();
			break;
			case 'discussionsComments':
			$access = $usersGroups->getAllowManageDiscussions();
			break;  
		}

		if(strstr($module,'Comments'))
		{      
		if($access>0)
		{
		$schema = array('view'      =>true,
		'view_own'  =>true,                            
		'insert'    =>true,
		'edit'      =>true,
		'delete'    =>true);
		}
		}
		else
		{
		switch($access)
		{    
		//full access
		case '1':     
		$schema = array('view'      =>true,
		'view_own'  =>false,                            
		'insert'    =>true,
		'edit'      =>true,
		'delete'    =>true);
		break;     
		//view only             
		case '2':     
		$schema = array('view'      =>true,
		'view_own'  =>false,                            
		'insert'    =>false,
		'edit'      =>false,
		'delete'    =>false);
		break;   
		//view own only       
		case '3':     
		$schema = array('view'      =>true,
		'view_own'  =>true,                            
		'insert'    =>false,
		'edit'      =>false,
		'delete'    =>false);
		break;
		//manage_own_lnly  
		case '4':     
		$schema = array('view'      =>true,
		'view_own'  =>true,                            
		'insert'    =>true,
		'edit'      =>true,
		'delete'    =>true);
		break;
		}   
		}

		//print_r($schema);

		return $schema;
	}
}

?>