<ul id="weibo-smile-list" class="clearfix">
<?php foreach ($emotions AS $emotion): ?>
	<li><img title="<?php echo str_replace(array('[', ']'), '', $emotion['phrase']) ?>" src="<?php echo $emotion['url'] ?>" data-emotion="<?php echo $emotion['phrase'] ?>" /></li>
<?php endforeach; ?>
</ul>
