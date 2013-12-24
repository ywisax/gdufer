<div class="row-fluid">
	<div class="span9">
		<div class="box">
			<h2><?php echo __('Layouts') ?></h2>
			<hr />
			<ul id="layoutlist" class="standardlist">
			<?php
			if (count($layouts) > 0)
			{
				foreach ($layouts AS $item)
				{
				?>
					<li <?php echo Helper_Text::alternate('class="z"', '')?> title="<?php echo __('Click to edit')?>" >
						<div class="actions">
							<?php
							echo HTML::anchor(Route::url('xunsec-admin', array('controller' => 'Layout', 'action' => 'edit', 'params' => $item->id)),
								 '<i class="icon-edit"></i>', array('title' => __('Click to edit')));
							echo HTML::anchor(Route::url('xunsec-admin', array('controller' => 'Layout', 'action' => 'delete', 'params' => $item->id)),
								 '<i class="icon-trash"></i>', array('title' => __('Click to delete')));
							?>
						</div>
						<?php
						echo
						HTML::anchor(Route::url('xunsec-admin', array('controller' => 'Layout', 'action' => 'edit', 'params' => $item->id)),
									 '<p>' . $item->title . '&nbsp;&nbsp;<small>(' . $item->desc . ')</small></p>');
						?>
					</li>
				<?php
			}
			else
			{
				echo '<li>'.__('No layouts found').'</li>';
			}
			?>
			</ul>
		</div>
	</div>
	<div class="span3">
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			<p><?php echo HTML::anchor(Route::url('xunsec-admin', array('controller' => 'Layout', 'action' => 'new')),__('Create a New Layout'), array('class' => 'btn btn-primary')); ?></p>
			<p>Help goes here</p>
		</div>
	</div>
</div>
