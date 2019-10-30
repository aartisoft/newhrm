<div class="content">
	<div class="row">
		<div class="col-md-6 col-sm-6 col-lg-3">
			<div class="dash-widget clearfix card-box">
				<span class="dash-widget-icon"><i class="fa fa-cubes" aria-hidden="true"></i></span>
				<div class="dash-widget-info">
					<?php $projects_count = $this->db->get_where('projects',array('status'=>'Active','proj_deleted'=>'No'))->result_array(); ?>
					<h3><?php echo count($projects_count); ?></h3>
					<span>Projects</span>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-6 col-lg-3">
			<div class="dash-widget clearfix card-box">
				<span class="dash-widget-icon"><i class="fa fa-usd" aria-hidden="true"></i></span>
				<div class="dash-widget-info">
					<?php $clients_count = $this->db->get('companies')->result_array(); ?>
					<h3><?php echo count($clients_count); ?></h3>
					<span>Clients</span>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-6 col-lg-3">
			<div class="dash-widget clearfix card-box">
				<span class="dash-widget-icon"><i class="fa fa-diamond"></i></span>
				<div class="dash-widget-info">
					<?php $tasks_count = $this->db->get('tasks')->result_array(); ?>
					<h3><?php echo count($tasks_count); ?></h3>
					<span>Tasks</span>
				</div>
			</div>
		</div>
		<div class="col-md-6 col-sm-6 col-lg-3">
			<div class="dash-widget clearfix card-box">
				<span class="dash-widget-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
				<div class="dash-widget-info">
					<?php //$users_count = $this->db->get_where('users',array('role_id !='=>1,'role_id !='=>4))->result_array(); ?>
					<?php $users_count = $this->db->get_where('users',array('role_id '=>3))->result_array(); ?>
					<h3><?php echo count($users_count); ?></h3>
					<span>Employees</span>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-6 text-center">
					<div class="card-box">
						<h3 class="card-title">Invoices Status</h3>
						<div id="bar-charts"></div>
					</div>
				</div>
				<div class="col-md-6 text-center">
					<div class="card-box">
						<h3 class="card-title">Overall Status</h3>
						<div id="pie-charts"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-table inv-table">
				<div class="panel-heading">
					<h3 class="panel-title">Invoices</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped custom-table m-b-0">
							<thead>
								<tr>
									<th>Invoice ID</th>
									<th>Client</th>
									<th>Due Date</th>
									<th>Total</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									$this->db->limit(5); 
									$limit_invoices = $this->db->get('invoices')->result_array();  
									foreach($limit_invoices as $invs){
										$status = Invoice::payment_status($invs['inv_id']);
										switch ($status) {
											case 'fully_paid': $label2 = 'label-success-border';  break;
											case 'partially_paid': $label2 = 'label-warning-border'; break;
											case 'not_paid': $label2 = 'label-danger-border'; break;
											case 'cancelled': $label2 = 'label-primary-border'; break;
										}
										$client_details = $this->db->get_where('companies',array('co_id'=>$invs['client']))->row_array();
								?>
								<tr>
									<td><a href="<?php echo base_url(); ?>invoices/view/<?php echo $invs['inv_id']; ?>">#<?php echo $invs['reference_no']; ?></a></td>
									<td>
										<h2><a href="<?php echo base_url(); ?>companies/view/<?php echo $invs['client']; ?>"><?php echo $client_details['company_name']; ?></a></h2>
									</td>
									<td><?php echo date('d-M-Y',strtotime($invs['due_date'])); ?></td>
									<td><?=Applib::format_currency($invs->currency, Invoice::get_invoice_subtotal($invs['inv_id']))?></td>
									<td>
										<span class="label <?php echo $label2; ?>"><?=lang($status)?></span>
									</td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<a href="<?php echo base_url(); ?>invoices/" class="text-primary">View all invoices</a>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-table pro-table">
				<div class="panel-heading">
					<h3 class="panel-title">Recent Projects</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-striped custom-table m-b-0">
							<thead>
								<tr>
									<th class="col-md-3">Project Name </th>
									<th class="col-md-3">Progress</th>
									<!--<th class="text-right col-md-1">Action</th> -->
								</tr>
							</thead>
							<tbody>
								<?php 
								$this->db->limit(5);
								$this->db->order_by('project_id',DESC);
								$all_projects = $this->db->get('projects')->result_array(); 
								foreach($all_projects as $project){
								?>
								<tr>
									<td>
										<h2><a href="<?php echo base_url(); ?>projects/view/<?php echo $project['project_id']; ?>"><?php echo $project['project_title']; ?></a></h2>
										<small class="block text-ellipsis">
											<?php 
											$completed_task_count = $this->db->get_where('tasks',array('project'=>$project['project_id'],'task_progress'=>'100'))->result_array();
											$open_task_count = $this->db->get_where('tasks',array('project'=>$project['project_id'],'task_progress !='=>'100'))->result_array(); ?>
											<span class="text-xs"><?php echo count($open_task_count); ?></span> <span class="text-muted">open tasks, </span>
											<span class="text-xs"><?php echo count($completed_task_count); ?></span> <span class="text-muted">tasks completed</span>
										</small>
									</td>
									<td>
										<div class="progress progress-xs progress-striped">
											<div class="progress-bar bg-success" role="progressbar" data-toggle="tooltip" title="<?php echo $project['progress'].'%'; ?>" style="width: <?php echo $project['progress'].'%'; ?>"></div>
										</div>
									</td>
									<!-- <td class="text-right">
										<a href="<?php echo base_url(); ?>projects/view/<?php echo $project['project_id']; ?>" class="action-icon"><i class="fa fa-eye"></i></a>
									</td> -->
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<a href="<?php echo base_url(); ?>projects/" class="text-primary">View all projects</a>
				</div>
			</div>
		</div>
	</div>
					
					
	<div class="row" style="display: none;">
		<div class="col-sm-6 col-md-6 col-lg-3">
			<div class="dash-widget card-box card-outstanding">
				<a class="clear" href="<?= base_url() ?>reports">
					<span class="dash-widget-icon"><i aria-hidden="true" class="fa fa-paper-plane"></i></span>
					<div class="dash-widget-info">
						<h3><?php echo Applib::format_currency(config_item('default_currency'),Invoice::outstanding());?></h3>
						<span><?= lang('outstanding') ?></span>
					</div>
				</a>
			</div>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-3">
			<div class="dash-widget card-box card-expenses">
				<a class="clear" href="<?= base_url() ?>expenses">
					<span class="dash-widget-icon"><i aria-hidden="true" class="fa fa-bank"></i></span>
					<div class="dash-widget-info">
						<h3><?php echo Applib::format_currency(config_item('default_currency'),Expense::total_expenses()); ?></h3>
						<span><?=lang('expenses');?></span>
					</div>
				</a>
			</div>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-3">
			<div class="dash-widget card-box card-lastmonth">
				<a class="clear" href="<?= base_url() ?>reports">
					<span class="dash-widget-icon"><i aria-hidden="true" class="fa fa-calendar"></i></span>
					<div class="dash-widget-info">
						<h3><?php echo Applib::format_currency(config_item('default_currency'),Report::month_amount(date('Y'),date('m')-1)); ?></h3>
						<span><?= lang('last_month') ?></span>
					</div>
				</a>
			</div>
		</div>
		<div class="col-sm-6 col-md-6 col-lg-3">
			<div class="dash-widget card-box card-thismonth">
				<a class="clear" href="<?= base_url() ?>reports">
					<span class="dash-widget-icon"><i aria-hidden="true" class="fa fa-calendar-check-o"></i></span>
					<div class="dash-widget-info">
						<h3><?php echo Applib::format_currency(config_item('default_currency'),Report::month_amount(date('Y'),date('m'))); ?></h3>
						<span><?=lang('this_month') ?></span>
					</div>
				</a>
			</div>
		</div>
	</div>
	<div class="row" style="display: none;">
		<div class="col-md-12 col-sm-12 col-lg-8">
			<?php
			// Chart Variables
			$chart = ($this->session->userdata('chart')) ? $this->session->userdata('chart') : 'payments';
			$chart_year = ($this->session->userdata('chart_year')) ? $this->session->userdata('chart_year') : date('Y');
			?>
			<div class="panel panel-payments">
				<div class="panel-heading">
					<h6 class="panel-title"><?=lang($chart)?> <?= lang('yearly_overview') ?>
						<div class="m-b-sm pull-right">
							<div class="btn-group">
								<button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">Type <span class="caret"></span></button>
								<ul class="dropdown-menu pull-right">
									<li><a href="<?=base_url()?>?chart=invoiced"><?=lang('invoiced')?></a></li>
									<li><a href="<?=base_url()?>?chart=payments"><?=lang('payments')?></a></li>
									<li><a href="<?=base_url()?>?chart=projects"><?=lang('projects')?></a></li>
									<li><a href="<?=base_url()?>?chart=expenses"><?=lang('expenses')?></a></li>
								</ul>
							</div>
							<div class="btn-group">
								<button class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">Year <span class="caret"></span></button>
								<ul class="dropdown-menu pull-right">
									<?php
									$max = date('Y');
									$min = $max - 3;
									foreach (range($min, $max) as $year) { ?>
									<li><a href="<?=base_url()?>?setyear=<?=$year?>"><?=$year?></a></li>
									<?php }
									?>
								</ul>
							</div>
						</div>
					</h6>
				</div>
				<div class="panel-body">
					<div id="line-chart"></div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-12 col-sm-12">
			<div class="panel panel-invoices">
				<div class="panel-heading">
					<h6 class="panel-title"><?= lang('recently_paid_invoices') ?></h6>
				</div>
				<div class="panel-body">
					<section class="slim-scroll" data-height="215" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
						<ul class="paid-inv-list">
							<?php foreach (Payment::recent_paid() as $key => $i) {
									$currency = $i->currency;
									$badge = 'primary';
									if($i->payment_method == '1') $badge = 'success';
									elseif($i->payment_method == '2') $badge = 'danger';
									$amount = "";
									if ($currency != config_item('default_currency')) {
										$amount = Applib::format_currency(config_item('default_currency'),Applib::convert_currency($currency, $i->amount));
									}else{ 
										$amount = Applib::format_currency(config_item('default_currency'),$i->amount); }
							?>
							<li>
								<a href="<?=base_url()?>invoices/view/<?php echo $i->invoice; ?>">
									<?php echo Invoice::view_by_id($i->invoice)->reference_no;?> 
										- <small class="text-muted">
										<?php echo $amount; ?>
									<span class="badge bg-<?php echo $badge; ?> pull-right">
									<?php echo Payment::method_name_by_id($i->payment_method); ?></span></small>
								</a>
							</li>
							<?php } ?>
						</ul>
					</section>
				</div>
				<div class="panel-footer">
					<small><?= lang('total_receipts') ?>: <strong>
					<?=Applib::format_currency(config_item('default_currency'),Report::total_paid());?>
					</strong></small>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="display: none;">
		<div class="col-md-12 col-sm-12 col-lg-8">
			<div class="panel panel-projects">
				<div class="panel-body">
					<div class="slim-scroll" data-height="375" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
					<!-- TABS -->
					<?=$this->load->view('tabs');?>
					<!-- END TABS -->
					</div>	
				</div>
			</div>
		</div>
		<!-- Revenue Collection -->
		<?php
		$total_receipts = $sums['paid'];
		$invoices_cost = Invoice::all_invoice_amount();
		$outstanding = $sums['due'];
		if ($outstanding < 0) $outstanding = 0;
		$perc_paid = $perc_outstanding = 0;

		if ($invoices_cost > 0) {
			$perc_paid = ($total_receipts / $invoices_cost) * 100;
			$perc_paid = ($perc_paid > 100) ? '100': round($perc_paid, 1);
			$perc_outstanding = round(100 - $perc_paid, 1);
		}
		?>
		<div class="col-md-12 col-sm-12 col-lg-4">
			<div class="panel revenue panel-revenue">
				<div class="panel-heading">
					<h6 class="panel-title"><?=lang('revenue_collection') ?></h6>
				</div>
				<div class="panel-body text-center">
					<h4><?= lang('received_amount') ?></h4>
					<small class="text-muted block"><?=lang('percentage_collection') ?></small>

					<div class="sparkline inline" data-type="pie" data-height="150" data-slice-colors="['<?=config_item('chart_color')?>','#38354a']">
					<?= $perc_paid ?>,<?= $perc_outstanding ?></div>
					<hr>
					<div>
						<i class="fa fa-circle text-dark"></i>
						<?=lang('outstanding') ?> - <?= $perc_outstanding?>%
						<i class="fa fa-circle" style="color:<?=config_item('chart_color')?>"></i> 
						<?= lang('paid') ?> - <?= $perc_paid ?>%
					</div>
				</div>
				<div class="panel-footer">
					<small><?= lang('total_outstanding') ?> : <strong>
					<?php echo (!isset($no_invoices)) 
					? Applib::format_currency(config_item('default_currency'), $sums['due']) 
					: Applib::format_currency(config_item('default_currency'), 0);
					?>
					</strong></small>
				</div>
			</div>
		</div>
	</div>
	<div class="row" style="display: none;">
		<div class="col-lg-4 col-md-12">
			<!-- Percentage Received -->
			<div class="panel panel-table panel-tickets">
				<div class="panel-heading">
					<h3 class="panel-title"><?= lang('recent_tickets') ?></h3>
				</div>
				<div class="panel-body">
					<div class="slim-scroll" data-height="400" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
						<div class="table-responsive">
						<table class="table table-striped custom-table m-b-0 tickets-table">
							<tbody>
								<?php
								foreach (Ticket::get_tickets() as $key => $ticket) {
									$badge = 'dark';
								if($ticket->status == 'open') $badge = 'danger';
									elseif($ticket->status == 'closed') $badge = 'success';
								?>
								<tr>
									<td>
										<a class="avatar" href="<?= base_url() ?>tickets/view/<?=$ticket->id;?>">
										<?php if($ticket->reporter != NULL){ ?>
												<img src="<?php echo User::avatar_url($ticket->reporter);?>" class="img-circle">
											<?php }else{ echo "NULL"; } ?>
										</a>
										<h2>
											<a href="<?= base_url() ?>tickets/view/<?=$ticket->id;?>">
											<?php 
												echo ($ticket->reporter != NULL) 
												? User::displayName($ticket->reporter)
												: 'NULL'; 
											?>
											</a>
											<span><?=$ticket->status;?></span>
										</h2>
									</td>
									<td>
										<div class="text-muted text-size-small">
											<a href="<?= base_url() ?>tickets/view/<?=$ticket->id;?>">
												<?=$ticket->subject;?>
											</a>
										</div>
									</td>
									<td class="text-center">
										<span class="date-result"><?php echo Applib::time_elapsed_string(strtotime($ticket->created));?></span>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-12">
			<!-- Percentage Received -->
			<div class="panel panel-table panel-tasks">
				<div class="panel-heading">
					<!-- <h6 class="panel-title"><?= lang('my_tasks') ?></h6> -->
					<h6 class="panel-title">All Tasks</h6>
				</div>
				<div class="panel-body">
					<div class="slim-scroll" data-height="400" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
					<div class="table-responsive">	
					<table class="table table-striped custom-table m-b-0 task-table">
							<tbody>
								<?php
								// $tasks = array_reverse($tasks = );
									foreach (Project::user_tasks(User::get_id()) as $key => $task) {
										$badge = 'danger';
										if($task->task_progress == '100') $badge = 'success';
										elseif($task->task_progress >= '50') $badge = 'warning';
										$user = $task->added_by;
								?>
								<tr>
									<!-- <td>
										<a href="<?=base_url()?>projects/tasks/close_open/<?=$task->t_id?>">
											<?php //if($task->task_progress == '100'): ?>
											<i class="fa fa-lg fa-check-square-o text-primary"></i>
											<?php //endif; ?>
											<?php //if($task->task_progress < '100'): ?>
											<i class="fa fa-lg fa-square-o text-primary"></i>
											<?php //endif; ?>
										</a>
									</td> -->
									<td>
										<a class="avatar" href="<?= base_url() ?>projects/view/<?=$task->project;?>?group=tasks&view=task&id=<?=$task->t_id?>">
											<img src="<?php echo User::avatar_url($user);?>" class="img-circle">
										</a>
										<h2>
											<a href="<?= base_url() ?>projects/view/<?=$task->project;?>?group=tasks&view=task&id=<?=$task->t_id?>">
												<?php echo User::displayName($user);?>
											</a>
										</h2>
									</td>
									<td>
										<div class="text-muted text-size-small">
											<a href="<?= base_url() ?>projects/view/<?=$task->project;?>?group=tasks&view=task&id=<?=$task->t_id?>">
												<?=$task->task_name;?>
											</a>
										</div>
									</td>
									<td>
										<div class="text-muted text-size-small">
											<a href="<?= base_url() ?>projects/view/<?=$task->project;?>?group=tasks&view=task&id=<?=$task->t_id?>">
												<span class="badge bg-<?=$badge?>">
												<?=$task->task_progress;?>%</span>
											</a>
										</div>
									</td>
									<td class="text-center">
										<span class="date-result"><?php echo Applib::time_elapsed_string(strtotime($task->date_added));?></span>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-12">
			<div class="panel activity-panel panel-activities">
				<div class="panel-heading">
					<h6 class="panel-title"><?= lang('recent_activities') ?></h6>
				</div>
				<div class="panel-body">
					<div class="activity-box slim-scroll" data-height="370" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
						<ul class="activity-list">
						<?php foreach ($activities as $key => $activity) { ?>
							<li>
								<div class="activity-user">
									<a class="avatar" href="javascript:void(0);">
										<img src="<?php echo User::avatar_url($activity->user);?>" class="img-circle">
									</a>
								</div>
								<div class="activity-content">
									<div class="timeline-content">
										<a class="name" href="javascript:void(0);"><?php echo User::displayName($activity->user); ?></a> <?php
										if (lang($activity->activity) != '') {
											if (!empty($activity->value1)) {
												if (!empty($activity->value2)) {
													echo sprintf(lang($activity->activity), '<a href="javascript:void(0);">' . $activity->value1 . '</a>', '<a href="javascript:void(0);">' . $activity->value2 . '</a>');
												} else {
													echo sprintf(lang($activity->activity), '<a href="javascript:void(0);">' . $activity->value1 . '</a>');
												}
											} else {
												echo lang($activity->activity);
											}
										} else {
											echo $activity->activity;
										}
										?>
										<span class="time"><?php echo Applib::time_elapsed_string(strtotime($activity->activity_date)); ?></span>
									</div>
								</div>
							</li>
						<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
$this->lang->load('calendar',config_item('language')); 
$result = array(
	'Employees' => count($users_count),
	'Projects'  => count($projects_count),
	'Clients'   => count($clients_count),
	'Tasks'		=> count($tasks_count)
);
 $cr_yr = date('Y'); 


for($i=0;$i<5;$i++){
	$yrs[] = $cr_yr - $i;
}

// foreach ($yrs as $yr) {
// 	echo $yr;
// }

 ?>
<script src="<?=base_url()?>assets/js/jquery-2.2.4.min.js"></script>
<script src="<?=base_url()?>assets/js/raphael-min.js"></script>
<script src="<?=base_url()?>assets/js/morris.min.js"></script>
<!-- <script src="<?=base_url()?>assets/js/chart-<?=config_item('top_bar_color')?>.js"></script> -->

<script type="text/javascript">
<?php
$cur = App::currencies(config_item('default_currency')); 
$labels = ucfirst($chart);
$preunits = '';
if($chart != 'projects') { $labels = lang('amount'); $preunits = $cur->symbol; };?>
	Morris.Line({
		element: 'line-chart',
		data: [
<?php
for ($i = 1; $i <= 12; $i++) {
	print_r('{
		"Received Amount": ' . Applib::cal_amount($chart, $chart_year, sprintf('%02d', $i)) . ',
		"period": "' . $chart_year . '-' . sprintf('%02d', $i) . '"
	},');
};
?>
],
xkey: 'period',
ykeys: ['Received Amount'],
labels: ['<?=$labels?>'],
hoverCallback: function (index, options, content) {
return(content);
},
hideHover: 'auto',
behaveLikeLine: true,
pointFillColors: ['#fff'],
pointStrokeColors: ['black'],
xLabelMargin: 10,
xLabelAngle: 70,
preUnits: ['<?=$preunits?>'],
lineColors: ['<?=config_item('chart_color')?>'],
xLabelFormat: function (x) {
var IndexToMonth = ["<?=lang('cal_jan')?>", "<?=lang('cal_feb')?>", "<?=lang('cal_mar')?>", "<?=lang('cal_apr')?>", "<?=lang('cal_may')?>", "<?=lang('cal_jun')?>", "<?=lang('cal_jul')?>", "<?=lang('cal_aug')?>", "<?=lang('cal_sep')?>", "<?=lang('cal_oct')?>", "<?=lang('cal_nov')?>", "<?=lang('cal_dec')?>"];
var month = IndexToMonth[ x.getMonth() ];
var year = x.getFullYear();
return year + ' ' + month;
},
dateFormat: function (x) {
var IndexToMonth = ["<?=lang('cal_jan')?>", "<?=lang('cal_feb')?>", "<?=lang('cal_mar')?>", "<?=lang('cal_apr')?>", "<?=lang('cal_may')?>", "<?=lang('cal_jun')?>", "<?=lang('cal_jul')?>", "<?=lang('cal_aug')?>", "<?=lang('cal_sep')?>", "<?=lang('cal_oct')?>", "<?=lang('cal_nov')?>", "<?=lang('cal_dec')?>"];
var month = IndexToMonth[ new Date(x).getMonth() ];
var year = new Date(x).getFullYear();
return year + ' ' + month;
},
resize: true
});


	// Morris.Donut({
	// 	element: 'pie-charts',
	// 	colors: [
	// 		'#ff9b44',
	// 		'#fc6075',
	// 		'#ffc999',
	// 		'#fd9ba8'
	// 	],
	// 	data: [
	// 		<?php
	// 			foreach($result as $key=>$value){
	// 				echo '{label:"'.$key.'", value:'.$value.'},';
	// 			}
	// 		?>
	// 	],
	// 	resize: true,
	// 	redraw: true
	// });



	// Morris.Bar({
	// 	element: 'bar-charts',
	// 	data: [
	// 		{ y: '2006', a: 100, b: 90 },
	// 		{ y: '2007', a: 75,  b: 65 },
	// 		{ y: '2008', a: 50,  b: 40 },
	// 		{ y: '2009', a: 75,  b: 65 },
	// 		{ y: '2010', a: 50,  b: 40 },
	// 		{ y: '2011', a: 75,  b: 65 },
	// 		{ y: '2012', a: 100, b: 90 }
	// 	],
	// 	xkey: 'y',
	// 	ykeys: ['a', 'b'],
	// 	labels: ['Total Income', 'Total Outcome'],
	// 	lineColors: ['#ff9b44','#fc6075'],
	// 	lineWidth: '3px',
	// 	barColors: ['#ff9b44','#fc6075'],
	// 	resize: true,
	// 	redraw: true
	// });


	// Theme Orange

	if($('body').hasClass('theme-orange')){
		Morris.Donut({
			element: 'pie-charts',
			colors: [
				'#ff9b44',
				'#fc6075',
				'#ffc999',
				'#fd9ba8'
			],
			data: [
			<?php
				foreach($result as $key=>$value){
					echo '{label:"'.$key.'", value:'.$value.'},';
				}
			?>
			],
			resize: true,
			redraw: true
		});




		Morris.Bar({
			element: 'bar-charts',
			data: [
				{ y: '2013', a: 100, b: 90 },
				{ y: '2014', a: 75,  b: 65 },
				{ y: '2015', a: 50,  b: 40 },
				{ y: '2016', a: 75,  b: 65 },
				{ y: '2017', a: 50,  b: 40 },
				{ y: '2018', a: 75,  b: 65 },
				{ y: '2019', a: 100, b: 90 }
			],
			xkey: 'y',
			ykeys: ['a', 'b'],
			labels: ['Total Invoice', 'Pending Invoice'],
			lineColors: ['#ff9b44','#fc6075'],
			lineWidth: '3px',
			barColors: ['#ff9b44','#fc6075'],
			resize: true,
			redraw: true
		});


	}

	// Theme White

	if($('body').hasClass('theme-white')){
		Morris.Donut({
			element: 'pie-charts',
			colors: [
				'#ff9b44',
				'#fc6075',
				'#ffc999',
				'#fd9ba8'
			],
			data: [
			<?php
				foreach($result as $key=>$value){
					echo '{label:"'.$key.'", value:'.$value.'},';
				}
			?>
			],
			resize: true,
			redraw: true
		});


		Morris.Bar({
			element: 'bar-charts',
			data: [
				{ y: '2013', a: 100, b: 90 },
				{ y: '2014', a: 75,  b: 65 },
				{ y: '2015', a: 50,  b: 40 },
				{ y: '2016', a: 75,  b: 65 },
				{ y: '2017', a: 50,  b: 40 },
				{ y: '2018', a: 75,  b: 65 },
				{ y: '2019', a: 100, b: 90 }
			],
			xkey: 'y',
			ykeys: ['a', 'b'],
			labels: ['Total Invoice', 'Pending Invoice'],
			lineColors: ['#ff9b44','#fc6075'],
			lineWidth: '3px',
			barColors: ['#ff9b44','#fc6075'],
			resize: true,
			redraw: true
		});
	}

// Theme Blue

	if($('body').hasClass('theme-blue')){
		Morris.Donut({
			element: 'pie-charts',
			colors: [
				'#00c5fb',
				'#0253cc',
				'#80e3ff',
				'#81b3fe'
			],
			data: [
			<?php
				foreach($result as $key=>$value){
					echo '{label:"'.$key.'", value:'.$value.'},';
				}
			?>
			],
			resize: true,
			redraw: true
		});


		Morris.Bar({
			element: 'bar-charts',
			data: [
				{ y: '2013', a: 100, b: 90 },
				{ y: '2014', a: 75,  b: 65 },
				{ y: '2015', a: 50,  b: 40 },
				{ y: '2016', a: 75,  b: 65 },
				{ y: '2017', a: 50,  b: 40 },
				{ y: '2018', a: 75,  b: 65 },
				{ y: '2019', a: 100, b: 90 }
			],
			xkey: 'y',
			ykeys: ['a', 'b'],
			labels: ['Total Invoice', 'Pending Invoice'],
			lineColors: ['#00c5fb','#0253cc'],
			lineWidth: '3px',
			barColors: ['#00c5fb','#0253cc'],
			resize: true,
			redraw: true
		});
	}

	// Theme Maroon

	if($('body').hasClass('theme-maroon')){
		Morris.Donut({
			element: 'pie-charts',
			colors: [
				'#f43b48',
				'#453a94',
				'#f8878e',
				'#aaa3db'
			],
			data: [
			<?php
				foreach($result as $key=>$value){
					echo '{label:"'.$key.'", value:'.$value.'},';
				}
			?>
			],
			resize: true,
			redraw: true
		});


		Morris.Bar({
			element: 'bar-charts',
			data: [
				{ y: '2013', a: 100, b: 90 },
				{ y: '2014', a: 75,  b: 65 },
				{ y: '2015', a: 50,  b: 40 },
				{ y: '2016', a: 75,  b: 65 },
				{ y: '2017', a: 50,  b: 40 },
				{ y: '2018', a: 75,  b: 65 },
				{ y: '2019', a: 100, b: 90 }
			],
			xkey: 'y',
			ykeys: ['a', 'b'],
			labels: ['Total Invoice', 'Pending Invoice'],
			lineColors: ['#f43b48','#453a94'],
			lineWidth: '3px',
			barColors: ['#f43b48','#453a94'],
			resize: true,
			redraw: true
		});
	}


	// Theme Purple

	if($('body').hasClass('theme-purple')){
		Morris.Donut({
			element: 'pie-charts',
			colors: [
				'#667eea',
				'#764ba2',
				'#a5b3f3',
				'#bfa8d7'
			],
			data: [
			<?php
				foreach($result as $key=>$value){
					echo '{label:"'.$key.'", value:'.$value.'},';
				}
			?>
			],
			resize: true,
			redraw: true
		});


		Morris.Bar({
			element: 'bar-charts',
			data: [
				{ y: '2013', a: 100, b: 90 },
				{ y: '2014', a: 75,  b: 65 },
				{ y: '2015', a: 50,  b: 40 },
				{ y: '2016', a: 75,  b: 65 },
				{ y: '2017', a: 50,  b: 40 },
				{ y: '2018', a: 75,  b: 65 },
				{ y: '2019', a: 100, b: 90 }
			],
			xkey: 'y',
			ykeys: ['a', 'b'],
			labels: ['Total Invoice', 'Pending Invoice'],
			lineColors: ['#667eea','#764ba2'],
			lineWidth: '3px',
			barColors: ['#667eea','#764ba2'],
			resize: true,
			redraw: true
		});
	}

	// Theme Purple

	if($('body').hasClass('theme-dark')){
		Morris.Donut({
			element: 'pie-charts',
			colors: [
				'#ff9b44',
				'#fc6075',
				'#ffc999',
				'#fd9ba8'
			],
			data: [
			<?php
				foreach($result as $key=>$value){
					echo '{label:"'.$key.'", value:'.$value.'},';
				}
			?>
			],
			resize: true,
			redraw: true
		});


		Morris.Bar({
			element: 'bar-charts',
			data: [
				{ y: '2013', a: 100, b: 90 },
				{ y: '2014', a: 75,  b: 65 },
				{ y: '2015', a: 50,  b: 40 },
				{ y: '2016', a: 75,  b: 65 },
				{ y: '2017', a: 50,  b: 40 },
				{ y: '2018', a: 75,  b: 65 },
				{ y: '2019', a: 100, b: 90 }
			],
			xkey: 'y',
			ykeys: ['a', 'b'],
			labels: ['Total Invoice', 'Pending Invoice'],
			lineColors: ['#ff9b44','#fc6075'],
			lineWidth: '3px',
			barColors: ['#ff9b44','#fc6075'],
			resize: true,
			redraw: true
		});
	}


</script>