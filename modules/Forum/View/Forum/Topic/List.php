<?php if (count($topics) > 0): ?>
<ul id="topic-list" class="unstyled">
<?php foreach ($topics AS $topic): ?>
	<li class="row-fluid topic-item topic-<?php echo $topic->id ?>">
		<div class="span9 info">
			<div class="pull-left avatar-block">
			<?php echo $topic->poster->avatar_link(); ?>
			</div>
			<div class="topic-details">
				<div class="title">
					<?php
					$sticky = $topic->sticky;
					$title = $sticky ? __('[STICKY] ').$topic->title : $topic->title;
					$attr = array('class' => 'subject', 'title' => $title);
					if ($sticky)
					{
						$attr['class'] = 'subject sticky';
					}
					echo HTML::anchor(Route::url('forum-topic', array('id' => $topic->id)), $title, $attr);
					?>
				</div>
				<div class="meta">
					<a href="<?php echo Route::url('forum-group', array('group' => $topic->group->id, 'page' => 1)) ?>" class="group" target="_blank">
						<span class="label label-info">
						<?php echo $topic->group->name; ?>
						</span>
					</a>
					<a href="<?php echo Route::url('auth-action', array('action' => 'view', 'id' => $topic->poster->id)) ?>" class="author" target="_blank">
						<span class="label label-success">
						<?php echo $topic->poster->username ?>
						</span>
					</a>
					<span class="label">
					<?php echo Helper_Date::time_ago($topic->date_touched) ?>
					</span>
				</div>
			</div>
		</div>
		<div class="span3 static">
			<div class="row-fluid">
				<span class="span6 label label-gray hits">
					<?php echo $topic->hits ?>
					<small><?php echo __('Click Count') ?></small>
				</span>
				<span class="span6 label comments">
					<?php echo $topic->comments ?>
					<small><?php echo __('Comment Count') ?></small>
				</span>
			</div>
		</div>
	</li>
<?php endforeach; ?>
</ul>
<?php if (isset($pagination)): ?>
<?php echo $pagination ?>
<?php endif; ?>
<?php elseif (isset($group)): ?>
<p class="topic-list-empty"><?php echo __('No topics here, :post.', array(
	':post' => HTML::anchor(Route::url('forum-topic-action', array('action' => 'new', 'id' => $group->id)), __('post a new topic'))
	)); ?></p>
<?php else: ?>
<p class="topic-list-empty"><?php echo __('No topics now'); ?></p>
<?php endif; ?>
