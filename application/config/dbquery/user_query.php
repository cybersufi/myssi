<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['get_user_gid'] 					= 'SELECT * FROM users_groups WHERE users_groups.user_id = ? INNER JOIN groups ON users_groups.group_id = groups.id';
$config['get_group_priv_by_id'] 			= 'SELECT * FROM groups WHERE id = ?';
$config['get_all_users']					= 'SELECT * FROM users ORDER BY id';
$config['get_user_color']					= 'SELECT groups.color FROM groups JOIN users_groups ON users_groups.group_id = groups.id JOIN users ON users.id = users_groups.user_id WHERE users.id = ? GROUP BY users.id LIMIT 1';