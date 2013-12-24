<div class="row-fluid">
	<div class="span9">
		<form class="form-horizontal" method="post">
			<legend><?php echo __('Adding New Page') ?></legend>
			<p class="alert alert-info"><?php echo __('Adding a sub page to ":page".', array(':page' => $parent->name)) ?></p>
			<?php include Kohana::find_file('View', 'XunSec.Error') ?>
			<div class="control-group">
				<label class="control-label" for="location"><?php echo __('Location') ?></label>
				<div class="controls">
					<select id="location" name="location">
						<option value="first"><?php echo __('First Child') ?></option>
						<?php foreach ( $parent->children() AS $child): ?>
						<option value="<?php echo $child->id ?>"><?php echo __('After :child', array(':child' => $child->name)) ?></option>
						<?php endforeach; ?>
						<option value="last" selected="selected"><?php echo __('Last Child') ?></option>
					</select>
					<span class="help-block"><?php echo __('Where in the list of siblings this page will appear.') ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="name"><?php echo __('Navigation Name') ?></label>
				<div class="controls">
					<input type="text" name="name" id="name" value="<?php echo $page->name ?>" />
					<span class="help-block"><?php echo __('This is the name that shows up in the navigation.') ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="url"><?php echo __('URL') ?></label>
				<div class="controls">
					<input type="text" name="url" id="url" class="input-xxlarge" value="<?php echo $page->url ?>" />
					<span class="help-block"><?php echo __('This is the "link" to the page, or whats in the address bar.') ?></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="islink"><?php echo __('External Link') ?></label>
				<div class="controls">
					<label for="islink" class="checkbox">
						<input type="checkbox" id="islink" name="islink"<?php if ($page->islink) { echo ' checked'; } ?> />
						<?php echo __('Checking this will mean you can\'t edit this page here, it simply links to the URL above.') ?>
					</label>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="shownav"><?php echo __('Show in Navigation') ?></label>
				<div class="controls">
					<label for="shownav" class="checkbox">
						<input type="checkbox" id="shownav" name="shownav"<?php if ($page->shownav) { echo ' checked'; } ?> />
						<?php echo __('Check this to have this page show in the navigation menus.') ?>
					</label>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="showmap"><?php echo __('Show in Site Map') ?></label>
				<div class="controls">
					<label for="showmap" class="checkbox">
						<input type="checkbox" id="showmap" name="showmap"<?php if ($page->showmap) { echo ' checked'; } ?> />
						<?php echo __('Check this to have this page show in the site map.') ?>
					</label>
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
			
			<div class="control-group">
				<div class="controls">
					<button class="btn btn-primary" type="submit"><?php echo __('Create Page') ?></button>
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

