<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends CI_Model
{
	private $active_module		= '';
    private $active_menu_key    = '';

    
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
        $query = $this->db->query($this->sql['get_menu_key_by_url'], array($module));
        $this->active_menu_key = ($query->row()) ? $query->row()->menu_key : '';
 	}

 	public function build_menu($content) {
        $this->set_active_module($content);
 		$html_out = '<ul class="sidebar-menu">'."\n\t\t".' <li class="header">MAIN NAVIGATION</li>'."\n";
 		$html_out .= $this->get_childs('0');
 		$html_out .= '</ul>';

 		return $html_out;
 	}

    private function get_childs($id)
    {
        $html_out = '';
 
        $query = $this->db->query($this->sql['get_menu_by_parent'], array($id));
        
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
            $menu_key = $row->menu_key;
            $menu_icon = $row->menu_icon;
            $access = $row->access_name;

            if ($module_name == 'dashboard') {
                $html_out .= ($module_name == $this->active_module) ? '<li class="active">' : '<li>';
            	$html_out .= anchor($url, '<i class="fa '.$menu_icon.'"></i> <span>'.$name.'</span>').'</li>'."\n";
            }
            else 
            {
            	if ($this->auth_lib->hasAccess($access, $module_name, $this->auth_lib->get_user_id(), false)) {
		            if ($is_parent == TRUE)
		            {
                        $html_out .= (strpos($this->active_menu_key, $menu_key) !== false ) ? '<li class="treeview active">'."\n" : '<li class="treeview">'."\n";
		            	$html_out .= anchor("#", '<i class="fa '.$menu_icon.'"></i><span>'.$name.'</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>')."\n";
		            	$html_out .= '<ul class="treeview-menu">'."\n";
		            	$html_out .= $this->get_childs($id);
		            	$html_out .= '</ul></li>'."\n";

		            }
		            else
		            {
		            	$html_out .= ($url == $this->active_module) ? '<li class="active">' : '<li>';
						$html_out .= anchor($url, '<i class="fa '.$menu_icon.'"></i> <span>'.$name.'</span>').'</li>'."\n";
		            }
		        }
	        }
        }
        return $html_out;
    }
}

?>