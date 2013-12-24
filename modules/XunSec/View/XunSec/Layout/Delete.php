<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Delete Layout') ?></legend>
		
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			
			<div class="control-group">
				<div class="controls">
					<p class="alert alert-danger"><strong><?php echo __('Are you sure you want to delete the layout ":name"?', array(':name' => $layout->name))?> <span style="color:red;"><?php echo __('This is not reversible!') ?></span></strong></p>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="hidden" name="layout_id" value="<?php echo $layout->id ?>" />
					<button class="btn btn-danger" type="submit"><?php echo __('Yes, delete it.') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Layout')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
			
		</form>		
	</div>
</div>
