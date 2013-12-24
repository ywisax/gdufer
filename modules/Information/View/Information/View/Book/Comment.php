<ul class="comment-list media-list unstyled">
<?php
$comments = $model->all_comments();
if (count($comments))
{
	foreach ($comments AS $comment)
	{
	?><li class="media">
		<a class="pull-left" href="<?php echo $comment->poster->link() ?>">
			<img class="media-object avatar" width="32" height="32" src="<?php echo $comment->poster->avatar_img() ?>">
		</a>
		<div class="media-body">
			<strong><?php echo $comment->poster_name ?></strong>
			<span>(<?php echo Helper_Date::time_ago($comment->date_created) ?>)</span>: <?php echo $comment->content ?>
			<button class="reply btn btn-mini btn-warning">回复</button>
		</div>
	</li><?php
	}
}
else
{
	?><li class="empty">暂时还没有人评论喔，你可以争取做第一个！</li><?php
}
?>
</ul>
<div class="comment-editor well">
	<?php if (Auth::instance()->logged_in()): ?>
	<form method="post" action="<?php echo Route::url('information-action', array('action' => 'comment', 'type' => $model->type(), 'id' => $model->id)) ?>">
		<h5>提交你的评论或意见</h5>
		<textarea name="content"></textarea>
		<input class="reply" type="hidden" name="reply_id" value="0" />
		<button type="submit" class="btn btn-info btn-middle">提交</button>
	</form>
	<?php else: ?>
	<h3 class="text-center">你需要先登陆才能进行评论</h3>
	<?php endif; ?>
</div>
