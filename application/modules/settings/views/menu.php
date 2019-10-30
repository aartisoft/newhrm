<div class="panel panel-white">
	<div class="panel-heading font-bold">
		<h3 class="panel-title"><?=lang('menu_settings')?></h3>
	</div>
	<div class="panel-body">
		<div class="row">
		<div class="col-xs-12 text-right m-b-20">
			<a href="<?php echo base_url(); ?>settings/new_menu_role/"><span class="btn btn-info">New Role</span></a>
		</div>

		<div class="col-sm-6 col-xs-12 col-md-6"> 

			<div class="form-group form-focus select-focus" style="width:100%;">

				<label class="control-label">Roles</label>

				<select class="select floating form-control" id="select_roles" name="select_roles" style="padding: 14px 9px 0px;"> 

					<option value="" selected="selected" disabled>Choose Role</option>
					<?php 
						$all_roles = $this->db->get('roles')->result_array();
						foreach($all_roles as $roles )
						{
					?>
					<option value="<?php echo $roles['role']; ?>"><?php echo ucfirst(str_replace('_',' ', $roles['role'])); ?></option>
					<?php } ?>
					
			</select>
		</div>
	</div>
</div>
		<div class="row">
		<div class="tab-content tab-content-fix MenuListRole">
			<img src="<?php echo base_url(); ?>assets/img/ajax-loader.gif" class="LoadGiFImg" style="width: 35%;margin: 0px 0px 0px 300px;display: none;" >
			
		</div>
	</div>
	</div>
</div>