<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Delete Snippet') ?></legend>
		
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			
			<div class="control-group">
				<div class="controls">
					<p class="alert alert-danger"><strong><?php echo __('Are you sure you want to delete the snippet ":name"?', array(':name' => $snippet->name)) ?></strong> <span style="color:red;"><?php echo __('This is not reversible!') ?></span></p>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="hidden" name="snippet_id" value="<?php echo $snippet->id ?>" />
					<button class="btn btn-danger" type="submit"><?php echo __('Yes, delete it.') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Snippet')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
			
		</form>		
	</div>
</div>
