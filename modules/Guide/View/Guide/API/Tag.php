<h4><?php echo __('Tags') ?></h4>
<ul class="tags">
<?php foreach ($tags AS $name => $set): ?>
	<li><?php echo ucfirst($name).($set ? ' - '.implode(', ', $set) : '') ?>
<?php endforeach ?>
</ul>
