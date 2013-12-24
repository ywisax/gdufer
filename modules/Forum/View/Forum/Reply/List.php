<?php
$is_admin = FALSE;
if (Auth::instance()->logged_in() AND Auth::instance()->get_user()->has_role('admin'))
{
	$is_admin = TRUE;
}

$i = 1;

foreach ($replies AS $reply):
?>
<div id="reply-<?php echo $reply->id ?>" class="reply clearfix">
	<div id="author-<?php echo $reply->id ?>" class="meta">
		<a href="<?php echo Route::url('auth-action', array('action' => 'view', 'id' => $reply->poster->id)) ?>" class="avatar tooltip" data-original-title="<?php echo $reply->poster->username ?>" data-toggle="tooltip" data-placement="top"><img src="<?php echo $reply->poster->avatar_img() ?>" width="54" height="54"></a>
	</div>
	<div id="reply-content-<?php echo $reply->id ?>" class="content">
		<ul class="inline clearfix header">
			<li class="poster" data-poster="<?php echo $reply->poster->username ?>"><?php echo $reply->poster->username ?></li>
			<li class="dateline"><span class="dateline"><?php echo __('Posted at :time_ago', array(':time_ago' => Helper_Date::time_ago($reply->date_created))) ?></span></li>
			<?php if ($is_admin): ?>
			<li class="pull-right">
				<a
					onclick="return confirm('<?php echo __('Are you sure to delete this reply ?') ?>');"
					class="btn btn-mini btn-danger"
					href="<?php echo Route::url('forum-reply-action', array('action' => 'delete', 'id' => $reply->id)) ?>"
				>
					<?php echo __('Delete') ?>
				</a>
			</li>
			<?php endif; ?>
			<li class="reply-link pull-right"><a data-poster="<?php echo $reply->poster->username ?>" href="#reply-form">第<?php echo $i + $pagination->offset ?>楼</a></li>
		</ul>
		<div class="inner">
		<?php echo $reply->content; ?>
		</div>
	</div>
</div>
<?php
$i++;
endforeach;
?>
