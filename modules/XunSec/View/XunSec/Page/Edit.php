<div class="row-fluid">
	<div class="span9">
		
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Editing Page:') ?> <?php echo $page->name ?></legend>
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			
			<?php if ($page->islink): ?>
				<div class="alert alert-warning"><?php echo __('This is an external link, meaning it is not actually a page managed by this system, but rather it links to a page somewhere else.  To change it to a page that you can control here, uncheck "External Link" below.') ?></div>
			<?php else: ?>
				<h3>
					<strong><?php echo __('Edit Page Content') ?></strong>
					<small class="pull-right"><?php echo HTML::anchor(Route::url('xunsec-admin', array(
							'controller' => 'Page',
							'action' => 'edit',
							'params' => $page->id
						)), __('Click to edit this page\'s content'), array('class' => 'button')) ?></small>
				</h3>
				<hr />
			<?php endif; ?>

			<div class="control-group">
				<label class="control-label" for="name"><?php echo __('Navigation Name') ?></label>
				<div class="controls">
					<input name="name" type="text" id="name" class="input-xxlarge" value="<?php echo $page->name ?>" />
					<span class="help-block"><?php echo __('This is the name that shows up in the navigation.') ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="url"><?php echo __('URL') ?></label>
				<div class="controls">
					<input name="url" type="text" id="url" class="input-xxlarge" value="<?php echo $page->url ?>" />
					<span class="help-block"><?php echo __('This is the "link" to the page, or whats in the address bar.') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="islink"><?php echo __('External Link') ?></label>
				<div class="controls">
					<label for="islink" class="checkbox">
						<input type="checkbox" class="check" id="islink" name="islink"<?php if ($page->islink) { echo ' checked'; } ?> />
						<?php echo __('Checking this will mean you can\'t edit this page here, it simply links to the URL above.') ?>
					</label>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="shownav"><?php echo __('Show in Navigation') ?></label>
				<div class="controls">
					<label for="shownav" class="checkbox">
						<input type="checkbox" class="check" id="shownav" name="shownav"<?php if ($page->shownav) { echo ' checked'; } ?> />
						<?php echo __('Check this to have this page show in the navigation menus.') ?>
					</label>
				</div>
			</div>
				
			<div class="control-group">
				<label class="control-label" for="showmap"><?php echo __('Show in Site Map') ?></label>
				<div class="controls">
					<label for="showmap" class="checkbox">
						<input type="checkbox" class="check" id="showmap" name="showmap"<?php if ($page->showmap) { echo ' checked'; } ?> />
						<?php echo __('Check this to have this page show in the site map.') ?>
					</label>
				</div>
			</div>
			
			<?php if ( ! $page->islink): ?>
			<hr/>
			<h3><?php echo __('Page Meta Data') ?></h3>

			<div class="control-group">
				<label class="control-label" for="title"><?php echo __('Title') ?></label>
				<div class="controls">
					<input name="title" id="title" type="text" class="input-xxlarge" value="<?php echo $page->title ?>" />
					<span class="help-block"><?php echo __('This is what shows up at the top of the window or tab.') ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="metakw"><?php echo __('Meta Keywords') ?></label>
				<div class="controls">
					<textarea style="height:60px;" id="metakw" class="input-xxlarge" name="metakw"><?php echo $page->metakw ?></textarea>
					<span class="help-block"><?php echo __('Keywords are used by search engines to find and rank your page.') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="metadesc"><?php echo __('Meta Description') ?></label>
				<div class="controls">
					<textarea style="height:60px;" id="metadesc" class="input-xxlarge" name="metadesc"><?php echo $page->metadesc ?></textarea>
					<span class="help-block"><?php echo __('This is used by search engines to summarize your page for visitors.') ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="layout_id"><?php echo __('Layout') ?></label>
				<div class="controls">
					<select id="layout_id" name="layout_id">
					<?php foreach ($layouts AS $layout): ?>
						<option value="<?php echo $layout->id ?>"<?php if ($layout->id == $page->layout_id) { echo ' selected'; } ?>><?php echo $layout->name ?></option>
					<?php endforeach; ?>
					</select>
					<span class="help-block"><?php echo __('Which layout this page should use.') ?></span>
				</div>
			</div>
			<?php endif; ?>
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Save Changes') ?></button>
					<a class="btn" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Page')) ?>"><?php echo __('Cancel') ?></a>
				</div>
			</div>
		</form>
	</div>

	<div class="span3">
		
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			
			<p>I need to write the help for this page.</p>
			
		</div>
		
	</div>
</div>
