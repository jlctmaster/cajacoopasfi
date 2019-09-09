<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Pay_cash extends Secure_Controller
{
	function __construct()
	{
		parent::__construct('pay_cash', NULL, 'pay_cash');
	}

	public function index()
	{
		$this->load->view('home/pay_cash');
	}

	public function logout()
	{
		$this->User->logout();
	}
}
?>
