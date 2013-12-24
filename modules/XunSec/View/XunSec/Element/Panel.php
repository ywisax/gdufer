<div class="xunsec-element-control">
	<p class="title"><?php echo $title ?></p>
	<ul class="xunsec-element-actions">
		<?php
		echo "<li>" .
			HTML::anchor(Route::url('xunsec-admin', array(
				'controller' => 'Element',
				'action' => 'edit',
				'params' => $block->id
			)),
			'<div class="fam-edit inline-sprite"></div>'.__('Edit')).
		"</li>\n";
		echo "<li>" .
			HTML::anchor(Route::url('xunsec-admin', array(
				'controller' => 'Element',
				'action' => 'moveup',
				'params' => $block->id
			)),
			'<div class="fam-up inline-sprite"></div>'.__('Move Up')).
		"</li>\n";
		echo "<li>" .
			HTML::anchor(Route::url('xunsec-admin', array(
				'controller' => 'Element',
				'action' => 'movedown',
				'params' => $block->id
			)),
			'<div class="fam-down inline-sprite"></div>'.__('Move Down')).
		"</li>\n";
		echo "<li>" .
			HTML::anchor(Route::url('xunsec-admin', array(
				'controller' => 'Element',
				'action' => 'delete',
				'params' => $block->id
			)),
			'<div class="fam-delete inline-sprite"></div>'.__('Delete')).
		"</li>\n";
		?>
	</ul>
	<div style="clear:left"></div>
</div>
