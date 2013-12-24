<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Adding :element', array(':element' => __(ucfirst($element->type())))) ?></legend>
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>

			<div class="control-group">
				<label class="control-label" for="title"><?php echo __('Title') ?></label>
				<div class="controls">
					<input name="title" type="text" class="input-xxlarge" id="title" value="<?php echo $element->title ?>" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="code"><?php echo __('Content') ?></label>
				<div class="controls">
					<textarea name="code" class="input-xxlarge" id="code"><?php echo $element->code ?></textarea>
				</div>
			</div>
	
			<div class="control-group">
				<div class="controls">
					<label for="markdown" class="checkbox inline">
						<input type="checkbox" name="markdown" id="markdown" class="check"<?php if ($element->markdown) { echo ' checked'; } ?> />
						<?php echo __('Enable Markdown') ?>
					</label>
					<label for="twig" class="checkbox inline">
						<input type="checkbox" name="twig" id="twig" class="check"<?php if ($element->twig) { echo ' checked'; } ?> />
						<?php echo __('Enable Twig') ?>
					</label>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Add Element') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array(
						'controller' => 'Page',
						'action' => 'edit',
						'params' => $page
					)) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
		</form>
	</div>
</div>
