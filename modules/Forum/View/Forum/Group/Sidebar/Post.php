<?php if (Auth::instance()->logged_in()): ?>
<a href="#broadcast-fastpost" role="button" id="new-topic-btn" class="btn btn-large btn-block btn-primary btn-success pinned" data-toggle="modal"><?php echo __('New Topic') ?></a>
<!-- Modal -->
<div id="broadcast-fastpost" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="broadcast-fastpost-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="broadcast-fastpost-label"><?php echo __('Please select a group') ?></h3>
	</div>
	<div id="broadcast-groups" class="modal-body">
		<ul class="inline">
		<?php foreach ($groups AS $group): ?>
			<li><a href="<?php echo Route::url('forum-topic-action', array('action' => 'new', 'id' => $group->id)) ?>"><?php echo $group->name ?></a></li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php else: ?>
<h2 class="text-center"><?php echo __('Post after login') ?></h2>
<?php endif; ?>
