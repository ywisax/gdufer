<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Create a New Redirect') ?></legend>
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>

			
			<div class="control-group">
				<label class="control-label" for="url"><?php echo __('Old URL') ?></label>
				<div class="controls">
					<input name="url" id="url" type="text" class="input-xxlarge" value="<?php echo $redirect->url ?>" />
					<span class="help-block"><?php echo __('When someone goes to this URL...') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="newurl"><?php echo __('New URL') ?></label>
				<div class="controls">
					<input name="newurl" id="newurl" type="text" class="input-xxlarge" value="<?php echo $redirect->newurl ?>" />
					<span class="help-block"><?php echo __('...they will be taken to this URL.') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo __('Redirect Type') ?></label>
				<div class="controls">
					<?php foreach (Model_Redirect::$_status AS $code => $name): ?>
					<label class="radio">
						<input type="radio" name="type" value="<?php echo $code ?>"<?php if ($code == $redirect->type) { echo ' checked'; } ?> />
						<?php echo __($name) ?>
					</label>
					<?php endforeach; ?>
					<span class="help-block"><?php echo __('This should be permanent (301), unless you know what you are doing.') ?></span>
				</div>
			</div>
			
			<p>
				
			</p>

			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Create Redirect') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Redirect')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
		</form>
	</div>

	<div class="span3">
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			
			<h3><?php echo __('What are redirects?') ?></h3>
			<p><?php echo __('You should add a redirect if you move a page or a site, so links on other sites do not break, and search engine rankings are preserved.<br/><br/>When a user types in the outdated link, or clicks on an outdated link, they will be taken to the new link.<br/><br/>Redirect type should be permanent (301) in most cases, as this helps to preserve search engine rankings better. Leave it as permanent unless you know what you are doing.') ?></p> 
		
		</div>
	</div>
</div>
