
<div class="content container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h4 class="page-title"><?=lang('activities')?></h4>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-4 col-sm-6 col-xs-12">
				<div class="form-group form-focus">
				<div class="ref-icon"> 
					<label class="control-label">Date From</label>
					<input class="form-control floating activity-from-datepicker" type="text" data-date-format="dd-mm-yyyy" id="ser_activity_date_from" value="" size="16">
					<i class="fa fa-refresh fa-clearicon" title="Clear To Date" onclick="$('#ser_activity_date_from').val('');$(this).parent().parent().removeClass('focused');"></i>
					<label id="ser_activity_date_from_error" class="error display-none" for="ser_activity_date_from">From Date Shouldn't be empty</label>
				</div>	
				</div>
		</div>
		<div class="col-lg-4 col-sm-6 col-xs-12">
				<div class="form-group form-focus">
				<div class="ref-icon">
					<label class="control-label">Date To</label>
					<input class="form-control floating activity-to-datepicker" type="text" data-date-format="dd-mm-yyyy" id="ser_activity_date_to" value="" size="16">
					<i class="fa fa-refresh fa-clearicon" title="Clear To Date" onclick="$('#ser_activity_date_to').val('');$(this).parent().parent().removeClass('focused');"></i>
					<label id="ser_activity_date_to_error" class="error display-none m-l-5" for="ser_activity_date_to">To Date Shouldn't be empty</label>
					</div>
				</div>
		</div>
		<div class="col-lg-4 col-sm-6 col-xs-12">
			<div class="form-group form-focus">
				<button type="button" class="btn btn-success form-control p-0" id="activity_search"> Search </button>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="activity">
				<div class="activity-box">
					<ul class="activity-list">
						<?php foreach (User::user_log(User::get_id()) as $key => $a) { 
						?>
						<li>								
							<div class="activity-user">
								<a class="avatar" href="javascript:void(0);">
									<img src="<?php echo User::avatar_url($a->user);?>" class="img-circle">
								</a>
							</div>
							<div class="activity-content">
								<div class="timeline-content">
									<a href="javascript:void(0);" class="name"><?=User::displayName($a->user)?></a>
									<?php 
									if (lang($a->activity) != '') {
										if (!empty($a->value1)) {
											if (!empty($a->value2)){
												echo sprintf(lang($a->activity), '<a href="#">'.$a->value1.'</a>', '<a href="#">'.$a->value2.'</a>');
											} else {
												echo sprintf(lang($a->activity), '<a href="#">'.$a->value1.'</a>');
											}
										} else { echo lang($a->activity); }
									} else { echo $a->activity; } 
									?>
									<span class="time"><?php echo Applib::time_elapsed_string(strtotime($a->activity_date));?></span>
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

<!-- <script>
$(".datepicker-activity").datepicker({
    todayHighlight: true,
    todayBtn: "linked",
    //autoclose: true
 }).on('hide', function(e) {
        console.log($(this).val());
        $(this).val($(this).val());
        if($(this).val() != '')
        {
        $(this).parent().parent().addClass('focused');
        }
        else
        {
        $(this).parent().parent().removeClass('focused');
        }
    });

</script> -->