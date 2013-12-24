<h1><?php echo __('Available Classes') ?></h1>
<label><?php echo __('Filter:') ?></label>
<input type="text" id="guide-api-filter-box" />

<div class="class-list">
	<?php foreach ($classes AS $class => $methods): ?>
	<?php $link = $route->uri(array('class' => $class)) ?>
	<div class="class">
		<h2><a class="method-toggle" href="#">+</a>&nbsp;&nbsp;<?php echo HTML::anchor($link, $class, NULL, NULL, TRUE) ?></h2>
		<ul class="methods" style="display:none;">
		<?php foreach ($methods AS $method): ?>
			<li><?php echo HTML::anchor("{$link}#{$method}", "{$class}::{$method}()", NULL, NULL, TRUE) ?></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<?php endforeach; ?>
</div>
