<div id="reply-form" class="well">
	<?php if (Auth::instance()->logged_in()): ?>
	<form id="reply-form-content" method="post" action="<?php echo Route::url('forum-reply-action', array('action' => 'new')); ?>" enctype="multipart/form-data">
		<fieldset>
			<legend>
				<?php echo __('Post new reply'); ?>
				<small class="pull-right">回复不可编辑，请谨慎发言</small>
			</legend>
			<input type="hidden" name="topic_id" value="<?php echo $topic->id; ?>" />
			<input type="hidden" name="user_id" value="<?php echo Auth::instance()->get_user()->id; ?>" />
			<div class="comment-body">
				<?php echo View::factory('Media.Editor') ?>
			</div>
			<div class="comment-action text-right">
				<button type="submit" class="btn btn-primary"><?php echo __('Reply the topic'); ?></button>
			</div>
		</fieldset>
	</form>
	<?php else: ?>
	<h2 class="text-center"><?php echo __('You need to login at first.') ?></h2>
	<?php endif ?>
</div>
