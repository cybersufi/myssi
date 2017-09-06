<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification {

    protected $CI;
    protected $messages;
    protected $errors; 
    protected $error_start_delimiter;
    protected $error_end_delimiter;
    protected $message_start_delimiter;
    protected $message_end_delimiter;

    public function __construct()
    {	
		$this->CI =& get_instance();
        $this->CI->load->config('auth', TRUE);
        $delimiters_source = $this->CI->config->item('delimiters_source', 'auth');
        // load the error delimeters either from the config file or use what's been supplied to form validation
        if ($delimiters_source === 'form_validation')
        {
            // load in delimiters from form_validation
            // to keep this simple we'll load the value using reflection since these properties are protected
            $this->CI->load->library('form_validation');
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
            $this->message_start_delimiter = $this->CI->config->item('message_start_delimiter', 'auth');
            $this->message_end_delimiter   = $this->CI->config->item('message_end_delimiter', 'auth');
            $this->error_start_delimiter   = $this->CI->config->item('error_start_delimiter', 'auth');
            $this->error_end_delimiter     = $this->CI->config->item('error_end_delimiter', 'auth');
        }
    }

    public function set_message_delimiters($start_delimiter, $end_delimiter)
    {
        $this->message_start_delimiter = $start_delimiter;
        $this->message_end_delimiter   = $end_delimiter;

        return TRUE;
    }

    public function set_error_delimiters($start_delimiter, $end_delimiter)
    {
        $this->error_start_delimiter = $start_delimiter;
        $this->error_end_delimiter   = $end_delimiter;

        return TRUE;
    }

    public function set_message($message)
    {
        $this->messages[] = $message;
        return $message;
    }

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

    public function messages_array()
    {
        return $this->messages;
    }

    public function clear_messages()
    {
        $this->messages = array();
        return TRUE;
    }

    public function set_error($error)
    {
        $this->errors[] = $error;
        return $error;
    }

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

    public function errors_array($langify = TRUE)
    {
        return $this->errors;
    }

    public function clear_errors()
    {
        $this->errors = array();
        return TRUE;
    }

}