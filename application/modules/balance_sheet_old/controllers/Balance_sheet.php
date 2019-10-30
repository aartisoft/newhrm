<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class balance_sheet extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		User::logged_in();

		$this->load->module('layouts');	
		$this->load->library(array('template','form_validation'));
		$this->template->title('balance_sheet');
		$this->load->model(array('App'));

		$this->applib->set_locale();
		$this->load->helper('date');
	}

	function index()
	{
		$this->load->module('layouts');

		$this->load->library('template');		 

		$this->template->title(lang('balance_sheet').' - '.config_item('company_name'));
		$data['page'] = lang('balance_sheet');
		$data['datatables'] = TRUE;
		$data['form'] = TRUE; 	
		$start_years = Date('Y', strtotime("-8 Year"));
		$current_year = date("Y");

		for ($i=$start_years; $i <=$current_year ; $i++) { 
			
			$years[]= $i;
			$end_year = $i+1;
				$financial_start_date = $i.'-04-1';
				$financial_end_date = $end_year.'-03-31';
			$this->db->select('sum(amount) as revenue');
			$this->db->where('payment_date >=', $financial_start_date);
			$this->db->where('payment_date <=', $financial_end_date);
			$revenue[$i] = $this->db->get('payments')->row()->revenue;

			$this->db->select('C.company_name as revenue_title,payments.p_id as payment_id');
			$this->db->where('payment_date >=', $financial_start_date);
			$this->db->where('payment_date <=', $financial_end_date);
			$this->db->join('companies C','C.co_id = payments.paid_by','LEFT');
			$profit[$i] = $this->db->get('payments')->result_array();

			$this->db->select('sum(amount) as gross_profit');
			$this->db->where('payment_date >=', $financial_start_date);
			$this->db->where('payment_date <=', $financial_end_date);
			$gross_profit[$i] = $this->db->get('payments')->row()->gross_profit;

			$this->db->select('C.company_name as expenses_title,expenses.notes,id');
			$this->db->where('expense_date >=', $financial_start_date);
			$this->db->where('expense_date <=', $financial_end_date);
			$this->db->where('invoiced', 0);
			$this->db->join('companies C','C.co_id = expenses.client','LEFT');
			$expenses[$i] = $this->db->get('expenses')->result_array();

			$this->db->select('sum(amount) as overall_expenses');
			$this->db->where('expense_date >=', $financial_start_date);
			$this->db->where('expense_date <=', $financial_end_date);
			$this->db->where('invoiced', 0);
			$overall_expenses[$i] = $this->db->get('expenses')->row()->overall_expenses;

			$this->db->select('(sum(tax)+sum(tax2)) as tax_amount');
			$this->db->where('due_date >=', $financial_start_date);
			$this->db->where('due_date <=', $financial_end_date);
			//$this->db->join('companies C','C.co_id = invoices.client','LEFT');
			$tax_amount[$i] = $this->db->get('invoices')->row()->tax_amount;

			// $this->db->select('sum(budget_amount) as budget_amount');
			// $this->db->where('budget_start_date >=', $financial_start_date);
			// $this->db->where('budget_end_date <=', $financial_end_date);
			// $net_earning[$i] = $this->db->get('budgets')->row()->budget_amount;

			//$g_progit[] = $gross_profit - $overall_expenses;

		}
		$all_expenses = array();
		foreach ($overall_expenses as $key => $value) {
		    $all_expenses[$key] = $overall_expenses[$key] + $tax_amount[$key];
		}

		$g_profit = array();
		foreach ($gross_profit as $key => $value) {
		    $g_profit[$key] = $gross_profit[$key] - $overall_expenses[$key];
		}

		$n_earning = array();
		foreach ($g_profit as $key => $value) {
		    $n_earning[$key] = $g_profit[$key] - $tax_amount[$key];
		}

		

		$data['years'] = $years;
		$data['years1'] = $years;
		$data['revenue'] = $revenue;
		$data['profit'] = $profit;
		$data['gross_profit'] = $g_profit;
		//$data['g_progit'] = $g_progit;
		$data['expenses'] = $expenses;
		$data['expenses1'] = $expenses;
		$data['overall_expenses'] = $all_expenses;
		$data['tax_amount'] = $tax_amount;
		$data['net_earnig'] = $n_earning;

		
		
		//echo '<pre>';print_r($ret); exit;
		//echo '<pre>';print_r($data['years']);exit;
		//print_r($this->db->last_query());
		
					
		
		$this->template

			 ->set_layout('users')

			 ->build('balance_sheet',isset($data) ? $data : NULL);
	}

	
	
}

/* End of file Social_impact.php */