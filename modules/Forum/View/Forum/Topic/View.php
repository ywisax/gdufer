<?php
XunSec::style('forum/css/forum.css');
XunSec::style('forum/css/forum-responsive.css');
XunSec::script('forum/js/forum.js');
?>
<div class="row-fluid">
	<div class="span9">
		<div class="topic topic-view clearfix" id="topic-view-<?php echo $topic->id ?>">
			<h2 class="title">
				<?php echo $topic->title ?>
			</h2>
			<div id="author-<?php echo $topic->poster->id ?>" class="meta">
				<a href="<?php echo Route::url('auth-action', array('action' => 'view', 'id' => $topic->poster->id)) ?>" class="avatar tooltip" data-original-title="<?php echo $topic->poster->username ?>" data-toggle="tooltip" data-placement="top"><img src="<?php echo $topic->poster->avatar_img() ?>" width="54" height="54"></a>
			</div>
			<div class="content">
				<ul class="inline header clearfix">
					<li class="poster" data-poster="<?php echo $topic->poster->username ?>"><?php echo $topic->poster->username ?></li>
					<?php if (Auth::instance()->logged_in() AND (Auth::instance()->get_user()->has_role('admin') OR (Auth::instance()->get_user()->id == $topic->poster->id))): ?>
					<li class="dropdown pull-right">
						<a class="dropdown-toggle btn btn-mini btn-primary" id="topic-moderator-trigger" role="button" data-toggle="dropdown" href="#"><?php echo __('Manage') ?> <b class="caret"></b></a>
						<ul id="topic-moderator-menu" class="dropdown-menu pull-right" role="menu" aria-labelledby="topic-moderator-trigger">
							<li><a href="<?php echo Route::url('forum-topic-action', array('action' => 'edit', 'id' => $topic->id)) ?>"><?php echo __('Edit topic') ?></a></li>
							<li><a href="<?php echo Route::url('forum-topic-action', array('action' => 'close', 'id' => $topic->id)) ?>"><?php echo __('Close topic') ?></a></li>
							<?php if (Auth::instance()->get_user()->has_role('admin')): ?>
								<li><a onclick="return confirm('<?php echo __('Are you sure to delte this topic ?') ?>');" href="<?php echo Route::url('forum-topic-action', array('action' => 'delete', 'id' => $topic->id)) ?>"><?php echo __('Delete topic') ?></a></li>
								<li><a onclick="return confirm('<?php echo __('Are you sure to stick this topic ?') ?>');" href="<?php echo Route::url('forum-topic-action', array('action' => 'sticky', 'id' => $topic->id)) ?>"><?php echo $topic->sticky ? __('Unstick topic') : __('Stick topic') ?></a></li>
							<?php endif; ?>
						</ul>
					</li>
					<?php endif; ?>
					<li class="dateline"><span class="dateline"><?php echo __('Posted at :time_ago', array(':time_ago' => Helper_Date::time_ago($topic->date_created))) ?></span></li>
					<li class="pull-right">
					<?php include Kohana::find_file('View', 'Forum.Topic.View.Share') ?>
					</li>
					<li class="pull-right"><a class="reply-link" href="#reply-form"><?php echo __('Reply Topic'); ?></a></li>
				</ul>
				<div class="inner">
					<?php echo $topic->content ?>
				</div>
			</div>
		</div>
		<div id="topic-reply-list-<?php echo $topic->id ?>" class="topic-reply-list">
			<?php include Kohana::find_file('View', 'Forum.Reply.List') ?>
		</div>
		<?php echo $pagination ?>
		<?php include Kohana::find_file('View', 'Forum.Reply.Write') ?>
	</div>
	<div id="forum-topic-view-sidebar" class="span3">
		<?php include Kohana::find_file('View', 'Forum.Topic.View.Sidebar') ?>
	</div>
</div>
