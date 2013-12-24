<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Delete Page') ?></legend>
		
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			
			<div class="control-group">
				<div class="controls">
					<p class="alert alert-danger"><strong><?php echo __('Are you sure you want to delete the page ":page"?', array(':page' => $page->name)) ?> <span style='color:red;font-weight:bold'><?php echo __('This is not reversible!') ?><span></strong></p>
					<?php if ($page->has_children()): ?>
					<p style="color:red;font-weight:bold;"><?php echo __('This page has children. Deleting it will delete all children too. Are you really sure you want to do this?') ?></p>
					<?php endif; ?>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="hidden" name="page_id" value="<?php echo $page->id ?>" />
					<button class="btn btn-danger" type="submit"><?php echo __('Yes, delete it.') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Page')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
		</form>		
	</div>
</div>
