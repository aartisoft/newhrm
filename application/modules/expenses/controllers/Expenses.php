<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expenses extends MX_Controller {

	

	function __construct()
	{
		parent::__construct();

		$this->load->module('layouts');	
		User::logged_in();

		$this->load->library(array('template','form_validation'));
		$this->template->title(lang('expenses').' - '.config_item('company_name'));
		$this->load->model(array('App','Client','Expense','Project'));

		App::module_access('menu_expenses');

        if (User::is_admin() || User::perm_allowed(User::get_id(),'view_all_expenses')) {
        	if(isset($_GET['project'])){

        	$this->expenses = App::get_by_where('expenses',array('id !='=>'0','project' => $_GET['project']));
        	
        	}else{

        	$this->expenses = App::get_by_where('expenses',array('id !='=>'0'));

        	}

        }elseif (User::is_staff()) {
        	$this->expenses = $this->staff_expenses();
        }else{      

        	$this->expenses = App::get_by_where('expenses',
        						array('client'=>User::profile_info(User::get_id())->company,'show_client'=>'Yes'));
        }
        
        $this->applib->set_locale();
	}

	
	function index()
	{
	$data['page'] = lang('expenses');

	$data['datatables'] = TRUE;
	$data['datepicker'] = TRUE;
	$data['form'] = TRUE;
	$data['expenses'] = $this->expenses;
	$data['attach_slip'] = TRUE;
	$this->template
	->set_layout('users')
	->build('expenses',isset($data) ? $data : NULL);
	}

	

	function view($id = NULL)
	{	
		if(!$this->_can_view_expense($id)){ redirect(); }
		$this->load->model('Invoice');

		$data['page'] = lang('expenses');
        $data['show_links'] = TRUE;
        $data['datepicker'] = TRUE;
        $data['id'] = $id;
		$data['expenses'] = $this->expenses; // GET a list of the Expenses
		$this->template
		->set_layout('users')
		->build('view',isset($data) ? $data : NULL);
	}


	function _can_view_expense($expense){
		if(User::is_admin()){ return TRUE; }
		$info = Expense::view_by_id($expense);

		if($info->show_client == 'No' && User::is_client()){ return FALSE; }
		
		if($info->client == User::profile_info(User::get_id())->company) { return TRUE; }

		if(User::perm_allowed(User::get_id(),'view_all_expenses')) { return TRUE; }

		if(User::is_staff()){
			return (Project::is_assigned(User::get_id(),$info->project)) ? TRUE : FALSE;
		}else{
				return FALSE;
		}
	}

	function create()
	{

		if($this->_can_add_expense() == FALSE){ App::access_denied('expenses'); }

		if ($this->input->post()) {

		$this->form_validation->set_rules('amount', 'Amount', 'required');
		$this->form_validation->set_rules('category', 'Category', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			Applib::go_to('expenses','error',lang('operation_failed'));
		}else{

			$attached_file = NULL;
			
				if(file_exists($_FILES['receipt']['tmp_name']) || is_uploaded_file($_FILES['receipt']['tmp_name'])) {
					$upload_response = $this->upload_slip($this->input->post());
					if($upload_response){
						$attached_file = $upload_response;
					}else{
						$attached_file = NULL;
						// Applib::go_to('expenses','error',lang('file_upload_failed'));
						$this->session->set_flashdata('tokbox_error', lang('file_upload_failed'));
						redirect('expenses');
					}

				}
				$project_id = $this->input->post('project',TRUE);
				$p = Project::by_id($project_id);

              	$billable = ($this->input->post('billable') == 'on') ? '1' : '0';
              	$show_client = ($this->input->post('show_client') == 'on') ? 'Yes' : 'No';
              	$invoiced = ($this->input->post('invoiced') == 'on') ? '1' : '0';
              	$client = ($this->input->post('project') == 'NULL') ? $this->input->post('client') : $p->client ;
              	$cur = Client::client_currency($client)->code;
              	$expense_date = date('Y-m-d',strtotime($this->input->post('expense_date',TRUE)));

              	$data = array(
              				'added_by'  	=> User::get_id(),
              				'billable'		=> $billable,
              				'amount'		=> $this->input->post('amount',TRUE),
              				'expense_date'	=> $expense_date,
              				'notes'			=> $this->input->post('notes'),
              				'project'		=> $this->input->post('project',TRUE),
              				'client'		=> $client,
              				'receipt'		=> $attached_file,
              				'invoiced'		=> $invoiced,
              				'show_client'	=> $show_client,
              				'category'		=> $this->input->post('category')
              	);
                    
		if($expense_id = App::save_data('expenses',$data)){
			$title = ($this->input->post('project') == 'NULL') ? 'N/A' : $p->project_title;
			// Log activity
			$data = array(
				'module' => 'expenses',
				'module_field_id' => $expense_id,
				'user' => User::get_id(),
				'activity' => 'activity_expense_created',
				'icon' => 'fa-plus',
				'value1' => $cur.' '.$this->input->post('amount'),
				'value2' => $title
				);
			App::Log($data);

			// Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('expense_created_successfully'));
			$this->session->set_flashdata('tokbox_success', lang('expense_created_successfully'));
			redirect($_SERVER['HTTP_REFERER']);
				}
			}

		}else{
			$auto_select = NULL;
			if(isset($_GET['project'])){ $auto_select = $_GET['project']; }else{ $auto_select = NULL; }
			
			$data['categories'] = App::get_by_where('categories',array('module'=>'expenses'));
			$data['projects'] = $this->get_user_projects();
			$data['auto_select_project'] = $auto_select;
			$data['form'] = TRUE;
			$this->load->view('modal/create_expense',$data);

		}
	}

	function _can_add_expense(){
		if (User::is_admin() || User::perm_allowed(User::get_id(),'add_expenses')) {
			return TRUE;
		}else{
			return FALSE;	
		}
	}

	function edit($id = NULL)
	{
		if ($this->input->post()) {

			
		$expense_id = $this->input->post('expense', TRUE);

		$this->form_validation->set_rules('amount', 'Amount', 'required');
		$this->form_validation->set_rules('category', 'Category', 'required');

		if ($this->form_validation->run() == FALSE)
		{
				$_POST = '';
				// Applib::go_to('expenses','error',lang('error_in_form'));
				$this->session->set_flashdata('tokbox_success', lang('error_in_form'));
				redirect('expenses');
		}else{	
			$receipt = NULL;
			if(file_exists($_FILES['receipt']['tmp_name']) || is_uploaded_file($_FILES['receipt']['tmp_name'])) {

					$upload_response = $this->upload_slip($this->input->post());
					if($upload_response){
						$receipt = $upload_response;
						App::update('expenses',array('id'=>$expense_id),array('receipt' => $receipt));
					}else{
						$receipt = NULL;
						// Applib::go_to('expenses','error',lang('file_upload_failed'));
						$this->session->set_flashdata('tokbox_error', lang('file_upload_failed'));
						redirect('expenses');
					}

				}
			  $project_id = $this->input->post('project',TRUE);
			  $p = Project::by_id($project_id);
              $billable = ($this->input->post('billable') == 'on') ? '1' : '0';
              $show_client = ($this->input->post('show_client') == 'on') ? 'Yes' : 'No';
              $invoiced = ($this->input->post('invoiced') == 'on') ? '1' : '0';

              $client = ($this->input->post('project') == 0 || $this->input->post('project') == 'NULL') 
              			? $this->input->post('client') : $p->client ;
              $cur = Client::client_currency($client)->code;
              $expense_date = date('Y-m-d',strtotime($this->input->post('expense_date',TRUE)));

              $data = array(
                				'billable'		=> $billable,
                				'amount'		=> $this->input->post('amount'),
                				'expense_date'	=> $expense_date,
                				'notes'			=> $this->input->post('notes'),
                				'project'		=> $project_id,
                				'client'		=> $client,
                				'invoiced'		=> $invoiced,
                				'show_client'	=> $show_client,
                				'category'		=> $this->input->post('category')
                				);
                if(App::update('expenses',array('id'=>$expense_id),$data)){

                $title = ($this->input->post('project') == 'NULL' || $this->input->post('project') == 0) ? 'N/A' : $p->project_title;
                	// Log activity
				$data = array(
					'module' => 'expenses',
					'module_field_id' => $expense_id,
					'user' => User::get_id(),
					'activity' => 'activity_expense_edited',
					'icon' => 'fa-pencil',
					'value1' => $cur.' '.$this->input->post('amount'),
					'value2' => $title
					);
				App::Log($data);


				// Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('expense_edited_successfully'));
				$this->session->set_flashdata('tokbox_success', lang('expense_edited_successfully'));
				redirect($_SERVER['HTTP_REFERER']);

                }
                else
                {
                	// Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('expense_edited_successfully'));
                	$this->session->set_flashdata('tokbox_success', lang('expense_edited_successfully'));
					redirect($_SERVER['HTTP_REFERER']);
                }
			}
		}else{


			$data['form'] = TRUE;
			$data['categories'] = App::get_by_where('categories',array('module'=>'expenses'));
			$data['projects'] = $this->get_user_projects();
			$data['id'] = $id;
			$this->load->view('modal/edit_expense',$data);

		}
	}
	function show($id = NULL){
		$data = array('show_client' => 'Yes');
		App::update('expenses',array('id'=>$id),$data);
		// Applib::go_to($_SERVER['HTTP_REFERER'],'success',lang('expense_edited_successfully'));
		$this->session->set_flashdata('tokbox_success', lang('expense_edited_successfully'));
				redirect($_SERVER['HTTP_REFERER']);
	}

	

	function delete($id = NULL)
	{
		if ($this->input->post()) {

			$expense = $this->input->post('expense', TRUE);
			App::delete('expenses',array('id'=>$expense));

			// Applib::go_to('expenses','success',lang('expense_deleted_successfully'));
			$this->session->set_flashdata('tokbox_success', lang('expense_deleted_successfully'));
				redirect('expenses');
		}else{
			$data['expense'] = $id;
			$this->load->view('modal/delete_expense',$data);

		}
	}

	/**
	 * get_user_projects
	 *
	 * Get user projects
	 *
	 * @access	public
	 * @param	type	name
	 * @return	array	
	 */
	 
	function get_user_projects()
	{
		if (!User::is_client()) {
			if (User::is_admin() || (User::is_staff() && User::perm_allowed(User::get_id(),'view_all_projects'))) {
				return Project::all();
			}else{
				return array();
			}
		}else{
			return Project::by_client(User::profile_info(User::get_id())->company);
		}
	}


	function upload_slip($data){

	Applib::is_demo();

	if ($data) {
		$config['upload_path']   = './assets/uploads/';
		$config['allowed_types'] = config_item('allowed_files');
		$config['remove_spaces'] = TRUE;
		$config['overwrite']  = FALSE;
		$this->load->library('upload', $config);

		if ($this->upload->do_upload('receipt'))
		{
			$filedata = $this->upload->data();
			return $filedata['file_name'];
		}else{
			return FALSE;
		}
	}else{
		return FALSE;
	}
}



	private function staff_expenses(){

		$projects = $this->db->select('project_assigned')
							 ->where('assigned_user',User::get_id())
							 ->get('assign_projects')->result_array();
		$pro = array();
		foreach ($projects as $key => $p) {
			$pro[] = $p['project_assigned'];
			
		}

		$expenses = array();
		if(count($pro) > 0){
			$expenses = $this->db->where_in('project', $pro)->get('expenses')->result();
		}
		
		return $expenses;
	}

}

/* End of file expenses.php */