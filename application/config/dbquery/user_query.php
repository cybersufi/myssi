<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*select*/
$config['get_user_gid'] 					= 'SELECT * FROM users_groups WHERE users_groups.user_id = ? INNER JOIN groups ON users_groups.group_id = groups.id';
$config['get_group_priv_by_id'] 			= 'SELECT * FROM groups WHERE id = ?';
$config['get_all_users']					= 'SELECT * FROM users ORDER BY id';
$config['get_user_color']					= 'SELECT groups.color FROM groups JOIN users_groups ON users_groups.group_id = groups.id JOIN users ON users.id = users_groups.user_id WHERE users.id = ? GROUP BY users.id LIMIT 1';
$config['get_user_by_id']					= 'SELECT * FROM users WHERE id =? ORDER BY id LIMIT 1';
$config['get_user_salt_by_id']				= 'SELECT users.salt FROM users where id=? ORDER BY id LIMIT 1';
$config['get_userid_by_email']				= 'SELECT id FROM users where email = ?';


/*update*/
$config['update_user_without_photo']		= 'UPDATE users SET name=?, email=?, phone=? WHERE id=?';
$config['update_user_with_photo']			= 'UPDATE users SET name=?, email=?, phone=?, photo=? WHERE id=?';



