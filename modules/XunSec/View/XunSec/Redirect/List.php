<div class="row-fluid">
	<div class="span9">
		<div class="box">
			<h1><?php echo __('Redirects') ?></h1>
			<hr />
			<ul class="standardlist">
			<?php
			if (count($redirects) > 0)
			{
				foreach ($redirects AS $item)
				{
				?>
					<li <?php echo Helper_Text::alternate('class="z"', '') ?> title="<?php echo __('Click to edit') ?>" >
						<div class='actions'>
							<?php
							echo HTML::anchor($item->url,
								 '<i class="icon-wrench"></i>', array('title' => __('Click to test')));
							echo HTML::anchor(Route::url('xunsec-admin', array(
									'controller' => 'Redirect',
									'action' => 'edit',
									'params' => $item->id
								)),
								'<i class="icon-edit"></i>', array('title' => __('Click to edit')));
							echo HTML::anchor(Route::url('xunsec-admin', array(
									'controller' => 'Redirect',
									'action' => 'delete',
									'params' => $item->id
								)),
								'<i class="icon-trash"></i>', array('title' => __('Click to delete')));
							?>
						</div>
						<?php
						echo
						HTML::anchor(Route::url('xunsec-admin', array(
								'controller' => 'Redirect',
								'action' => 'edit',
								'params' => $item->id
							)),
							"<p>" . $item->url .
							'&nbsp;&nbsp;&nbsp;<i class="icon-arrow-right"></i>&nbsp;&nbsp;&nbsp;' .
							$item->newurl .
							"&nbsp;&nbsp;<span class=\"label label-info\">" .
								(($item->type == '301') ? __('permanent').' (301)' : '').
								(($item->type == '302') ? __('temporary').' (302)' : '').
							"</span>" .
							"</p>"
						);
						?>
					</li>
					
				<?php
				}
			}
			else
			{
				echo '<li><p>'.__('No redirects found').'</p></li>';
			}
			?>
			</ul>
			</div>
		</div>
		
	<div class="span3">
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			<p><?php echo HTML::anchor(Route::url('xunsec-admin', array('controller' => 'Redirect', 'action' => 'new')), __('Create a New Redirect'), array('class' => 'btn')); ?></p>
			<h3><?php echo __('What are redirects?') ?></h3>
			<p><?php echo __('You should add a redirect if you move a page or a site, so links on other sites do not break, and search engine rankings are preserved.<br/><br/>When a user types in the outdated link, or clicks on an outdated link, they will be taken to the new link.<br/><br/>Redirect type should be permanent (301) in most cases, as this helps to preserve search engine rankings better. Leave it as permanent unless you know what you are doing.') ?></p>
		</div>
	</div>
</div>
