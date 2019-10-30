<div class="content">
	<?php
	$user_id = User::get_id();
	$depts = User::profile_info($user_id)->department;
	$belongs_to = json_decode($depts);
	$worked_hours = Project::staff_logged_time($user_id);
	?>
	<div class="row">
		<div class="col-sm-6 col-md-3">
			<div class="dash-widget card-box">
				<a href="<?= base_url() ?>projects" class="clear">
					<span class="dash-widget-icon"><i class="fa fa-paper-plane"></i></span>
					<div class="dash-widget-info">
						<h3><?=Applib::sec_to_hours($worked_hours)?></h3>
						<span><?=lang('worked')?></span>
					</div>
				</a>
			</div>
		</div>
		<div class="col-sm-6 col-md-3">
			<div class="dash-widget card-box">
				<a href="<?= base_url() ?>projects" class="clear">
					<span class="dash-widget-icon"><i class="fa fa-list"></i></span>
					<div class="dash-widget-info">
						<h3>
							<?php
							$this->db->join('assign_tasks', 'assign_tasks.task_assigned = tasks.t_id');
							echo $this->db->where(array('assigned_user'=>$user_id,'task_progress <'=> 100))->get('tasks')->num_rows();
							?>
						</h3>
						<span><?=lang('tasks')?></span>
					</div>
				</a>
			</div>
		</div>
		<div class="col-sm-6 col-md-3">
			<div class="dash-widget card-box">
				<a href="<?= base_url() ?>tickets" class="clear">
					<span class="dash-widget-icon"><i class="fa fa-ticket"></i></span>
					<div class="dash-widget-info">
						<h3>
							<?php
							$this->db->where('status !=','closed');
							echo $this->db->where_in('department', $belongs_to)->get('tickets')->num_rows();
							?>
						</h3>
						<span><?=lang('tickets')?></span>
					</div>
				</a>
			</div>
		</div>
		<div class="col-sm-6 col-md-3">
			<div class="dash-widget card-box">
				<a href="<?= base_url() ?>projects" class="clear">
					<span class="dash-widget-icon"><i class="fa fa-clock-o"></i></span>
					<div class="dash-widget-info">
						<h3><?php echo Project::staff_work('week',$user_id);?></h3>
						<span><?=lang('this_week')?></span>
					</div>
				</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="panel panel-table">
				<div class="panel-heading">
					<h3 class="panel-title"><?=lang('recent_projects')?></h3>
				</div>
				<div class="panel-body">
					<table class="table table-striped custom-table m-b-0">
						<thead>
							<tr>
								<th class="col-md-6"><?=lang('project_name')?> </th>
								<th class="col-md-4"><?=lang('progress')?></th>
								<th class="col-options no-sort col-md-2 text-right"><?=lang('options')?></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$recent = Welcome::recent_projects($user_id);
							$lead_check = $this->db->get_where('projects',array('assign_lead'=>$this->session->userdata('user_id')))->result();
					            if(count($lead_check) != 0){
					                $recent = $lead_check;
					            }
							foreach ($recent as $key => $project) { ?>
							<tr>
								<?php $progress = Project::get_progress($project->project_id); ?>
								<td><a href="<?=base_url()?>projects/view/<?=$project->project_id?>"><?=$project->project_title?></a></td>
								<td>
									<?php $bg = ($progress >= 100) ? 'success' : 'danger'; ?>
									<div class="progress progress-xs progress-striped m-b-0 active">
										<div class="progress-bar progress-bar-<?=$bg?>" data-toggle="tooltip" data-original-title="<?=$progress?>%" style="width: <?=$progress?>%"></div>
									</div>
								</td>
								<td class="text-right">
									<a class="btn  btn-success btn-xs" href="<?=base_url()?>projects/view/<?=$project->project_id?>">
										<i class="fa fa-folder-open-o text"></i> <?=lang('view')?>
									</a>
								</td>
							</tr>
							<?php } ?>
							<?php if(count(Welcome::recent_projects($user_id)) == 0) { ?>
							<tr>
								<td colspan="3" class="text-center text-muted"><?=lang('nothing_to_display')?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="panel-footer bg-white no-padder">
					<div class="row text-center no-gutter">
						<div class="col-xs-3 b-r b-light">
							<span class="h4 font-bold m-t block">
								<?=lang('today')?>
							</span>
							<small class="text-muted m-b block">
								<?php echo Project::staff_work('today',$user_id);?>
							</small>
						</div>
						<div class="col-xs-3 b-r b-light">
							<span class="h4 font-bold m-t block">
								<?=lang('this_week')?>
							</span>
							<small class="text-muted m-b block">
								<?php echo Project::staff_work('week',$user_id);?>
							</small>
						</div>
						<div class="col-xs-3 b-r b-light">
							<span class="h4 font-bold m-t block">
								<?=lang('this_month')?>
							</span>
							<small class="text-muted m-b block">
								<?php echo Project::staff_work('month',$user_id);?>
							</small>
						</div>
						<div class="col-xs-3">
							<span class="h4 font-bold m-t block">
								<?=lang('this_year')?>
							</span>
							<small class="text-muted m-b block">
								<?php echo Project::staff_work('year',$user_id);?>
							</small>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="panel">
				<div class="panel-heading">
					<h3 class="panel-title"><?=lang('recent_tickets')?></h3>
				</div>
				<div class="panel-body">
					<div class="slim-scroll" data-height="400" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
						<div class="list-group no-radius m-b-0">
							<?php
							$this->db->order_by('id','desc')->where('status !=','closed');
							$tickets = $this->db->where_in('department', $belongs_to)->get('tickets')->result();
							$this->load->helper('text');
							foreach ($tickets as $key => $ticket) {
							if($ticket->status == 'open'){ $badge = 'danger'; }elseif($ticket->status == 'closed'){ $badge = 'success'; }else{ $badge = 'dark'; }
							?>
								<a href="<?=base_url()?>tickets/view/<?=$ticket->id?>" data-original-title="<?=$ticket->subject?>" data-toggle="tooltip" data-placement="top" title = "" class="list-group-item">
									<small class="text-muted"><?=word_limiter($ticket->subject,5)?></small>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div class="card-box task-panel">
				<h3 class="card-title"><?=lang('my_tasks')?></h3>
				<div class="task-wrapper">
					<div class="task-list-container">
						<div class="task-list-body">
							<ul class="task-list" id="task-list">
								<?php foreach (Welcome::recent_tasks($user_id) as $key => $task) { ?>
								<li class="task <?php if($task->task_progress == '100') { echo 'completed'; } ?>">
									<div class="task-container">
										<?php if(Project::is_assigned($user_id, $task->project)) {  ?>
										<span class="task-action-btn task-check task_complete">
											<label class="checkbox-custom">
												<span class="action-circle large complete-btn">
													<input type="checkbox" data-id="<?=$task->t_id?>" <?php if($task->task_progress == '100') { echo 'checked="checked"'; } ?> <?php if($task->timer_status == 'On') { echo 'disabled="disabled"'; } ?>>
													<i class="material-icons">check</i>
												</span>
											</label>
										</span>
										<?php } ?>
										<span class="task-label">
											<a href="<?=base_url()?>projects/view/<?=$task->project?>?group=tasks&view=task&id=<?=$task->t_id?>"> <?=$task->task_name?> - 
												<small class="text-muted"><?=Project::by_id($task->project)->project_title; ?></small>
											</a>
										</span>
									</div>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- Recent activities -->
		<div class="col-md-4">
			<div class="panel activity-panel">
				<div class="panel-heading">
					<h3 class="panel-title"><?= lang('recent_activities') ?></h3>
				</div>
				<div class="panel-body">
					<div class="slim-scroll" data-height="400" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
						<ul class="activity-list">
							<?php foreach (Welcome::recent_activities($user_id) as $key => $activity) { ?>
							<li>
								<div class="activity-user">
									<a href="javascript:void(0);" class="avatar">
										<img class="img-circle" src="<?=User::avatar_url($activity->user); ?>">
									</a>
								</div>
								<div class="activity-content">
									<div class="timeline-content">
										<a href="javascript:void(0);" class="name"><?php echo User::displayName($activity->user); ?></a>
										<?php
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