<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model
{
	/**
	 * Holds an array of tables used
	 *
	 * @var array
	 **/
	public $tables = array();

	/**
	 * activation code
	 *
	 * @var string
	 **/
	public $activation_code;

	/**
	 * forgotten password key
	 *
	 * @var string
	 **/
	public $forgotten_password_code;

	/**
	 * new password
	 *
	 * @var string
	 **/
	public $new_password;

	/**
	 * Identity
	 *
	 * @var string
	 **/
	public $identity;

	/**
	 * Response
	 *
	 * @var string
	 **/
	protected $response = NULL;

	/**
	 * message (uses lang file)
	 *
	 * @var string
	 **/
	protected $messages;

	/**
	 * error message (uses lang file)
	 *
	 * @var string
	 **/
	protected $errors;

	/**
	 * error start delimiter
	 *
	 * @var string
	 **/
	protected $error_start_delimiter;

	/**
	 * error end delimiter
	 *
	 * @var string
	 **/
	protected $error_end_delimiter;

	/**
	 * caching of users and their groups
	 *
	 * @var array
	 **/
	public $_cache_user_in_group = array();

	/**
	 * caching of groups
	 *
	 * @var array
	 **/
	protected $_cache_groups = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->config('auth', TRUE);
		$this->load->config('dbquery/auth_query', TRUE);
		$this->load->helper('cookie');
		$this->load->helper('date');
		//$this->lang->load('ion_auth');

		$this->sql = $this->config->item('dbquery/auth_query');

		// initialize db tables data
		//$this->tables  = $this->config->item('tables', 'ion_auth');

		//initialize data
		$this->identity_column = $this->config->item('identity', 'auth');
		$this->store_salt      = $this->config->item('store_salt', 'auth');
		$this->salt_length     = $this->config->item('salt_length', 'auth');
		//$this->join			   = $this->config->item('join', 'ion_auth');


		// initialize hash method options (Bcrypt)
		$this->hash_method = $this->config->item('hash_method', 'auth');
		$this->default_rounds = $this->config->item('default_rounds', 'auth');
		$this->random_rounds = $this->config->item('random_rounds', 'auth');
		$this->min_rounds = $this->config->item('min_rounds', 'auth');
		$this->max_rounds = $this->config->item('max_rounds', 'auth');


		// initialize messages and error
		$this->messages    = array();
		$this->errors      = array();
		$delimiters_source = $this->config->item('delimiters_source', 'auth');

		// load the error delimeters either from the config file or use what's been supplied to form validation
		if ($delimiters_source === 'form_validation')
		{
			// load in delimiters from form_validation
			// to keep this simple we'll load the value using reflection since these properties are protected
			$this->load->library('form_validation');
			$form_validation_class = new ReflectionClass("CI_Form_validation");

			$error_prefix = $form_validation_class->getProperty("_error_prefix");
			$error_prefix->setAccessible(TRUE);
			$this->error_start_delimiter = $error_prefix->getValue($this->form_validation);
			$this->message_start_delimiter = $this->error_start_delimiter;

			$error_suffix = $form_validation_class->getProperty("_error_suffix");
			$error_suffix->setAccessible(TRUE);
			$this->error_end_delimiter = $error_suffix->getValue($this->form_validation);
			$this->message_end_delimiter = $this->error_end_delimiter;
		}
		else
		{
			// use delimiters from config
			$this->message_start_delimiter = $this->config->item('message_start_delimiter', 'auth');
			$this->message_end_delimiter   = $this->config->item('message_end_delimiter', 'auth');
			$this->error_start_delimiter   = $this->config->item('error_start_delimiter', 'auth');
			$this->error_end_delimiter     = $this->config->item('error_end_delimiter', 'auth');
		}


		// initialize our hooks object
		//$this->_ion_hooks = new stdClass;

		// load the bcrypt class if needed
		if ($this->hash_method == 'bcrypt') {
			if ($this->random_rounds)
			{
				$rand = rand($this->min_rounds,$this->max_rounds);
				$params = array('rounds' => $rand);
			}
			else
			{
				$params = array('rounds' => $this->default_rounds);
			}

			$params['salt_prefix'] = $this->config->item('salt_prefix', 'auth');
			$this->load->library('bcrypt',$params);
		}

		//$this->trigger_events('model_constructor');
	}

	/**
	 * Hashes the password to be stored in the database.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password($password, $salt=false, $use_sha1_override=FALSE)
	{
		if (empty($password))
		{
			return FALSE;
		}

		// bcrypt
		if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
		{
			return $this->bcrypt->hash($password);
		}


		if ($this->store_salt && $salt)
		{
			return  sha1($password . $salt);
		}
		else
		{
			$salt = $this->salt();
			return  $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
		}
	}

	/**
	 * This function takes a password and validates it
	 * against an entry in the users table.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_password_db($id, $password, $use_sha1_override=FALSE)
	{
		if (empty($id) || empty($password))
		{
			return FALSE;
		}

		$query = $this->db->query($this->sql['get_passnsalt'], array($id));

		$hash_password_db = $query->row();

		if ($query->num_rows() !== 1)
		{
			return FALSE;
		}

		// bcrypt
		if ($use_sha1_override === FALSE && $this->hash_method == 'bcrypt')
		{
			if ($this->bcrypt->verify($password,$hash_password_db->password))
			{
				return TRUE;
			}

			return FALSE;
		}

		// sha1
		if ($this->store_salt)
		{
			$db_password = sha1($password . $hash_password_db->salt);
		}
		else
		{
			$salt = substr($hash_password_db->password, 0, $this->salt_length);

			$db_password =  $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
		}

		if($db_password == $hash_password_db->password)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Generates a random salt value for forgotten passwords or any other keys. Uses SHA1.
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function hash_code($password)
	{
		return $this->hash_password($password, FALSE, TRUE);
	}

	/**
	 * Generates a random salt value.
	 *
	 * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
	 *
	 * @return void
	 * @author Anthony Ferrera
	 **/
	public function salt()
	{

		$raw_salt_len = 16;

 		$buffer = '';
        $buffer_valid = false;

        if (function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && @is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len) {
                $buffer .= fread($f, $raw_salt_len - $read);
                $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $raw_salt_len) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
                if ($i < $bl) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }

        $salt = $buffer;

        // encode string with the Base64 variant used by crypt
        $base64_digits   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string   = base64_encode($salt);
        $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

	    $salt = substr($salt, 0, $this->salt_length);


		return $salt;

	}

	/**
	 * Activation functions
	 *
	 * Activate : Validates and removes activation code.
	 * Deactivae : Updates a users row with an activation code.
	 *
	 * @author Mathew
	 */

	/**
	 * activate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function activate($id, $code = false)
	{
		if ($code !== FALSE)
		{
			$query = $this->db->query($this->sql['activate'], array($code, $id));
			$result = $query->row();

			if ($query->num_rows() !== 1)
			{
				$this->set_error('Unable to Activate Account');
				return FALSE;
			}

			$data = array(
			    'activation_code' => NULL,
			    'active'          => 1
			);

			$this->db->query($this->sql['update_activation'], array(NULL, 1, $id));
		}
		else
		{
			$data = array(
			    'activation_code' => NULL,
			    'active'          => 1
			);

			$this->db->query($this->sql['update_activation'], array(NULL, 1, $id));
		}

		$return = $this->db->num_rows() == 1;
		if ($return)
		{
			$this->set_message('Account Activated');
		}
		else
		{
			$this->set_error('Unable to Activate Account');
		}

		return $return;
	}


	/**
	 * Deactivate
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function deactivate($id = NULL)
	{
		if (!isset($id))
		{
			$this->set_error('Unable to De-Activate Account');
			return FALSE;
		}

		$activation_code       = sha1(md5(microtime()));
		$this->activation_code = $activation_code;

		$data = array(
		    'activation_code' => $activation_code,
		    'active'          => 0
		);

		$this->db->query($this->sql['update_activation'], array($activation_code, 0, $id));

		$return = $this->db->num_rows() == 1;
		if ($return)
			$this->set_message('Account De-Activated');
		else
			$this->set_error('Unable to De-Activate Account');

		return $return;
	}

	public function clear_forgotten_password_code($code) {

		if (empty($code))
		{
			return FALSE;
		}

		$this->db->query($this->sql['get_by_forgotten_password_code'], array($code));
		
		if ($this->db->num_rows() > 0)
		{
			$data = array(
			    'forgotten_password_code' => NULL,
			    'forgotten_password_time' => NULL
			);
			$this->db->query($this->sql['clear_forgotten_password_code'], array(NULL, NULL, $code));
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * reset password
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function reset_password($identity, $new) {
		if (!$this->identity_check($identity)) {
			return FALSE;
		}

		//$this->trigger_events('extra_where');

		$query = $this->db->query($this->sql['get_id_pass_salt'], array($identity));

		if ($query->num_rows() !== 1)
		{
			//$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
			$this->set_error('Unable to Change Password');
			return FALSE;
		}

		$result = $query->row();

		$new = $this->hash_password($new, $result->salt);

		// store the new password and reset the remember code so all remembered instances have to re-login
		// also clear the forgotten password code
		$this->db->query($this->sql['reset_pass_update'], array($new, $identity));

		$return = $this->db->num_rows() == 1;
		if ($return)
		{
			//$this->trigger_events(array('post_change_password', 'post_change_password_successful'));
			$this->set_message('Password Successfully Changed');
		}
		else
		{
			//$this->trigger_events(array('post_change_password', 'post_change_password_unsuccessful'));
			$this->set_error('Unable to Change Password');
		}

		return $return;
	}

	/**
	 * change password
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function change_password($identity, $old, $new)
	{
		$query = $this->db->query($this->sql['get_id_pass_salt'], array($identity));
		if ($query->num_rows() !== 1)
		{
			$this->set_error('Unable to Change Password');
			return FALSE;
		}

		$user = $query->row();
		$old_password_matches = $this->hash_password_db($user->id, $old);

		if ($old_password_matches === TRUE)
		{
			// store the new password and reset the remember code so all remembered instances have to re-login
			$hashed_new_password  = $this->hash_password($new, $user->salt);
			$successfully_changed_password_in_db = $this->db->query($this->sql['reset_pass_update'], array($hashed_new_password, $identity));
			if ($successfully_changed_password_in_db)
			{
				$this->set_message('Password Successfully Changed');
			}
			else
			{
				$this->set_error('Unable to Change Password');
			}

			return $successfully_changed_password_in_db;
		}

		$this->set_error('Unable to Change Password');
		return FALSE;
	}

	/**
	 * Checks username
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function username_check($username = '')
	{
		$this->trigger_events('username_check');

		if (empty($username))
		{
			return FALSE;
		}

		$query = $this->db->query($this->sql['get_username'], array($username));
		return $query->num_rows() > 0;
	}

	/**
	 * Checks email
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function email_check($email = '')
	{
		if (empty($email))
		{
			return FALSE;
		}

		$query = $this->db->query($this->sql['get_email'], array($email));
		return $query->num_rows() > 0;
	}

	/**
	 * Insert a forgotten password key.
	 *
	 * @return bool
	 * @author Mathew
	 * @updated Ryan
	 * @updated 52aa456eef8b60ad6754b31fbdcc77bb
	 **/
	public function forgotten_password($identity)
	{
		if (empty($identity))
		{
			return FALSE;
		}

		// All some more randomness
		$activation_code_part = "";
		if(function_exists("openssl_random_pseudo_bytes")) {
			$activation_code_part = openssl_random_pseudo_bytes(128);
		}

		for($i=0;$i<1024;$i++) {
			$activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
		}

		$key = $this->hash_code($activation_code_part.$identity);

		// If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
		if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == FALSE)
		{
			// preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
			// compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
			if ( ! preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-'))."]+$|i", $key))
			{
				$key = preg_replace("/[^".$this->config->item('permitted_uri_chars')."]+/i", "-", $key);
			}
		}

		$this->forgotten_password_code = $key;
		$this->db->query($this->sql['set_forgotten_password_code'], array($key, time(), $identity));

		$return = $this->db->num_rows() == 1;
		return $return;
	}

	/**
	 * Forgotten Password Complete
	 *
	 * @return string
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code, $salt=FALSE)
	{
		if (empty($code))
		{
			return FALSE;
		}

		$profile = $this->db->query($this->sql['get_forgotten_pass_time'], array($code))->row(); //pass the code to profile

		if ($profile) {

			if ($this->config->item('forgot_password_expiration', 'auth') > 0) {
				//Make sure it isn't expired
				$expiration = $this->config->item('forgot_password_expiration', 'auth');
				if (time() - $profile->forgotten_password_time > $expiration) {
					//it has expired
					$this->set_error('forgot_password_expired');
					return FALSE;
				}
			}

			$password = $this->salt();
			$savepass = $this->hash_password($password, $salt);

			$this->db->query($this->sql['set_forgotten_password'], array($savepass, $code));
			return $password;
		}
		return FALSE;
	}

	/**
	 * register
	 *
	 * @return bool
	 * @author Mathew
	 **/
	//public function register($identity, $password, $email, $additional_data = array(), $groups = array())
	public function register($email, $name, $password, $phone, $photo, $groups = array())
	{
		$manual_activation = $this->config->item('manual_activation', ' auth');

		if ($this->email_check($email))
		{
			$this->set_error('Identity Already Used or Invalid');
			return FALSE;
		}
		elseif ( !$this->config->item('default_group', 'auth') && empty($groups) )
		{
			$this->set_error('Default group is not set');
			return FALSE;
		}

		// check if the default set in config exists in database
		$query = $this->db->query($this->sql['get_default_group_status'], array($this->config->item('default_group', 'auth')))->row();
		if( !isset($query->id) && empty($groups) )
		{
			$this->set_error('Invalid default group name set');
			return FALSE;
		}

		// capture default group details
		$default_group = $query;

		// IP Address
		$ip_address = $this->_prepare_ip($this->input->ip_address());
		$salt       = $this->store_salt ? $this->salt() : FALSE;
		$password   = $this->hash_password($password, $salt);
		$active 	= ($manual_activation) ? 0 : 1;

		//(email,name,phone,photo,password,salt,ip_address,created_on,active)
		$data = array($email, $name, $phone, $photo, $password, $salt, $ip_address, time(), $active);
		// Users table.
		$this->db->query($this->sql['register_new_user'], $data);
		$id = $this->db->insert_id();

		// add in groups array if it doesn't exits and stop adding into default group if default group ids are set
		if( isset($default_group->id) && empty($groups) )
		{
			$groups[] = $default_group->id;
		}

		if (!empty($groups))
		{
			// add to groups
			foreach ($groups as $group)
			{
				$this->add_to_group($group, $id);
			}
		}

		return (isset($id)) ? $id : FALSE;
	}

	/**
	 * login
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function login($identity, $password, $remember=FALSE)
	{
		if (empty($identity) || empty($password))
		{
			$this->set_error('Incorrect Login');
			return FALSE;
		}

		$query = $this->db->query($this->sql['get_user_by_email'], array($identity));

		if($this->is_time_locked_out($identity))
		{
			// Hash something anyway, just to take up time
			$this->hash_password($password);
			$this->set_error('Temporarily Locked Out.  Try again later.');
			return FALSE;
		}

		if ($query->num_rows() === 1)
		{
			$user = $query->row();
			$password = $this->hash_password_db($user->id, $password);

			if ($password === TRUE)
			{
				if ($user->active == 0)
				{
					$this->set_error('Account is inactive');
					return FALSE;
				}

				$this->set_session($user);

				$this->update_last_login($user->id);

				$this->clear_login_attempts($identity);

				if ($remember && $this->config->item('remember_users', 'auth'))
				{
					$this->remember_user($user->id);
				}

				$this->set_message('Logged In Successfully');

				return TRUE;
			}
		}

		// Hash something anyway, just to take up time
		$this->hash_password($password);
		$this->increase_login_attempts($identity);
		$this->set_error('Incorrect Login');
		return FALSE;
	}

	/**
	 * is_max_login_attempts_exceeded
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity
	 * @return boolean
	 **/
	public function is_max_login_attempts_exceeded($identity) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$max_attempts = $this->config->item('maximum_login_attempts', 'auth');
			if ($max_attempts > 0) {
				$attempts = $this->get_attempts_num($identity);
				return $attempts >= $max_attempts;
			}
		}
		return FALSE;
	}

	/**
	 * Get number of attempts to login occured from given IP-address or identity
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param	string $identity
	 * @return	int
	 */
	function get_attempts_num($identity)
	{
        if ($this->config->item('track_login_attempts', 'auth')) {
            $ip_address = $this->_prepare_ip($this->input->ip_address());
            $this->db->select('1', FALSE);
            if ($this->config->item('track_login_ip_address', 'auth')) {
            	$this->db->query($this->sql['count_atempt_by_ip'], array($ip_address, $identity));
            } 
            else if (strlen($identity) > 0) 
            {
            	$this->db->query($this->sql['count_atempt_by_ip'], array($identity));
			}

            $qres = $this->db->get();
            return $qres->num_rows();
        }
        return 0;
	}
	/**
	 * Get a boolean to determine if an account should be locked out due to
	 * exceeded login attempts within a given period
	 *
	 * @return	boolean
	 */
	public function is_time_locked_out($identity) {

		return $this->is_max_login_attempts_exceeded($identity) && $this->get_last_attempt_time($identity) > time() - $this->config->item('lockout_time', 'auth');
	}

	/**
	 * Get the time of the last time a login attempt occured from given IP-address or identity
	 *
	 * @param	string $identity
	 * @return	int
	 */
	public function get_last_attempt_time($identity) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$ip_address = $this->_prepare_ip($this->input->ip_address());

			$this->db->select_max('time');
            if ($this->config->item('track_login_ip_address', 'auth')) 
            {
            	$qres = $this->db->query($this->sql['get_time_by_ip_email'], array($ip_address, $identity));
            }
			else if (strlen($identity) > 0) 
			{
				$qres = $this->db->query($this->sql['get_time_by_email'], array($identity));
			}

			if($qres->num_rows() > 0) {
				return $qres->row()->time;
			}
		}
		return 0;
	}

	/**
	 * increase_login_attempts
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity
	 **/
	public function increase_login_attempts($identity) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$ip_address = $this->_prepare_ip($this->input->ip_address());
			return $this->db->query($this->sql['increase_login_attempts'], array($ip_address, $identity, time()));
		}
		return FALSE;
	}

	/**
	 * clear_login_attempts
	 * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
	 *
	 * @param string $identity
	 **/
	public function clear_login_attempts($identity, $expire_period = 86400) {
		if ($this->config->item('track_login_attempts', 'auth')) {
			$ip_address = $this->_prepare_ip($this->input->ip_address());
			return $this->db->query($this->sql['delete_login_attempt'], array($ip_address, $identity, (time() - $expire_period)));
		}
		return FALSE;
	}

	/**
	 * users
	 *
	 * @return object Users
	 * @author Ben Edmunds
	 **/
	public function users($groups = NULL)
	{
		if (isset($this->_ion_select) && !empty($this->_ion_select))
		{
			foreach ($this->_ion_select as $select)
			{
				$this->db->select($select);
			}

			$this->_ion_select = array();
		}
		else
		{
			//default selects
			$this->db->select(array(
			    $this->tables['users'].'.*',
			    $this->tables['users'].'.id as id',
			    $this->tables['users'].'.id as user_id'
			));
		}

		// filter by group id(s) if passed
		if (isset($groups))
		{
			// build an array if only one group was passed
			if (!is_array($groups))
			{
				$groups = Array($groups);
			}

			// join and then run a where_in against the group ids
			if (isset($groups) && !empty($groups))
			{
				$this->db->distinct();
				$this->db->join(
				    $this->tables['users_groups'],
				    $this->tables['users_groups'].'.'.$this->join['users'].'='.$this->tables['users'].'.id',
				    'inner'
				);
			}

			// verify if group name or group id was used and create and put elements in different arrays
			$group_ids = array();
			$group_names = array();
			foreach($groups as $group)
			{
				if(is_numeric($group)) $group_ids[] = $group;
				else $group_names[] = $group;
			}
			$or_where_in = (!empty($group_ids) && !empty($group_names)) ? 'or_where_in' : 'where_in';
			// if group name was used we do one more join with groups
			if(!empty($group_names))
			{
				$this->db->join($this->tables['groups'], $this->tables['users_groups'] . '.' . $this->join['groups'] . ' = ' . $this->tables['groups'] . '.id', 'inner');
				$this->db->where_in($this->tables['groups'] . '.name', $group_names);
			}
			if(!empty($group_ids))
			{
				$this->db->{$or_where_in}($this->tables['users_groups'].'.'.$this->join['groups'], $group_ids);
			}
		}

		$this->trigger_events('extra_where');

		// run each where that was passed
		if (isset($this->_ion_where) && !empty($this->_ion_where))
		{
			foreach ($this->_ion_where as $where)
			{
				$this->db->where($where);
			}

			$this->_ion_where = array();
		}

		if (isset($this->_ion_like) && !empty($this->_ion_like))
		{
			foreach ($this->_ion_like as $like)
			{
				$this->db->or_like($like);
			}

			$this->_ion_like = array();
		}

		if (isset($this->_ion_limit) && isset($this->_ion_offset))
		{
			$this->db->limit($this->_ion_limit, $this->_ion_offset);

			$this->_ion_limit  = NULL;
			$this->_ion_offset = NULL;
		}
		else if (isset($this->_ion_limit))
		{
			$this->db->limit($this->_ion_limit);

			$this->_ion_limit  = NULL;
		}

		// set the order
		if (isset($this->_ion_order_by) && isset($this->_ion_order))
		{
			$this->db->order_by($this->_ion_order_by, $this->_ion_order);

			$this->_ion_order    = NULL;
			$this->_ion_order_by = NULL;
		}

		$this->response = $this->db->get($this->tables['users']);

		return $this;
	}

	/**
	 * user
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function user($id = NULL)
	{
		// if no id was passed use the current users id
		$id || $id = $this->session->userdata('user_id');

		$this->limit(1);
		$this->order_by($this->tables['users'].'.id', 'desc');
		$this->where($this->tables['users'].'.id', $id);

		$this->users();

		return $this;
	}

	/**
	 * get_users_groups
	 *
	 * @return array
	 * @author Ben Edmunds
	 **/
	public function get_users_groups($id=FALSE)
	{
		$this->trigger_events('get_users_group');

		// if no id was passed use the current users id
		$id || $id = $this->session->userdata('user_id');

		//return $this->db->select($this->tables['users_groups'].'.'.$this->join['groups'].' as id, '.$this->tables['groups'].'.name, '.$this->tables['groups'].'.description')
        /* ADD => , '.$this->tables['groups'].'.bgcolor' */
		return $this->db->select($this->tables['users_groups'].'.'.$this->join['groups'].' as id, '.$this->tables['groups'].'.name, '.$this->tables['groups'].'.description, '.$this->tables['groups'].'.bgcolor')
		                ->where($this->tables['users_groups'].'.'.$this->join['users'], $id)
		                ->join($this->tables['groups'], $this->tables['users_groups'].'.'.$this->join['groups'].'='.$this->tables['groups'].'.id')
		                ->get($this->tables['users_groups']);
	}

	/**
	 * add_to_group
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function add_to_group($group_ids, $user_id=false)
	{
		// if no id was passed use the current users id
		$user_id || $user_id = $this->session->userdata('user_id');

		if(!is_array($group_ids))
		{
			$group_ids = array($group_ids);
		}

		$return = 0;

		// Then insert each into the database
		foreach ($group_ids as $group_id)
		{
			//if ($this->db->insert($this->tables['users_groups'], array( $this->join['groups'] => (float)$group_id, $this->join['users'] => (float)$user_id)))
			if ($this->db->query($this->sql['add_to_group'], array( (float)$group_id, (float)$user_id)))
			{
				if (isset($this->_cache_groups[$group_id])) {
					$group_name = $this->_cache_groups[$group_id];
				}
				else {
					$group = $this->db->query($this->sql['get_group_by_gid'], array($group_id))->result();
					$group_name = $group[0]->name;
					$this->_cache_groups[$group_id] = $group_name;
				}
				$this->_cache_user_in_group[$user_id][$group_id] = $group_name;

				// Return the number of groups added
				$return += 1;
			}
		}

		return $return;
	}

	/**
	 * remove_from_group
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function remove_from_group($group_ids=false, $user_id=false)
	{
		// user id is required
		if(empty($user_id))
		{
			return FALSE;
		}

		// if group id(s) are passed remove user from the group(s)
		if( ! empty($group_ids))
		{
			if(!is_array($group_ids))
			{
				$group_ids = array($group_ids);
			}

			foreach($group_ids as $group_id)
			{
				$this->db->query($this->sql['delete_user_from_group'], array( (float)$group_id, (float)$user_id));
				if (isset($this->_cache_user_in_group[$user_id]) && isset($this->_cache_user_in_group[$user_id][$group_id]))
				{
					unset($this->_cache_user_in_group[$user_id][$group_id]);
				}
			}

			$return = TRUE;
		}
		// otherwise remove user from all groups
		else
		{
			if ($return = $this->db->query($this->sql['delete_all_group_frm_user'], array((float)$user_id))) {
				$this->_cache_user_in_group[$user_id] = array();
			}
		}
		return $return;
	}

	/**
	 * update
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function update($id, array $data)
	{
		$user = $this->db->query($this->sql['get_user_by_id'], array((float)$id))->row();
		
		$this->db->trans_begin();

		if (array_key_exists('email', $data) && $this->email_check($data['email']) && $user->email !== $data['email'])
		{
			$this->db->trans_rollback();
			$this->set_error('account_creation_duplicate_identity');
			$this->set_error('update_unsuccessful');
			return FALSE;
		}

		// Filter the data passed
		$data = $this->_filter_data($this->tables['users'], $data);

		if (array_key_exists('email', $data) || array_key_exists('password', $data))
		{
			if (array_key_exists('password', $data))
			{
				if( ! empty($data['password']))
				{
					$data['password'] = $this->hash_password($data['password'], $user->salt);
				}
				else
				{
					// unset password so it doesn't effect database entry if no password passed
					unset($data['password']);
				}
			}
		}

		$this->trigger_events('extra_where');
		$this->db->update($this->tables['users'], $data, array('id' => $user->id));

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();

			$this->trigger_events(array('post_update_user', 'post_update_user_unsuccessful'));
			$this->set_error('update_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();

		$this->trigger_events(array('post_update_user', 'post_update_user_successful'));
		$this->set_message('update_successful');
		return TRUE;
	}

	/**
	* delete_user
	*
	* @return bool
	* @author Phil Sturgeon
	**/
	public function delete_user($id)
	{
		$this->trigger_events('pre_delete_user');

		$this->db->trans_begin();

		// remove user from groups
		$this->remove_from_group(NULL, $id);

		// delete user from users table should be placed after remove from group
		$this->db->delete($this->tables['users'], array('id' => $id));

		// if user does not exist in database then it returns FALSE else removes the user from groups
		if ($this->db->num_rows() == 0)
		{
		    return FALSE;
		}

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->trigger_events(array('post_delete_user', 'post_delete_user_unsuccessful'));
			$this->set_error('delete_unsuccessful');
			return FALSE;
		}

		$this->db->trans_commit();

		$this->trigger_events(array('post_delete_user', 'post_delete_user_successful'));
		$this->set_message('delete_successful');
		return TRUE;
	}

	/**
	 * update_last_login
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function update_last_login($id)
	{
		$this->load->helper('date');
		return $this->db->query($this->sql['update_user_lastlogin'], array(time(), $id));
	}

	/**
	 * set_lang
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function set_lang($lang = 'en')
	{
		$this->trigger_events('set_lang');

		// if the user_expire is set to zero we'll set the expiration two years from now.
		if($this->config->item('user_expire', 'ion_auth') === 0)
		{
			$expire = (60*60*24*365*2);
		}
		// otherwise use what is set
		else
		{
			$expire = $this->config->item('user_expire', 'ion_auth');
		}

		set_cookie(array(
			'name'   => 'lang_code',
			'value'  => $lang,
			'expire' => $expire
		));

		return TRUE;
	}

	/**
	 * set_session
	 *
	 * @return bool
	 * @author jrmadsen67
	 **/
	public function set_session($user)
	{

		$session_data = array(
		    'identity'             => $user->email,
		    'email'                => $user->email,
		    'user_id'              => $user->id, //everyone likes to overwrite id so we'll use user_id
		    'old_last_login'       => $user->last_login
		);

		$this->session->set_userdata($session_data);
		return TRUE;
	}

	/**
	 * remember_user
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function remember_user($id)
	{
		if (!$id)
		{
			return FALSE;
		}

		$user = $this->db->query($this->sql['get_user_by_id'], array($id))->row();

		$salt = $this->salt();

		$this->db->query($this->sql['update_remember_code'], array($salt, $id));

		if ($this->db->num_rows() > -1)
		{
			// if the user_expire is set to zero we'll set the expiration two years from now.
			if($this->config->item('user_expire', 'auth') === 0)
			{
				$expire = (60*60*24*365*2);
			}
			// otherwise use what is set
			else
			{
				$expire = $this->config->item('user_expire', 'auth');
			}

			set_cookie(array(
			    'name'   => $this->config->item('identity_cookie_name', 'auth'),
			    'value'  => $user->email,
			    'expire' => $expire
			));

			set_cookie(array(
			    'name'   => $this->config->item('remember_cookie_name', 'auth'),
			    'value'  => $salt,
			    'expire' => $expire
			));
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * login_remembed_user
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function login_remembered_user()
	{
		// check for valid data
		if (!get_cookie($this->config->item('identity_cookie_name', 'auth'))
			|| !get_cookie($this->config->item('remember_cookie_name', 'ion_auth'))
			|| !$this->identity_check(get_cookie($this->config->item('identity_cookie_name', 'auth'))))
		{
			return FALSE;
		}

		// get the user
		$email = get_cookie($this->config->item('identity_cookie_name', 'ion_auth'));
		$rem_code = get_cookie($this->config->item('remember_cookie_name', 'ion_auth'));
		$query = $this->db->query($this->sql['get_user_by_remember_code'], array($email, $rem_code));

		// if the user was found, sign them in
		if ($query->num_rows() == 1)
		{
			$user = $query->row();
			$this->update_last_login($user->id);
			$this->set_session($user);
			// extend the users cookies if the option is enabled
			if ($this->config->item('user_extend_on_login', 'auth'))
			{
				$this->remember_user($user->id);
			}
			return TRUE;
		}
		return FALSE;
	}


	/**
	 * create_group
	 *
	 * @author aditya menon
	*/
	public function create_group($group_name = FALSE, $group_description = '', $additional_data = array())
	{
		// bail if the group name was not passed
		if(!$group_name)
		{
			$this->set_error('Group name is a required field');
			return FALSE;
		}

		// bail if the group name already exists
		$existing_group = $this->db->query($this->sql['get_group_by_name'], array($group_name))->num_rows();
		if($existing_group !== 0)
		{
			$this->set_error('Group name already taken');
			return FALSE;
		}

		// filter out any data passed that doesnt have a matching column in the groups table
		// and merge the set group data and the additional data
		/*groups (name, description, allow_view_all, allow_manage_projects, allow_manage_tasks, allow_manage_tickets, 
		allow_manage_users, allow_manage_configuration, allow_manage_tasks_viewonly, allow_manage_discussions, allow_manage_discussion_viewonly) */
		//if (!empty($additional_data)) $data = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);
		$data_ins = array($group_name, $group_description);
		if (!empty($additional_data)) {
			array_push($data_ins, $additional_data);
		} else {
			array_push($data_ins, array(0,0,0,0,0,0,0,0));
		}

		// insert the new group
		$this->db->query($this->sql['create_new_group'], $data_ins);
		$group_id = $this->db->insert_id();

		// report success
		$this->set_message('Group created Successfully');
		// return the brand new group id
		return $group_id;
	}

	/**
	 * update_group
	 *
	 * @return bool
	 * @author aditya menon
	 **/
	public function update_group($group_id = FALSE, $group_name = FALSE, $group_description = FALSE, $additional_data = array())
	{
		if (empty($group_id)) return FALSE;

		$data = array();

		if (!empty($group_name))
		{
			// we are changing the name, so do some checks

			// bail if the group name already exists
			$existing_group = $this->db->query($this->sql['get_group_by_name'], array($group_name))->row();
			if(isset($existing_group->id) && $existing_group->id != $group_id)
			{
				$this->set_error('Group name already taken');
				return FALSE;
			}

			$data['name'] = $group_name;
		}

		// restrict change of name of the admin group
        $group = $this->db->query($this->sql['get_group_by_gid'], array($group_id))->row();
        if($this->config->item('admin_group', 'auth') === $group->name && $group_name !== $group->name)
        {
            $this->set_error('Admin group name can not be changed');
            return FALSE;
        }


		// IMPORTANT!! Third parameter was string type $description; this following code is to maintain backward compatibility
		// New projects should work with 3rd param as array
		//if (is_string($additional_data)) $additional_data = array('description' => $additional_data);

		$data_update = array($group_name, $group_description);
		if (!empty($additional_data)) {
			array_push($data_ins, $additional_data);
		} else {
			array_push($data_ins, array(0,0,0,0,0,0,0,0));
		}
		array_push($data_update, $group_id);

		// filter out any data passed that doesnt have a matching column in the groups table
		// and merge the set group data and the additional data
		//if (!empty($additional_data)) $data = array_merge($this->_filter_data($this->tables['groups'], $additional_data), $data);
		//$this->db->update($this->tables['groups'], $data, array('id' => $group_id));
		$this->db->query($this->sql['update_group'], $data_update);
		$this->set_message('Group details updated');
		return TRUE;
	}

	/**
	* delete_group
	*
	* @return bool
	* @author aditya menon
	**/
	public function delete_group($group_id = FALSE)
	{
		// bail if mandatory param not set
		if(!$group_id || empty($group_id))
		{
			return FALSE;
		}
		$group = $this->db->query($this->sql['get_group_by_gid'], array($group_id))->row();
		if($group->name == $this->config->item('admin_group', 'auth'))
		{
			$this->set_error('Can\'t delete the administrators\' group');
			return FALSE;
		}

		$this->db->trans_begin();

		// remove all users from this group
		$this->db->query($this->sql['delete_all_user_frm_group'], array($group_id));
		// remove the group itself
		$this->db->query($this->sql['delete_group_by_gid'], array($group_id));

		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			$this->set_error('Unable to delete group');
			return FALSE;
		}

		$this->db->trans_commit();

		$this->set_message('Group deleted');
		return TRUE;
	}

	/**
	 * set_message_delimiters
	 *
	 * Set the message delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message_delimiters($start_delimiter, $end_delimiter)
	{
		$this->message_start_delimiter = $start_delimiter;
		$this->message_end_delimiter   = $end_delimiter;

		return TRUE;
	}

	/**
	 * set_error_delimiters
	 *
	 * Set the error delimiters
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error_delimiters($start_delimiter, $end_delimiter)
	{
		$this->error_start_delimiter = $start_delimiter;
		$this->error_end_delimiter   = $end_delimiter;

		return TRUE;
	}

	/**
	 * set_message
	 *
	 * Set a message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message($message)
	{
		$this->messages[] = $message;
		return $message;
	}



	/**
	 * messages
	 *
	 * Get the messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function messages()
	{
		$_output = '';
		foreach ($this->messages as $message)
		{
			$messageLang = '##' . $message . '##';
			$_output .= $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
		}

		return $_output;
	}

	/**
	 * messages as array
	 *
	 * Get the messages as an array
	 *
	 * @return array
	 * @author Raul Baldner Junior
	 **/
	public function messages_array()
	{
		return $this->messages;
	}


	/**
	 * clear_messages
	 *
	 * Clear messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function clear_messages()
	{
		$this->messages = array();

		return TRUE;
	}


	/**
	 * set_error
	 *
	 * Set an error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error($error)
	{
		$this->errors[] = $error;
		return $error;
	}

	/**
	 * errors
	 *
	 * Get the error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function errors()
	{
		$_output = '';
		foreach ($this->errors as $error)
		{
			$errorLang = '##' . $error . '##';
			$_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
		}

		return $_output;
	}

	/**
	 * errors as array
	 *
	 * Get the error messages as an array
	 *
	 * @return array
	 * @author Raul Baldner Junior
	 **/
	public function errors_array($langify = TRUE)
	{
		return $this->errors;
	}


	/**
	 * clear_errors
	 *
	 * Clear Errors
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function clear_errors()
	{
		$this->errors = array();
		return TRUE;
	}

	protected function _prepare_ip($ip_address) {
		// just return the string IP address now for better compatibility
		return $ip_address;
	}

	public function getAccessSchema($module, $userid ,$projects_id=false)
	{
		$access = array();
		$custom_access = array();

		$schema = array('view'      =>false,
						'view_own'  =>false,                    
						'insert'    =>false,
						'edit'      =>false,
						'delete'    =>false);

		$ugroups = $this->db->query($this->sql['get_user_priv'], array($userid));

		if(empty($ugroups->result()))
		{
			return $schema;
		}

		$access = 0;

		foreach ($ugroups->result() as $group) 
		{
			switch($module)
			{
				case 'projects':
					$access = ($group->projects_priv > $access) ? $group->projects_priv : $access;
				break;
				case 'tasks':          
					$access = ($group->tasks_priv > $access) ? $group->tasks_priv : $access;           
				break;
				case 'tickets':          
					$access = ($group->tickets_priv > $access) ? $group->tickets_priv : $access;           
				break;
				case 'discussions':          
					$access = ($group->discussions_priv > $access) ? $group->discussions_priv : $access;           
				break;
				case 'config':
					$access = ($group->config_priv > $access) ? $group->config_priv : $access;
				break;
				case 'users':
					$access = ($group->users_priv > $access) ? $group->users_priv : $access;
				break;
			}
		}	

		if(strstr($module,'Comments'))
		{      
			if($access>0)
			{
				$schema = array('view'      =>true,
				'view_own'  =>true,                            
				'insert'    =>true,
				'edit'      =>true,
				'delete'    =>true);
			}
		}
		else
		{
			switch($access)
			{    
				//full access
				case '1':     
				$schema = array('view'      =>true,
				'view_own'  =>false,                            
				'insert'    =>true,
				'edit'      =>true,
				'delete'    =>true);
				break;     
				//view only             
				case '2':     
				$schema = array('view'      =>true,
				'view_own'  =>false,                            
				'insert'    =>false,
				'edit'      =>false,
				'delete'    =>false);
				break;   
				//view own only       
				case '3':     
				$schema = array('view'      =>true,
				'view_own'  =>true,                            
				'insert'    =>false,
				'edit'      =>false,
				'delete'    =>false);
				break;
				//manage_own_only  
				case '4':     
				$schema = array('view'      =>true,
				'view_own'  =>true,                            
				'insert'    =>true,
				'edit'      =>true,
				'delete'    =>true);
				break;
			}   
		}
		return $schema;
	}

	public function hasAccess($access,$module,$user_id,$projects_id=false)
	{
		$schema = $this->getAccessSchema($module,$user_id,$projects_id);
		      
		if(strstr($access,'|'))
		{
			foreach(explode('|',$access) as $a)
			{
				if(array_key_exists($access, $schema) && $schema[$access])
				{
					return true;
				}
			}
		}
		elseif(array_key_exists($access, $schema))
		{
			return $schema[$access];
		}
		else 
		{
			return false;
		}    
	}

	public function checkAccess($access,$module,$user_id,$projects_id=false)
	{
		return $this->hasAccess($access,$module,$user_id,$projects_id);
	}

	public function hasProjectsAccess($access, $user_id, $project_id)
	{
		$this->load->model('projects_model');
		if($this->hasAccess($access,'projects',$user_id,$project_id) and $this->projects_model->hasViewOwnAccess($user_id, $project_id))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function hasTasksAccess($access, $user_id, $tasks_id, $project_id=false)
	{
		$this->load->model('tasks_model');
		if($this->hasAccess($access,'tasks',$user_id,$project_id) and $this->task_model->hasViewOwnAccess($user_id,$task_id,$project_id))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function hasTicketsAccess($access, $user_id, $ticket_id, $project_id=null)
	{
		$this->load->model('tickets_model');
		if($projects_id)
		{
			if($this->hasAccess($access,'tickets',$user_id,$project_id) and $this->tickets_model->hasViewOwnAccess($user_id,$ticket_id,$project_id))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if($this->hasAccess($access,'tickets',$user_id) and $thos->tickets_model->hasViewOwnAccess($user_id,$ticket_id))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	public function translatePriv($priv_id) {
		
		$schema = array('view'      =>false,
						'view_own'  =>false,                    
						'insert'    =>false,
						'edit'      =>false,
						'delete'    =>false);

		if ($priv_id > 0)
		{
			switch($priv_id)
			{    
				//full access
				case '1':     
				$schema = array('view'      =>true,
				'view_own'  =>false,                            
				'insert'    =>true,
				'edit'      =>true,
				'delete'    =>true);
				break;     
				//view only             
				case '2':     
				$schema = array('view'      =>true,
				'view_own'  =>false,                            
				'insert'    =>false,
				'edit'      =>false,
				'delete'    =>false);
				break;   
				//view own only       
				case '3':     
				$schema = array('view'      =>true,
				'view_own'  =>true,                            
				'insert'    =>false,
				'edit'      =>false,
				'delete'    =>false);
				break;
				//manage_own_lnly  
				case '4':     
				$schema = array('view'      =>true,
				'view_own'  =>true,                            
				'insert'    =>true,
				'edit'      =>true,
				'delete'    =>true);
				break;
			}
		}
		return $schema;
	}

	public function getUserPriviledge($userid) 
	{
		$ugroups = $this->db->query($this->sql['get_user_priv'], array($userid));
		$access['projects_priv']['value'] = 4;
		$access['tasks_priv']['value'] = 4;
		$access['tickets_priv']['value'] = 4;           
		$access['discussions_priv']['value'] = 4;
		$access['config_priv']['value'] = 4;
		$access['users_priv']['value'] = 4;
		$access['projects_priv']['schema'] = array();
		$access['tasks_priv']['schema'] = array();
		$access['tickets_priv']['schema'] = array();
		$access['discussions_priv']['schema'] = array();
		$access['config_priv']['schema'] = array();
		$access['users_priv']['schema'] = array();

		if ($ugroups->num_rows() == 0)
		{
			$access['projects_priv']['value'] = 0;
			$access['tasks_priv']['value'] = 0;
			$access['tickets_priv']['value'] = 0;           
			$access['discussions_priv']['value'] = 0;
			$access['config_priv']['value'] = 0;
			$access['users_priv']['value'] = 0;
		}
		else {
			foreach ($ugroups->result() as $group) 
			{
				$access['projects_priv']['value'] = ($group->projects_priv < $access['projects_priv']['value']) ? $group->projects_priv : $access['projects_priv']['value'];
				$access['tasks_priv']['value'] = ($group->tasks_priv < $access['tasks_priv']['value']) ? $group->tasks_priv : $access['tasks_priv']['value'];
				$access['tickets_priv']['value'] = ($group->tickets_priv < $access['tickets_priv']['value']) ? $group->tickets_priv : $access['tickets_priv']['value'];           
				$access['discussions_priv']['value'] = ($group->discussions_priv < $access['discussions_priv']['value']) ? $group->discussions_priv : $access['discussions_priv']['value'];           
				$access['config_priv']['value'] = ($group->config_priv < $access['config_priv']['value']) ? $group->config_priv : $access['config_priv']['value'];
				$access['users_priv']['value'] = ($group->users_priv < $access['users_priv']['value']) ? $group->users_priv : $access['users_priv']['value'];
			}
		}

		$access['projects_priv']['schema'] = $this->translatePriv($access['projects_priv']['value']);
		$access['tasks_priv']['schema'] = $this->translatePriv($access['projects_priv']['value']);
		$access['tickets_priv']['schema'] = $this->translatePriv($access['tickets_priv']['value']);
		$access['discussions_priv']['schema'] = $this->translatePriv($access['discussions_priv']['value']);
		$access['config_priv']['schema'] = $this->translatePriv($access['config_priv']['value']);
		$access['users_priv']['schema'] = $this->translatePriv($access['users_priv']['value']);	

		return $access;
	}

	public function get_user_attrib($user_id)
	{
		if (!empty($user_id))
		{
			$query = $this->db->query($this->sql['get_user_by_id'], array($user_id));
			if ($query->num_rows() > 0) {
				return $query->row();
			} else {
				return null;
			}
		}
		return null;
	}

	public function get_user_groups($user_id)
	{
		if (!empty($user_id))
		{
			$query = $this->db->query($this->sql['get_user_group'], array($user_id));
			if ($query->num_rows() > 0) {
				return $query->result();
			}
		}
		return null;
	}
}

?>