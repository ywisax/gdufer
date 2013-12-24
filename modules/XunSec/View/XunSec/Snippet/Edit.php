<div class="row-fluid">
	<div class="span9">

		<form class="form-horizontal" method="post">
			<legend><?php echo __('Edit Snippet') ?></legend>
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			
			<div class="control-group">
				<label class="control-label" for="title"><?php echo __('Snippet Title') ?></label>
				<div class="controls">
					<input name="title" type="text" id="title" class="input-xxlarge" value="<?php echo $snippet->title ?>" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="name"><?php echo __('Name') ?></label>
				<div class="controls">
					<input name="name" type="text" id="name" class="input-xxlarge" value="<?php echo $snippet->name ?>" />
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="code"><?php echo __('Content') ?></label>
				<div class="controls">
					<textarea name="code" class="input-xxlarge" id="code"><?php echo $snippet->code ?></textarea>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<label for="markdown" class="checkbox inline">
						<input type="checkbox" name="markdown" id="markdown" class="check"<?php if ($snippet->markdown) { echo ' checked'; } ?> />
						<?php echo __('Enable Markdown') ?>
					</label>
					<label for="twig" class="checkbox inline">
						<input type="checkbox" name="twig" id="twig" class="check"<?php if ($snippet->twig) { echo ' checked'; } ?> />
						<?php echo __('Enable Twig') ?>
					</label>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Save Changes') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Snippet')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
		</form>

	</div>

	<div class="span3">
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			<p>Help goes here</p>
		</div>
	</div>
</div>