<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Delete :element', array(':element' => __(ucfirst($element->type())))) ?></legend>
		
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			
			<div class="control-group">
				<div class="controls">
					<p class="alert alert-danger"><strong><?php echo __('Are you sure you want to delete this element?') ?></strong></p>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<input type="hidden" name="element_id" value="<?php echo $element->id ?>" />
					<button class="btn btn-danger" type="submit"><?php echo __('Yes, delete it.') ?></button>
					<?php echo HTML::anchor(Route::url('xunsec-admin', array(
						'controller' => 'Page',
						'action' => 'edit',
						'params' => $element->block->page->id,
					)), __('cancel')); ?>
				</div>
			</div>
			
		</form>		
	</div>
</div>