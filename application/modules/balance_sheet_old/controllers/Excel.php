<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Excel extends MX_Controller {
	

	public function __construct(){
		parent::__construct();
		$this->load->library('PHPExcel');
		
		$this->columns	=	array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');
		
		$this->borders 	=	array(
								'borders'	=>	array(
													'allborders'	=>	array(
														'style' => PHPExcel_Style_Border::BORDER_THIN)
													)
							);

		$this->center 	=	array(
								'alignment'	=>	array(
													'horizontal'	=>	PHPExcel_Style_Alignment::HORIZONTAL_CENTER
													)
							);

		$this->right 	=	array(
								'alignment'	=>	array(
													'horizontal'	=>	PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
													)
							);

		$this->left 	=	array(
							'alignment'	=>	array(
												'horizontal'	=>	PHPExcel_Style_Alignment::HORIZONTAL_LEFT
												)
							);
	}


    public function preload_excel_data($input_data=array()){
		
		
		if(isset($_REQUEST['count'])){
			$excel['count']	=	$_REQUEST['count'];
		}else{
			$excel['count']	=	0;
		}
		
		if(isset($_REQUEST['excel_title'])){
			$excel['title']	=	$_REQUEST['excel_title'];
		}else{
			$excel['title']	=	0;
		}
		
		if(isset($_REQUEST['data_count'])){
			$excel['data_count']	=	$_REQUEST['data_count'];
		}

		$this->session->set_userdata(array('excel'=>$excel));
	}
	
	public function html($input_condition=array()){
		$this->preload_excel_data($_REQUEST);
		if($_REQUEST['excel_title']=='General Category Report'){
			$this->session->set_userdata(array('excel_content'=>$_REQUEST['excel_data']));
			$html		=	$this->generate_excel();
		}else{
			$html		=	$_REQUEST['excel_data'];
		}
		
		$this->session->set_userdata(array('excel_content'=>$html));

	}
	
	public function print_excel_data(){
		//echo APPPATH;

		$excel_data			=	$this->session->set_userdata('excel');
		$excel_content		=	$this->session->set_userdata('excel_content');
		echo '<pre>';
		print_r($excel_data);
		print_r($excel_content);
	}

    /**
     * Create Creates and Downloads the EXCEL File
     *
     * @param null
     * @return null
     */
    public function index(){
		$this->load->library('PHPExcel');
		
		$excel_data			=	$this->session->userdata('excel');
		//print_r($excel_data);exit;

		// Create new PHPExcel object
		$html 				=	$this->session->userdata('excel_content');
		//print_r($html);exit;
		
		$ext	=	'.xlsx';
//		$csv_routes	=	array('Import Customer');
//		if(in_array($excel_data['title'],$csv_routes)){
//			$ext	=	'.csv';
//		}
		
		file_put_contents(str_replace("\\","/",FCPATH)."assets/excel/excel.php",$html);

		$inputFileType = 'HTML';
		$inputFileName = str_replace("\\","/",FCPATH)."assets/excel/excel.php";

		$outputFileType = 'Excel2007';
		$outputFileName = str_replace("\\","/",FCPATH)."assets/excel/".$excel_data['title'].$ext;
		$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objPHPExcelReader->load($inputFileName);

		$style_definition_array		=	array(
											'Balance Sheet'				=>	'balance_sheet',											
										);
										
		//Will call the Corresponding Style Definition Function..., So Please atleast put a empty function
		//print_r($excel_data['title']); exit;
		$style_definition_function	=	$style_definition_array[@$excel_data['title']];
		$this->$style_definition_function($objPHPExcel,$excel_data);


		header('Content-Type: text/html; charset=UTF-8');
		

		$objPHPExcelWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,$outputFileType);
		$objPHPExcel = $objPHPExcelWriter->save($outputFileName);

		// Redirect output to a client’s web browser (Excel2007)
		// @apache_setenv('no-gzip', 1);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$excel_data['title'].$ext);
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 16 Oct 1992 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		ob_clean();
		readfile($outputFileName);
		exit();			
	}

	
	
	
	public function balance_sheet($object,$input_condition=array()){
		$count	=	($input_condition['count']+3); // TOP HEADING
		$auto_width_columns		=	$this->columns;
		for($i=0;$i<=10;$i++){
			$object->getActiveSheet()->getColumnDimension($auto_width_columns[$i])->setAutoSize(true);
		}
		$object->getActiveSheet()->removeRow(3);
		$object->getActiveSheet()->getStyle('A1')->applyFromArray($this->center)->getFont()->setBold(true)->setSize(14);
		$object->getActiveSheet()->getStyle('A2')->applyFromArray($this->center)->getFont()->setBold(true)->setSize(11);
		for($i=3;$i<=$count;$i++){ // amount allignment
			
			$object->getActiveSheet()->getStyle('B'.$i.':'.'J'.$i)->applyFromArray($this->right);
			$object->getActiveSheet()->getStyle('A'.$i.':'.'J'.$i)->applyFromArray($this->borders);
		}
		
		$object->getActiveSheet()->getStyle('A'.($count-1))->getFont()->setBold(true)->setSize(12);	
		// $object->getActiveSheet()->getStyle('A'.$count.':J'.$count)->getFont()->setBold(true)->setSize(12)->applyFromArray($this->right);	
		//$object->getActiveSheet()->getStyle('A'.($count-1).':J'.($count-1))->getFont()->setBold(true)->setSize(12)->applyFromArray($this->right);		
		$object->getActiveSheet()->getStyle('A3:J3')->applyFromArray($this->center)->getFont()->setBold(true)->setSize(12);
		$object->getActiveSheet()->getStyle('A4')->getFont()->setBold(true)->setSize(12);

	}

	

	public function checked_excel(){
		$this->preload_excel_data($_REQUEST);
		set_session(array('excel_content'=>$_REQUEST['excel_data']));
		$html		=	$this->generate_excel();
		
		set_session(array('excel_content'=>$html));
	}
	
	public function generate_excel(){
		//echo '<pre>';
//		$excel_content			=	$input_data;
		if(session('current_route')=='reports.general_category_report'){
			$data	=	$this->category_report_template();
		}elseif(session('current_route')=='users.index'){
			$data	=	$this->users_list_template();
		}
		return $data;

	}
	
	public function category_report_template(){
		$this->load->model('masters/companies_model');
		$current_firm_details			=	$this->companies_model->fetch_companies(array('id'=>session('session_firm_id')));
		$firm_name						=	$current_firm_details['company_name'];
		$address1						=	$current_firm_details['address1'];
		$address2						=	$current_firm_details['address2'];
		$city							=	$current_firm_details['city'];
		$state							=	$current_firm_details['state'];
		$zip							=	$current_firm_details['zip'];
		$country						=	$current_firm_details['country'];

		$financial_year					=	session('session_financial_year');
		$start_date						=	explode(' ',$financial_year['start_date']);
		$end_date						=	explode(' ',$financial_year['end_date']);
		$report_duration				=	ymd_to_dmy($start_date[0]).' TO '.ymd_to_dmy($end_date[0]);

		$excel_content			=	session('excel_content');
		$excel_data				=	session('excel');
		
		$month_count			=	count($excel_content['months']);
		$data_count				=	count($excel_content['category']);

		$col_count				=	($month_count*2)+1;
		$row_count				=	$data_count+3;
		
		$html					=	'
									<table class="table" border="0" cellspacing="0" cellpadding="1" style=" width:100% !important;">
										<tbody>										
											<tr class="text_center">
												<td colspan="'.$col_count.'" style="text-align:center; font-weight:bold;"><b>'.$excel_data['title'].'</b></td>
											</tr>
											<tr class="text_center">
												<td colspan="'.$col_count.'" style="text-align:center; font-weight:bold; text-transform:uppercase"><b>'.$report_duration.'</b></td>
											</tr>
											<tr>
												<td colspan="'.$col_count.'" style="font-weight:bold;text-align:right;"><b>'.date('d-m-Y').'</b></td>
											</tr>	
										</tbody>
									</table>
									<table class="table" border="1" cellspacing="0" cellpadding="3" style=" width:100% !important;">
										<link rel="stylesheet" href="'.base_url().'/design/css/custom_table.css">
										<style>
											.text_center{text-align:center;}
											.text_right{text-align:right;}
										</style>
										<tbody>';


		//Start : Adding Month Name
		$html					.=	'	<tr>
											<td rowspan="2" class="border-ltb">Ledger Name</td>
									';

		foreach($excel_content['months'] as $month){
			$html				.=	'		<td class="border-ltb border-rtb text_center" style="padding:2px" colspan="2">'.$month.'</td>';
		}
		$html					.=	'	</tr>';

		$html					.=	'	<tr>
											
									';
		//End : Adding Month Name

	
		//Start : Adding STATIC COLUMN BUDGET and EXPENSE
		foreach($excel_content['months'] as $month){
			$html				.=	'		<td class="border-ltb border-rtb text_center" style="padding:2px">Budget</td>
											<td class="border-ltb border-rtb text_center" style="padding:2px">Expense</td>';
		}
		$html					.=	'	</tr>';
		//End : Adding STATIC COLUMN BUDGET and EXPENSE


		$budget					=	array();
		$expense				=	array();
		foreach($excel_content['category'] as $key=>$val ){
			$category = json_decode($val, true);
				$html				.=	'	<tr>';
				$html				.=	'		<td class="border-ltb border-rtb text_center" style="padding:2px">'.$category['name'].'</td>';
				foreach($category['detail'] as $key=>$detail){
					if($key==0 || $key%2==0){
						$budget[$key]=	isset($budget[$key])?$budget[$key]+inr_to_number($detail):inr_to_number($detail);
					}else{
						$expense[$key]=	isset($expense[$key])?$expense[$key]+inr_to_number($detail):inr_to_number($detail);
	
					}
					$html			.=	'		<td class="border-ltb border-rtb text_right" style="padding:2px">'.$detail.'</td>';
				}
				$html				.=	'	</tr>';
		}


		//Start : Adding STATIC COLUMN BUDGET and EXPENSE
		$html					.=	'	<tr>
											<td class="border-ltb border-rtb text_right" style="padding:2px">Total</td>';
		foreach($excel_content['total'] as $total){
			$html				.=	'		<td class="border-ltb border-rtb text_right" style="padding:2px">'.$total.'</td>';
		}
		$html					.=	'	</tr>';
		//End : Adding STATIC COLUMN BUDGET and EXPENSE


		$html					.=	'	
										</tbody>
									</table>';
		
		return	$html;
		//print_r($expense);
		//print_r($html);
	}
	
	public function users_list_template(){
		$this->load->model('masters/companies_model');
		$this->load->model('masters/user_groups_model');
		$this->load->model('masters/categories_model');
		$this->load->model('masters/users_model');
		
		$firms					=	$this->companies_model->fetch_companies(array('company_type'=>'firm'));
		$firm_name				=	array();
		foreach($firms as $firm){
			$firm_name[$firm['id']]	=	$firm['company_name'];
		}

		$user_groups			=	$this->user_groups_model->fetch_user_groups();
		$user_group_name		=	array();
		foreach($user_groups as $group){
			$user_group_name[$group['id']]	=	$group['user_group_name'];
		}

		$divisions			=	$this->categories_model->fetch_divisions();
		$division_name		=	array();
		foreach($divisions as $div){
			$division_name[$div['id']]	=	$div['division_name'];
		}

		$salary_details			=	$this->users_model->employee_salary_details();
		$employee_salary		=	array();
		if(count($salary_details)){
			foreach($salary_details as $salary){
				$employee_salary[$salary['user_id']]	=	$salary['net_pay'];
			}
		}






		$excel_content			=	session('excel_content');
		$excel_data				=	session('excel');
		
		$where					=	array();
		$condition['where_in'][]=	array('field'=>'us.id','value'=>$excel_content['user_id']);
		$condition['order_by']	=	'field(id,'.implode(',',$excel_content['user_id']).')';
		$condition['rows']		=	1;
		
		$users					=	fetch('users us','*',$where,$condition);
		
		$html					=	'';
		$html					.=	'<table>';
		
		$html					.=	'<thead>';
			$html				.=	'<tr>';
			$html				.=	'<th colspan="9">Employee Details</th>';
			$html				.=	'<th colspan="14"></th>';
			$html				.=	'</tr>';

			$html				.=	'<tr>';
			$html				.=	'<th>S.No</th>';
			$html				.=	'<th>Employee code</th>';
			$html				.=	'<th>Employee Name</th>';
			$html				.=	'<th>Designation</th>';
			$html				.=	'<th>Employment Status</th>';
			$html				.=	'<th>Employee of</th>';
			$html				.=	'<th>Firms</th>';
			$html				.=	'<th>Role</th>';
			$html				.=	'<th>Division</th>';
			$html				.=	'<th>DOJ</th>';
			$html				.=	'<th>Mobile</th>';
			$html				.=	'<th>Mobile 1</th>';
			$html				.=	'<th>Email</th>';
			$html				.=	'<th>DOB</th>';
			$html				.=	'<th>Gender</th>';
			$html				.=	'<th>Marital Status</th>';
			$html				.=	'<th>Address</th>';
			$html				.=	'<th>City</th>';
			$html				.=	'<th>State</th>';
			$html				.=	'<th>Pincode</th>';
			$html				.=	'<th>Country</th>';
			$html				.=	'<th>Status</th>';
			$html				.=	'<th>Salary</th>';

			$html				.=	'</tr>';
		$html					.=	'</thead>';

		$html					.=	'<tbody>';
	
		$count					=	1;
		$total_salary			=	0;
		foreach($users as $user){
			$salary				=	(isset($employee_salary[$user['id']]) && $employee_salary[$user['id']]>0)?$employee_salary[$user['id']]:0;

			$html				.=	'<tr>';
			$html				.=	'<td>'.$count.'</td>';
			$html				.=	'<td>'.$user['employee_code'].'</td>';
			$html				.=	'<td>'.$user['employee_name'].'</td>';
			$html				.=	'<td>'.$user['designation'].'</td>';
			$html				.=	'<td>'.ucwords($user['employment_status']).'</td>';
			$html				.=	'<td>'.$firm_name[$user['employee_of']].'</td>';
			
			$html				.=	'<td>';
			$assigned_firms		=	json_decode(quote_adder($user['firm_id']));
			$firm_names			=	'';
			foreach($assigned_firms as $a_f){
				$firm_names		.=	(@$firm_names!='')?' , '.$firm_name[$a_f]:@$firm_name[$a_f];
			}
			$html				.=	$firm_names;
			$html				.=	'</td>';
			
			$html				.=	'<td>'.@$user_group_name[$user['user_group_id']].'</td>';
			$html				.=	'<td>'.@$division_name[$user['division_id']].'</td>';
			$html				.=	'<td>'.ymd_to_dmy($user['doj']).'</td>';
			$html				.=	'<td>'.$user['mobile'].'</td>';
			$html				.=	'<td>'.$user['mobile1'].'</td>';
			$html				.=	'<td>'.$user['email'].'</td>';
			$html				.=	'<td>'.ymd_to_dmy($user['dob']).'</td>';
			$html				.=	'<td>'.ucwords($user['gender']).'</td>';
			$html				.=	'<td>'.ucwords($user['marital_status']).'</td>';
			$html				.=	'<td>'.$user['address'].', '.$user['address1'].'</td>';
			$html				.=	'<td>'.$user['city'].'</td>';
			$html				.=	'<td>'.$user['state'].'</td>';
			$html				.=	'<td>'.$user['pincode'].'</td>';
			$html				.=	'<td>'.$user['country'].'</td>';
			$html				.=	'<td>'.ucwords($user['status']).'</td>';
			$html				.=	'<td>'.currency_inr($salary).'</td>';

			$html				.=	'</tr>';
			$count++;
			$total_salary		+=	$salary;
		}
	
		$html					.=	'</tbody>';
		$html					.=	'<tfoot>';
			$html				.=	'<tr>';
			$html				.=	'<th colspan="22">Total Salary</th>';
			$html				.=	'<th>'.currency_inr($total_salary).'</th>';
			$html				.=	'</tr>';
		$html					.=	'</tfoot>';


		$html					.=	'</table>';
		
		//echo json_encode($users);exit;
		
		//$html	=	'<table><thead><tr><th>HTML TEMPLATE</th></tr></thead></table>';
		//echo json_encode($excel_content);
		return $html;
	}
	
	public function generate_datatable_excel(){
		$input_data	=	$_REQUEST;
		if($input_data['current_route']=='users.index'){
			$this->load->model('masters/companies_model');
			
			$firms					=	$this->companies_model->fetch_companies(array('company_type'=>'firm'));
			
			$where					=	array();
			$condition['where_in'][]=	array('field'=>'us.id','value'=>$input_data['data']);
			
			$users					=	fetch('users us','*',$where,$condition);
			
			echo json_encode($users);exit;
			
		}
		

	}

}