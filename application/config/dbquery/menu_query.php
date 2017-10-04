<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*select*/
$config['get_all_menu']						= 'SELECT * FROM menu';
$config['get_menu_by_parent']				= 'SELECT menu.*, menu_module.module_name, menu_access.access_name FROM menu LEFT JOIN menu_module ON menu_module.id = menu.module_id LEFT JOIN menu_access on menu_access.id = menu.access_type WHERE menu.parent_id = ? ORDER BY menu.sort ASC';


/*update*/
$config['update_user_without_photo']		= 'UPDATE users SET name=?, email=?, phone=? WHERE id=?';
$config['update_user_with_photo']			= 'UPDATE users SET name=?, email=?, phone=?, photo=? WHERE id=?';



