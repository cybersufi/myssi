<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu {
   
    function __construct()
    {
        $this->load->model('menu_model');
    }

    /**
     * __call
     *
     * Acts as a simple way to call model methods without loads of stupid alias'
     *
     **/
    public function __call($method, $arguments)
    {
        if (!method_exists( $this->menu_model, $method) )
        {
            throw new Exception('Undefined method auth::' . $method . '() called');
        }
        return call_user_func_array( array($this->menu_model, $method), $arguments);
    }

    /**
     * __get
     *
     * Enables the use of CI super-global without having to define an extra variable.
     *
     * I can't remember where I first saw this, so thank you if you are the original author. -Militis
     *
     * @access  public
     * @param   $var
     * @return  mixed
     */
    public function __get($var)
    {
        return get_instance()->$var;
    }
}