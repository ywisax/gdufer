<h1><?php echo __('User Guide') ?></h1>

<p><?php echo __('The following modules have userguide pages:') ?></p>

<?php if( ! empty($modules)): ?>

	<?php foreach ($modules AS $url => $options): ?>
	
		<p>
			<strong><?php echo html::anchor(Route::get('guide-doc')->uri(array('module' => $url)), $options['name'], NULL, NULL, TRUE) ?></strong> -
			<?php echo $options['description'] ?>
		</p>
	
	<?php endforeach; ?>
	
<?php else: ?>

	<p class="error"><?php echo __("I couldn't find any modules with userguide pages.") ?></p>

<?php endif; ?>