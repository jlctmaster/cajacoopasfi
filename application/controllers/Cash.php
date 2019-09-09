<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Cash extends Secure_Controller
{
	function __construct()
	{
		parent::__construct('cash', NULL, 'cash');
	}

	public function index()
	{
		$this->load->view('home/cash');
	}

	public function logout()
	{
		$this->User->logout();
	}
}
?>
