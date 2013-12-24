<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Delete User') ?></legend>
		
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			
			<div class="control-group">
				<div class="controls">
					<p class="alert alert-danger">
						<strong><?php echo __('Are you sure you want to delete the user ":name"?', array(':name' => $user->username)) ?></strong>
						<span style="color:red;"><?php echo __('This is not reversible!') ?></span>
					</p>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="hidden" name="user_id" value="<?php echo $user->id ?>" />
					<button class="btn btn-danger" type="submit"><?php echo __('Yes, delete it.') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Auth')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
			
		</form>		
	</div>
</div>
