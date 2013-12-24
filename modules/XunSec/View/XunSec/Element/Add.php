<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
		<legend><?php echo __('Adding :element', array(':element' => __(ucfirst($element->type())))) ?></legend>
		<?php include Kohana::find_file('View', 'XunSec.Error') ?>
		
		<?php foreach ($element->inputs() AS $label => $input): ?>
		<p>
			<label><?php echo __($label) ?></label>
			<?php echo $input ?>
		</p>
		<?php endforeach; ?>

			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Add Element') ?></button>
					<?php echo HTML::anchor(Route::url('xunsec-admin', array(
						'controller' => 'Page',
						'action' => 'edit',
						'params' => $page
					)), __('cancel')) ?>
				</div>
			</div>
		</form>
	</div>
</div>