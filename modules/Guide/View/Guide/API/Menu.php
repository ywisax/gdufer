<h2><?php echo __('Modules') ?></h2>
<ol class="menu">
<?php foreach ($menu AS $package => $categories): ksort($categories); ?>
<li><span><strong><?php echo $package ?></strong></span>
	<ol>
	<?php foreach ($categories AS $category => $classes): sort($classes); ?>
		<li><?php if ($category !== 'Base'): ?><span><?php echo $category ?></span>
			<ol><?php endif ?>
			<?php foreach ($classes AS $class): ?>
				<li><?php echo $class ?></li>
			<?php endforeach ?>
			<?php if ($category !== 'Base'): ?></ol><?php endif ?>
		</li>
	<?php endforeach ?>
	</ol>
<?php endforeach ?>
</ol>