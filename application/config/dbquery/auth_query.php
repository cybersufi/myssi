<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*::: GET :::*/
$config['get_passnsalt']					= 'SELECT password, salt FROM users WHERE id = ? ORDER by id DESC LIMIT 1';
$contig['activate']							= 'SELECT email FROM users WHERE activation_code = ? AND id = ? ORDER BY id DESC LIMIT 1';
$config['get_by_forgotten_password_code']	= 'SELECT forgotten_password_code FROM users WHERE forgotten_password_code = ? ';
$config['get_id_pass_salt']					= 'SELECT id, password, salt FROM users WHERE email = ? ORDER BY id DESC LIMIT 1';
$config['get_username']						= 'SELECT username FROM users WHERE username = ? GROUP BY id ORDER BY id ASC LIMIT 1';
$config['get_email']						= 'SELECT email FROM users WHERE email = ? GROUP BY id ORDER BY id ASC LIMIT 1';
$config['get_forgotten_pass_time']			= 'SELECT forgotten_password_time FROM users WHERE forgotten_password_code = ?';
$config['get_default_group_status']			= 'SELECT * FROM users_groups WHERE name = ?';


/*::: UPDATE :::*/
$config['update_activation'] 				= 'UPDATE users SET activation_code = ?, active = ?  WHERE id = ?';
$config['clear_forgotten_password_code']	= 'UPDATE users SET forgotten_password_code = ?, forgotten_password_time = ? WHERE forgotten_password_code = ?';
$config['reset_pass_update']				= 'UPDATE users SET password = ?, remember_code = NULL, forgotten_password_code = NULL, forgotten_password_time = NULL WHERE email = ?';
$config['change_pass_rcode']				= 'UPDATE users SET password = ?, remember_code = ? WHERE email = ?';
$config['set_forgotten_password_code']		= 'UPDATE users SET forgotten_password_code = ?, forgotten_password_time = ? WHERE email = ?';
$config['set_forgotten_password']			= 'UPDATE users SET password = ?, forgotten_password_code = NULL, active = 1 WHERE forgotten_password_code = ?';


/*::: INSERT :::*/
$config['register_new_user']				= 'INSERT INTO users (email,name,phone,photo,password,salt,ip_address,created_on,active) VALUES (?,?,?,?,?,?,?,?,?)';
$config['add_to_group']						= 'INSERT INTO users_groups (user_id, group_id) VALUES (?,?)';


/*::: DELETE :::*/





