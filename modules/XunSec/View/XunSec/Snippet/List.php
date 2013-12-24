<div class="row-fluid">
	<div class="span9">
		<div class="box">
			<h1><?php echo __('Snippets') ?></h1>
			<hr />
			<ul class="standardlist">
			<?php
			if (count($snippets) > 0)
			{
				foreach ($snippets AS $item)
				{
				?>
					<li <?php echo Helper_Text::alternate('class="z"', '') ?> title="<?php echo __('Click to edit') ?>" >
						<div class="actions">
							<?php
							echo HTML::anchor(Route::url('xunsec-admin', array(
									'controller' => 'Snippet',
									'action' => 'edit',
									'params' => $item->id,
								)),
								'<i class="icon-edit"></i>', array('title' => __('Click to edit')));
							echo HTML::anchor(Route::url('xunsec-admin', array(
									'controller' => 'Snippet',
									'action' => 'delete',
									'params' => $item->id,
								)),
								'<i class="icon-trash"></i>', array('title' => __('Click to delete')));
							?>
						</div>
						<?php echo HTML::anchor(Route::url('xunsec-admin', array(
								'controller' => 'Snippet',
								'action' => 'edit',
								'params' => $item->id,
							)),
							'<p>'.$item->title.' <small>('.$item->name.')</small></p>') ?>
					</li>
					
				<?php
				}
				
			}
			else
			{
				echo '<li>'.__('No Snippets found.'). '</li>';
			}
			?>
			</ul>
			<div class="clear"></div>
			
		</div>
		
	</div>

	<div class="span3">
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			<p><?php echo HTML::anchor(Route::url('xunsec-admin', array('controller' => 'Snippet', 'action' => 'new')), __('Create a New Snippet'), array('class'=>'button')); ?></p>
			<p>Help goes here</p>
		</div>
	</div>
</div>