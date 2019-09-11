<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Submenu extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$module_id = $this->uri->uri_string;
		$submodule_id = NULL; 
		$menu_group = $this->uri->uri_string;

		$this->load->model('User');
		$model = $this->User;

		if(!$model->is_logged_in())
		{
			redirect('login');
		}

		$logged_in_user_info = $model->get_logged_in_user_info();
		if(!$model->has_module_grant($module_id, $logged_in_user_info->person_id) || 
			(isset($submodule_id) && !$model->has_module_grant($submodule_id, $logged_in_user_info->person_id)))
		{
			redirect('no_access/' . $module_id . '/' . $submodule_id);
		}

		// load up global data visible to all the loaded views

		$this->load->library('session');
		if($menu_group == NULL)
		{
			$menu_group = $this->session->userdata('menu_group');
		}
		else
		{
			$this->session->set_userdata('menu_group', $menu_group);
		}

		if($menu_group == 'home')
		{
			$allowed_modules = $this->Module->get_allowed_home_modules($logged_in_user_info->person_id);
		}
		else if($menu_group == 'office')
		{
			$allowed_modules = $this->Module->get_allowed_office_modules($logged_in_user_info->person_id);
		}
		else
		{
			$allowed_modules = $this->Module->get_allowed_custom_modules($logged_in_user_info->person_id,$menu_group);
		}

		foreach($allowed_modules->result() as $module)
		{
			$data['allowed_modules'][] = $module;
		}

		$data['user_info'] = $logged_in_user_info;
		$data['controller_name'] = $module_id;

		$this->load->vars($data);

	}

	public function index($submenu)
	{

		$data['submenu'] = $submenu;

		$this->load->view('home/custom',$data);
	}

	public function logout()
	{
		$this->User->logout();
	}
}
?>
