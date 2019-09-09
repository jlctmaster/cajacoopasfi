<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Cash_books extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('cash_books');
	}

	public function index()
	{
		 $data['table_headers'] = $this->xss_clean(get_cash_book_manage_table_headers());

		 $this->load->view('cash_books/manage', $data);
	}

	/*
	Returns expense_category_manage table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$cash_books = $this->Cash_book->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Cash_book->get_found_rows($search);

		$data_rows = array();
		foreach($cash_books->result() as $cash_book)
		{
			$data_rows[] = $this->xss_clean(get_cash_book_data_row($cash_book));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function get_row($row_id)
	{
		$data_row = $this->xss_clean(get_cash_book_data_row($this->Cash_book->get_info($row_id)));

		echo json_encode($data_row);
	}

	public function check_cash_general($cash_book_id = -1)
	{
		$data_row = $this->Cash_book->exists_cash_general($cash_book_id);
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}
		echo json_encode($data_row);
	}

	public function get_user_by_location($location_id)
	{
		$data_row = $this->User->get_user_by_location($location_id);
		foreach(get_object_vars($data_row) as $property => $value)
		{
			$data_row->$property = $this->xss_clean($value);
		}
		echo json_encode($data_row);
	}

	public function view($cash_book_id = -1)
	{
		$data['cash_book_info'] = $this->Cash_book->get_info($cash_book_id);

		$stock_location = array('-1' => $this->lang->line('common_none_selected_text'));
		foreach($this->Stock_location->get_all()->result_array() as $row)
		{
			$stock_location[$this->xss_clean($row['location_id'])] = $this->xss_clean($row['location_name']);
		}
		$data['stock_location'] = $stock_location;
		$data['selected_stock_location'] = $data['cash_book_info']->stock_location_id;

		$user = array('-1' => $this->lang->line('common_none_selected_text'));
		$data_row = $this->User->get_user_by_location($data['cash_book_info']->stock_location_id);
		foreach($data_row as $row)
		{
			$user[$this->xss_clean($row->person_id)] = $this->xss_clean($row->first_name." ".$row->last_name." (".$row->username.")");
		}
		$data['user'] = $user;
		$data['selected_user'] = $data['cash_book_info']->user_id;

		$this->load->view("cash_books/form", $data);
	}

	public function save($cash_book_id = -1)
	{
		$this->Cash_book->set_log("<< Start Log >>");

		$cash_book_data = array(
			'code' => $this->input->post('code'),
			'stock_location_id' => $this->input->post('stock_location_id'),
			'user_id' => $this->input->post('user_id'),
			'address' => $this->input->post('address'),
			'is_cash_general' => $this->input->post('is_cash_general') != NULL
		);

		if($this->Cash_book->save($cash_book_data, $cash_book_id))
		{
			$cash_book_data = $this->xss_clean($cash_book_data);

			// New cash_book_id
			if($cash_book_id == -1)
			{
				$this->Cash_book->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cash_book->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cash_books_successful_adding'), 'id' => $cash_book_data['cash_book_id']));
			}
			else // Existing Cash_book
			{
				$this->Cash_book->set_log($this->db->last_query());
				$this->Cash_book->set_log("<< End Log >>");
				//	Use log 
				//	$this->Cash_book->get_log()."<br>".
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cash_books_successful_updating'), 'id' => $cash_book_id));
			}
		}
		else//failure
		{
			if($cash_book_id != -1)
			{
				$this->Cash_book->set_log($this->db->last_query());
			}
			$this->Cash_book->set_log("<< End Log >>");
			//	Use log 
			//	$this->Cash_book->get_log()."<br>".
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cash_books_error_adding_updating') . ' ' . $cash_book_data['code'], 'id' => -1));
		}
	}

	public function delete()
	{
		$cash_book_to_delete = $this->input->post('ids');

		$success = FALSE;

		$count = 0;
		for($i = 0; $i < count($cash_book_to_delete); $i++)
		{
			if(!$this->Cash_book->have_movements($this->input->post('ids')[$i]))
			{
				$this->Cash_book->set_log($this->db->last_query());
				$success = $this->Cash_book->delete($this->input->post('ids')[$i]);
				$this->Cash_book->set_log($this->db->last_query());
				if($success)
				{
					$count++;
				}
			}
		}

		$not_deleted = (count($cash_book_to_delete)-$count);
		
		if($success)
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('cash_books_successful_deleted') . ' ' . $count . ' ' . $this->lang->line('cash_books_one_or_multiple') . ' <br> Los otros '.$not_deleted. ' ' .$this->lang->line('cash_books_one_or_multiple'). ' tienen movimientos y no se pueden borrar'));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('cash_books_cannot_be_deleted')));
		}
	}
}
?>
