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
$config['get_default_group_status']			= 'SELECT * FROM groups WHERE name = ?';
$config['get_group_by_gid']					= 'SELECT * FROM groups WHERE id = ?';
$config['get_group_by_name']				= 'SELECT * FROM groups WHERE name = ?';
$config['get_user_by_id']					= 'SELECT * FROM users WHERE id =? ORDER BY id LIMIT 1';
$config['get_user_by_remember_code']		= 'SELECT id, email, last_login FROM users WHERE email = ? and remember_code = ? ORDER BY id DESC LIMIT 1';
$config['get_user_by_email']				= 'SELECT email, id, password, active, last_login FROM users WHERE email = ? ORDER BY id DESC LIMIT 1';
$config['count_atempt_by_ip']				= 'SELECT 1 FROM login_attempts WHERE ip_address = ? AND login = ?';
$config['count_atempt_by_email']			= 'SELECT 1 FROM login_attempts WHERE login = ?';
$config['get_time_by_ip_email']				= 'SELECT MAX (time) FROM login_attempts WHERE ip_address = ? AND login = ?';
$config['get_time_by_email']				= 'SELECT MAX (time) FROM login_attempts WHERE login = ?';
$config['get_user_priv'] 					= 'SELECT * FROM users_groups LEFT JOIN groups ON users_groups.group_id = groups.id WHERE users_groups.user_id = ?';
$config['get_user_group'] 					= 'SELECT groups.name, groups.color FROM groups LEFT JOIN users_groups ON users_groups.group_id = groups.id WHERE users_groups.user_id = ?';


/*::: UPDATE :::*/
$config['update_user_lastlogin']			= 'UPDATE users SET last_login = ? WHERE id = ?';
$config['update_activation'] 				= 'UPDATE users SET activation_code = ?, active = ?  WHERE id = ?';
$config['clear_forgotten_password_code']	= 'UPDATE users SET forgotten_password_code = ?, forgotten_password_time = ? WHERE forgotten_password_code = ?';
$config['reset_pass_update']				= 'UPDATE users SET password = ?, remember_code = NULL, forgotten_password_code = NULL, forgotten_password_time = NULL WHERE email = ?';
$config['change_pass_rcode']				= 'UPDATE users SET password = ?, remember_code = ? WHERE email = ?';
$config['update_remember_code']				= 'UPDATE users SET remember_code = ? WHERE id = ?';
$config['set_forgotten_password_code']		= 'UPDATE users SET forgotten_password_code = ?, forgotten_password_time = ? WHERE email = ?';
$config['set_forgotten_password']			= 'UPDATE users SET password = ?, forgotten_password_code = NULL, active = 1 WHERE forgotten_password_code = ?';
$config['update_group']						= 'UPDATE groups SET name = ?, description = ?, config_priv = ?, projects_priv = ?, tasks_priv = ?, tickets_priv = ?, users_priv = ?, discussions_priv = ? WHERE id = ?)';


/*::: INSERT :::*/
$config['register_new_user']				= 'INSERT INTO users (email,name,phone,photo,password,salt,ip_address,created_on,active) VALUES (?,?,?,?,?,?,?,?,?)';
$config['add_to_group']						= 'INSERT INTO users_groups (user_id, group_id) VALUES (?,?)';
$config['create_new_group']					= 'INSERT INTO groups (name, description, config_priv, projects_priv, tasks_priv, tickets_priv, users_priv, discussions_priv) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
$config['increase_login_attempts']			= 'INSERT INTO login_attempts (ip_address, login, time) VALUES (?,?,?)';

/*::: DELETE :::*/
$config['delete_user_from_group']			= 'DELETE FROM users_groups WHERE user_id = ? AND group_id = ?';
$config['delete_all_group_frm_user']		= 'DELETE FROM users_groups WHERE user_id = ?';
$config['delete_all_user_frm_group']		= 'DELETE FROM users_groups WHERE group_id = ?';
$config['delete_group_by_gid']				= 'DELETE FROM groups WHERE id = ?';
$config['delete_login_attempt']				= 'DELETE FROM groups WHERE ip_address = ? AND login = ? OR time < ?';



