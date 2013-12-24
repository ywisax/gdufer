<div class="row-fluid">
	<div class="span9">
		
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Edit Layout') ?></legend>
			
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>

			<div class="control-group">
				<label class="control-label" for="name"><?php echo __('Layout Name') ?></label>
				<div class="controls">
					<input name="name" type="text" id="name" class="input-xxlarge" value="<?php echo $layout->name ?>" />
					<span class="help-block"><?php echo __('This is the layout name.') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="title"><?php echo __('Layout Title') ?></label>
				<div class="controls">
					<input name="title" type="text" id="title" class="input-xxlarge" value="<?php echo $layout->title ?>" />
					<span class="help-block"><?php echo __('This is the layout title.') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="desc"><?php echo __('Layout Description') ?></label>
				<div class="controls">
					<input name="desc" type="text" id="desc" class="input-xxlarge" value="<?php echo $layout->desc ?>" />
					<span class="help-block"><?php echo __('This is the layout desc.') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="code"><?php echo __('Layout Code') ?></label>
				<div class="controls">
					<textarea name="code" id="code" class="input-xxlarge"><?php echo $layout->code ?></textarea>
					<span class="help-block"><?php echo __('This is the layout code.') ?></span>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Save Changes') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Layout')) ?>"><?php echo __('Cancel') ?></a>
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