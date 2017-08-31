<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['get_user_gid'] 					= 'SELECT * FROM users_groups WHERE users_groups.user_id = ? INNER JOIN groups ON user_groups.group_id = groups.id';
$config['get_group_priv_by_id'] 			= 'SELECT * FROM groups WHERE id = ?';