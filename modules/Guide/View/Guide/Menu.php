<h2><?php echo __('Modules') ?></h2>
<?php if( ! empty($modules)): ?>
	<ul>
	<?php foreach ($modules AS $url => $options): ?>
		<li><?php echo html::anchor(Route::get('guide-doc')->uri(array('module' => $url)), $options['name'], NULL, NULL, TRUE) ?></li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<p class="error"><?php echo __('No modules.') ?></p>
<?php endif; ?>
