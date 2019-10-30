<div class="content container-fluid">
					<div class="row">
						<div class="col-sm-8">
							<h4 class="page-title">Attendance</h4>
						</div>
					</div>
					
					<!-- Search Filter -->
					<div class="row filter-row">
						<div class="col-sm-3 col-xs-6">  
							<div class="form-group form-focus">
								<input type="text" class="form-control floating" name="employee_name" id="employee_name" value="" />
		                        <input type="hidden"  name="employee_id" id="employee_id" value="0" />
								<label class="control-label">Employee Name</label>
							</div>
						</div>
						<div class="col-sm-3 col-xs-6"> 
							<?php 
							$s_year = '2015';
							$select_y = date('Y');

							$s_month = date('m');
							$e_year = date('Y');
						 ?>
							<div class="form-group form-focus select-focus">
								<select class="select floating form-control" id="attendance_month" name="attendance_month">  
								<option value="" selected="selected" disabled>Select Month</option>
								<?php 
									for ($ji=1; $ji <=12 ; $ji++) {  ?>
									<option value="<?php echo $ji; ?>" <?php echo ($s_month==$ji)?'selected':''; ?>><?php echo date('F',strtotime($select_y.'-'.$ji)); ?></option>		
									<?php } ?>
								
							</select>
								<label class="control-label">Select Month</label>
							</div>
						</div>
						<div class="col-sm-3 col-xs-6"> 
							<div class="form-group form-focus select-focus">
								<select class="select floating form-control" id="attendance_year" name="attendance_year"> 
									<option value="" selected="selected" disabled>Select Year</option>
									<?php for($k =$e_year;$k>=$s_year;$k--){ ?>
									<option value="<?php echo $k; ?>" <?php echo ($select_y==$k)?'selected':''; ?> ><?php echo $k; ?></option>
									<?php } ?>
								</select>
								<label class="control-label">Select Year</label>
							</div>
						</div> 
						<div class="col-sm-3 col-xs-6">  
							<a href="#" class="btn btn-success btn-block" onclick="attendance_next_filter_page(1)"> Search </a>  
						</div>     
                    </div>
					<!-- /Search Filter -->
					
                    <div class="row">
                        <div class="col-lg-12">

                        		<div id="attendance_table">
                        			
                        		</div>

							
                        </div>
                    </div>

                  