<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wikig extends CI_Controller 
{
	private $user = false;

	/**
	 * Index Page for this controller.
	 */
	public function index()
	{
		// Check session
		$user = $this->layout->init("wikig");


		$data = array();
		$data['user'] = $user;
		

        $this->layout->addCss('wikig');
		$this->layout->view('wikig/index', $data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */