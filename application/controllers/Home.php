<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Home extends Secure_Controller 
{
	function __construct()
	{
		parent::__construct(NULL, NULL, 'home');
	}

	public function index()
	{
		$this->load->view('home/home');
	}

	public function logout()
	{
		$this->User->logout();
	}

	/*
	Loads the change user password form
	*/
	public function change_password($user_id = -1)
	{
		$person_info = $this->User->get_info($user_id);
		foreach(get_object_vars($person_info) as $property => $value)
		{
			$person_info->$property = $this->xss_clean($value);
		}
		$data['person_info'] = $person_info;

		$this->load->view('home/form_change_password', $data);
	}

	/*
	Change user password
	*/
	public function save($user_id = -1)
	{
		if($this->input->post('current_password') != '' && $user_id != -1)
		{
			if($this->User->check_password($this->input->post('username'), $this->input->post('current_password')))
			{
				$user_data = array(
					'username' => $this->input->post('username'),
					'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
					'hash_version' => 2
				);

				if($this->User->change_password($user_data, $user_id))
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('users_successful_change_password'), 'id' => $user_id));
				}
				else//failure
				{
					echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('users_unsuccessful_change_password'), 'id' => -1));
				}
			}
			else
			{
				echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('users_current_password_invalid'), 'id' => -1));
			}
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('users_current_password_invalid'), 'id' => -1));
		}
	}
}
?>
