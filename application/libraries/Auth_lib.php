<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Auth_lib
{
	/**
	 * account status ('not_activated', etc ...)
	 *
	 * @var string
	 **/
	protected $status;

	/**
	 * extra where
	 *
	 * @var array
	 **/
	public $_extra_where = array();

	/**
	 * extra set
	 *
	 * @var array
	 **/
	public $_extra_set = array();

	/**
	 * caching of users and their groups
	 *
	 * @var array
	 **/
	public $_cache_user_in_group;

	/**
	 * __construct
	 *
	 * @return void
	 * @author Ben
	 **/
	public function __construct()
	{
		$this->load->config('auth', TRUE);
		$this->load->library(array('email'));
		$this->load->helper(array('cookie', 'language','url'));
		$this->load->library('session');
		$this->load->model('auth_model');
		$this->_cache_user_in_group =& $this->auth_model->_cache_user_in_group;

		//auto-login the user if they are remembered
		if (!$this->logged_in() && get_cookie($this->config->item('identity_cookie_name', 'auth')) && get_cookie($this->config->item('remember_cookie_name', 'auth')))
		{
			$this->auth_model->login_remembered_user();
		}

		$email_config = $this->config->item('email_config', 'auth');

		if ($this->config->item('use_ci_email', 'auth') && isset($email_config) && is_array($email_config))
		{
			$this->email->initialize($email_config);
		}
	}

	/**
	 * __call
	 *
	 * Acts as a simple way to call model methods without loads of stupid alias'
	 *
	 **/
	public function __call($method, $arguments)
	{
		if (!method_exists( $this->auth_model, $method) )
		{
			throw new Exception('Undefined method auth::' . $method . '() called');
		}
		if($method == 'create_user')
		{
			return call_user_func_array(array($this, 'register'), $arguments);
		}
		if($method=='update_user')
		{
			return call_user_func_array(array($this, 'update'), $arguments);
		}
		return call_user_func_array( array($this->auth_model, $method), $arguments);
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 * I can't remember where I first saw this, so thank you if you are the original author. -Militis
	 *
	 * @access	public
	 * @param	$var
	 * @return	mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}


	/**
	 * forgotten password feature
	 *
	 * @return mixed  boolian / array
	 * @author Mathew
	 **/
	public function forgotten_password($identity)    //changed $email to $identity
	{
		if ( $this->auth_model->forgotten_password($identity) )   //changed
		{
			// Get user information
      	$identifier = $this->auth_model->identity_column; // use model identity column, so it can be overridden in a controller
      	$user = $this->where($identifier, $identity)->where('active', 1)->users()->row();  // changed to get_user_by_identity from email

			if ($user)
			{
				$data = array(
					'identity'		=> $user->{$this->config->item('identity', 'auth')},
					'forgotten_password_code' => $user->forgotten_password_code
				);

				if(!$this->config->item('use_ci_email', 'auth'))
				{
					$this->set_message('forgot_password_successful');
					return $data;
				}
				else
				{
					$message = $this->load->view($this->config->item('email_templates', 'auth').$this->config->item('email_forgot_password', 'auth'), $data, true);
					$this->email->clear();
					$this->email->from($this->config->item('admin_email', 'auth'), $this->config->item('site_title', 'auth'));
					$this->email->to($user->email);
					$this->email->subject($this->config->item('site_title', 'auth') . ' - ' . $this->lang->line('email_forgotten_password_subject'));
					$this->email->message($message);

					if ($this->email->send())
					{
						$this->set_message('forgot_password_successful');
						return TRUE;
					}
					else
					{
						$this->set_error('forgot_password_unsuccessful');
						return FALSE;
					}
				}
			}
			else
			{
				$this->set_error('forgot_password_unsuccessful');
				return FALSE;
			}
		}
		else
		{
			$this->set_error('forgot_password_unsuccessful');
			return FALSE;
		}
	}

	/**
	 * forgotten_password_complete
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function forgotten_password_complete($code)
	{
		$identity = $this->config->item('identity', 'auth');
		$profile  = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if (!$profile)
		{
			//$this->auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}

		$new_password = $this->auth_model->forgotten_password_complete($code, $profile->salt);

		if ($new_password)
		{
			$data = array(
				'identity'     => $profile->{$identity},
				'new_password' => $new_password
			);
			if(!$this->config->item('use_ci_email', 'auth'))
			{
				$this->set_message('password_change_successful');
				//$this->auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
					return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'auth').$this->config->item('email_forgot_password_complete', 'auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'auth'), $this->config->item('site_title', 'auth'));
				$this->email->to($profile->email);
				$this->email->subject($this->config->item('site_title', 'auth') . ' - ' . $this->lang->line('email_new_password_subject'));
				$this->email->message($message);

				if ($this->email->send())
				{
					$this->set_message('password_change_successful');
					//$this->auth_model->trigger_events(array('post_password_change', 'password_change_successful'));
					return TRUE;
				}
				else
				{
					$this->set_error('password_change_unsuccessful');
					//$this->auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
					return FALSE;
				}

			}
		}

		//$this->auth_model->trigger_events(array('post_password_change', 'password_change_unsuccessful'));
		return FALSE;
	}

	/**
	 * forgotten_password_check
	 *
	 * @return void
	 * @author Michael
	 **/
	public function forgotten_password_check($code)
	{
		$profile = $this->where('forgotten_password_code', $code)->users()->row(); //pass the code to profile

		if (!is_object($profile))
		{
			$this->set_error('password_change_unsuccessful');
			return FALSE;
		}
		else
		{
			if ($this->config->item('forgot_password_expiration', 'auth') > 0) {
				//Make sure it isn't expired
				$expiration = $this->config->item('forgot_password_expiration', 'auth');
				if (time() - $profile->forgotten_password_time > $expiration) {
					//it has expired
					$this->clear_forgotten_password_code($code);
					$this->set_error('password_change_unsuccessful');
					return FALSE;
				}
			}
			return $profile;
		}
	}

	/**
	 * register
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function register($email, $name, $password, $phone, $photo, $group_ids = array()) //need to test email activation
	{
		//$this->auth_model->trigger_events('pre_account_creation');

		$email_activation = $this->config->item('email_activation', 'auth');

		$id = $this->auth_model->register($email, $name, $password, $phone, $photo, $group_ids);

		if (!$email_activation)
		{
			if ($id !== FALSE)
			{
				$this->set_message('Account Successfully Created');
				return $id;
			}
			else
			{
				$this->set_error('Unable to Create Account');
				return FALSE;
			}
		}
		else
		{
			/*if (!$id)
			{
				$this->set_error('account_creation_unsuccessful');
				return FALSE;
			}

			// deactivate so the user much follow the activation flow
			$deactivate = $this->auth_model->deactivate($id);

			// the deactivate method call adds a message, here we need to clear that
			$this->auth_model->clear_messages();


			if (!$deactivate)
			{
				$this->set_error('deactivate_unsuccessful');
				//$this->auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful'));
				return FALSE;
			}

			$activation_code = $this->auth_model->activation_code;
			$identity        = $this->config->item('identity', 'auth');
			$user            = $this->auth_model->user($id)->row();

			$data = array(
				'identity'   => $user->{$identity},
				'id'         => $user->id,
				'email'      => $email,
				'activation' => $activation_code,
			);
			if(!$this->config->item('use_ci_email', 'auth'))
			{
				//$this->auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
				$this->set_message('activation_email_successful');
				return $data;
			}
			else
			{
				$message = $this->load->view($this->config->item('email_templates', 'auth').$this->config->item('email_activate', 'auth'), $data, true);

				$this->email->clear();
				$this->email->from($this->config->item('admin_email', 'auth'), $this->config->item('site_title', 'auth'));
				$this->email->to($email);
				$this->email->subject($this->config->item('site_title', 'auth') . ' - ' . $this->lang->line('email_activation_subject'));
				$this->email->message($message);

				if ($this->email->send() == TRUE)
				{
					//$this->auth_model->trigger_events(array('post_account_creation', 'post_account_creation_successful', 'activation_email_successful'));
					$this->set_message('activation_email_successful');
					return $id;
				}

			}

			//$this->auth_model->trigger_events(array('post_account_creation', 'post_account_creation_unsuccessful', 'activation_email_unsuccessful'));
			$this->set_error('activation_email_unsuccessful');
			return FALSE;*/
			return FALSE;
		}
	}

	/**
	 * logout
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logout()
	{
		//$this->auth_model->trigger_events('logout');

		$identity = $this->config->item('identity', 'auth');

                if (substr(CI_VERSION, 0, 1) == '2')
		{
			$this->session->unset_userdata( array($identity => '', 'id' => '', 'user_id' => '') );
                }
                else
                {
                	$this->session->unset_userdata( array($identity, 'id', 'user_id') );
                }

		// delete the remember me cookies if they exist
		if (get_cookie($this->config->item('identity_cookie_name', 'auth')))
		{
			delete_cookie($this->config->item('identity_cookie_name', 'auth'));
		}
		if (get_cookie($this->config->item('remember_cookie_name', 'auth')))
		{
			delete_cookie($this->config->item('remember_cookie_name', 'auth'));
		}

		// Destroy the session
		$this->session->sess_destroy();

		//Recreate the session
		if (substr(CI_VERSION, 0, 1) == '2')
		{
			$this->session->sess_create();
		}
		else
		{
			$this->session->sess_regenerate(TRUE);
		}

		$this->set_message('logout_successful');
		return TRUE;
	}

	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function logged_in()
	{
		//$this->auth_model->trigger_events('logged_in');

		return (bool) $this->session->userdata('identity');
	}

	public function get_user_id()
	{
		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $user_id;
		}
		return null;
	}

	public function get_user_email()
	{
		$email = $this->session->userdata('email');
		if (!empty($email))
		{
			return $email;
		}
		return null;
	}

	public function get_user_attr()
	{
		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $this->auth_model->get_user_attrib($user_id);
		}
		return null;
	}

	public function get_user_grps()
	{
		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id))
		{
			return $this->auth_model->get_user_groups($user_id);
		}
		return null;
	}

	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function is_admin($id=false)
	{
		//$this->auth_model->trigger_events('is_admin');

		$admin_group = $this->config->item('admin_group', 'auth');

		return $this->in_group($admin_group, $id);
	}

	/**
	 * in_group
	 *
	 * @param mixed group(s) to check
	 * @param bool user id
	 * @param bool check if all groups is present, or any of the groups
	 *
	 * @return bool
	 * @author Phil Sturgeon
	 **/
	public function in_group($check_group, $id=false, $check_all = false)
	{
		//$this->auth_model->trigger_events('in_group');

		$id || $id = $this->session->userdata('user_id');

		if (!is_array($check_group))
		{
			$check_group = array($check_group);
		}

		if (isset($this->_cache_user_in_group[$id]))
		{
			$groups_array = $this->_cache_user_in_group[$id];
		}
		else
		{
			$users_groups = $this->auth_model->get_users_groups($id)->result();
			$groups_array = array();
			foreach ($users_groups as $group)
			{
				$groups_array[$group->id] = $group->name;
			}
			$this->_cache_user_in_group[$id] = $groups_array;
		}
		foreach ($check_group as $key => $value)
		{
			$groups = (is_string($value)) ? $groups_array : array_keys($groups_array);

			/**
			 * if !all (default), in_array
			 * if all, !in_array
			 */
			if (in_array($value, $groups) xor $check_all)
			{
				/**
				 * if !all (default), true
				 * if all, false
				 */
				return !$check_all;
			}
		}

		/**
		 * if !all (default), false
		 * if all, true
		 */
		return $check_all;
	}

}
