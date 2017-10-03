<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*select*/
$config['get_all_groups']					= 'SELECT groups.*, assigned_users.assigned FROM myssi.groups LEFT JOIN assigned_users on groups.id = assigned_users.group_id ORDER BY id ASC';


/*update*/
$config['update_user_without_photo']		= 'UPDATE users SET name=?, email=?, phone=? WHERE id=?';
$config['update_user_with_photo']			= 'UPDATE users SET name=?, email=?, phone=?, photo=? WHERE id=?';



