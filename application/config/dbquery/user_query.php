<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['get_user_gid'] 					= 'SELECT group_id FROM users_groups where user_id = ?';