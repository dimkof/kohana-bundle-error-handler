<?php defined('SYSPATH') OR die('No direct script access.');

class HTTP_Exception extends Kohana_HTTP_Exception {

    public function get_response()
    {
		// Lets log the Exception, Just in case it's important!
        Kohana_Exception::log($this);

        if (Kohana::$environment > Kohana::STAGING)
        {
            // Show the normal Kohana error page.
            return parent::get_response();
        }

        $config = Kohana::$config->load('error_handler');

        $tpl = (Kohana::find_file('views', rtrim($config->view_path, '/').'/'.$this->code))
			? rtrim($config->view_path, '/').'/'.$this->code
			: $config->view_default;

        $view = View::factory($tpl)
			->set('code', $this->code)
			->set('message', $this->getMessage());


		if ($this->_request->is_initial() AND ! $this->_request->is_ajax())
		{
			$view = View::factory($config->view_index)->set('content', $view);
		}

		$response = Response::factory();
		$response->body($view->render());

        return $response;
    }
}
