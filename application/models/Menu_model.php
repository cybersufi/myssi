<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends CI_Model
{
	private $ci;            
    private $id_menu            = 'id="menu"';
    private $class_menu         = 'class="menu"';
    private $class_parent       = 'class="parent"';
    private $class_last         = 'class="last"';
    private $active_module		= '';
    private $non_parent_head    = '';
    private 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('dbquery/menu_query', TRUE);
		$this->sql = $this->config->item('dbquery/menu_query');
	}
 	
 	public function set_active_module($module)
 	{
 		$this->active_module = $module;
 	}

 	public function build_menu() {
 		$html_out = '<ul class="sidebar-menu"> <li class="header">MAIN NAVIGATION</li>';
 		$html_out .= $this->get_childs('0');
 		$html_out .= '</ul>';

 		return $html_out;
 	}

    private function get_childs($id)
    {
        $menu = array();
 
        $query = $this->ci->db->query($this->sql['get_menu_by_parent'], array($id));
        
      	foreach ($query->result() as $row)
        {
            $id = $row->id;
            $name = $row->name;
            $page_id = $row->page_id;
            $module_name = $row->module_name;
            $url = $row->url;
            $sort = $row->sort;
            $parent_id = $row->parent_id;
            $is_parent = $row->is_parent;
            $menu_key = $row->smenu_key;
            $menu_icon = $row->menu_icon;
            $access = $row->access_name;

            if (strcmp($module_name, 'dashboard')) {
            	$html_out .= '<li>'.anchor($url, '<i class="fa '.$menu_icon.'"></i> <span>'.$name.'</span>').'</li>';
            }
            else 
            {
            	if ($this->auth_lib->hasAccess($access, $module_name, $this->auth_lib->get_user_id(), false)) {
		            if ($is_parent == TRUE)
		            {
		            	$html_out .= '<li class="treeview">'.anchor("#", '<i class="fa '.$menu_icon.'"></i><span>'.$name.'</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>');
		            	$html_out .= '<ul class="treeview-menu">'
		            	$html_out .= $this->get_childs($id);
		            	$html_out .= '</ul></li>'

		            }
		            else
		            {
		            	$html_out .= (strcmp($this->active_module, $url)) ? '<li class="active">' : '<li>';
						$html_out .= '<li>'.anchor($url, '<i class="fa '.$menu_icon.'"></i> <span>'.$name.'</span>').'</li>';
		            }
		        }
	        }
        }
        return $html_out;
    }
}

?>