<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['get_user_infog'] 					= 'SELECT id, name, photo, created_on FROM users where id = ?';