<div class="row-fluid">
	<div class="span9">
		<h2><?php echo __('Authentication User') ?></h2>
		<hr />
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __('UID') ?></th>
					<th><?php echo __('Screen Name') ?></th>
					<th><?php echo __('Location') ?></th>
					<th><?php echo __('Followers Count') ?></th>
					<th><?php echo __('Friends Count') ?></th>
					<th><?php echo __('Statuses Count') ?></th>
					<th width="40"><?php echo __('Actions') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($users AS $user): ?>
			<?php $user->check_ban_expired() ?>
				<tr<?php if ($user->ban_expired) { echo ' class="error"'; } ?>>
					<td><a href="http://weibo.com/<?php echo $user->profile_url ?>" rel="popover" href="#" data-img="<?php echo $user->avatar_large ?>" target="_blank"><?php echo $user->uid ?></a></td>
					<td><?php echo $user->screen_name ?></td>
					<td><?php echo $user->location ?></td>
					<td><?php echo $user->followers_count ?></td>
					<td><?php echo $user->friends_count ?></td>
					<td><?php echo $user->statuses_count ?></td>
					<td>
						<?php if ($user->ban_expired): ?>
						<a href="#" data-reason="<?php echo __('Ban reason: :reason', array(':reason' => '垃圾广告')) ?>" data-operator="<?php echo __('Ban operator: :operator', array(':operator' => 'admin')) ?>" data-title="<?php echo __('Release date: :date', array(':date' => date('Y-m-d H:i:s', $user->ban_expired))) ?>" data-url="<?php echo Route::url('xunsec-admin', array('controller' => 'Weibo', 'action' => 'release', 'params' => $user->uid)) ?>" class="btn btn-mini btn-success release-user-button"><?php echo __('Release') ?></a>
						<?php else: ?>
						<a href="#" data-url="<?php echo Route::url('xunsec-admin', array('controller' => 'Weibo', 'action' => 'banuser', 'params' => $user->uid)) ?>" class="btn btn-mini btn-danger ban-user-button"><?php echo __('Ban') ?></a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination ?>
	</div>
	<?php include Kohana::find_file('View', 'XunSec.Weibo.Sidebar') ?>
</div>
<script type="text/javascript" src="<?php echo Media::url('bootbox/bootbox.min.js') ?>"></script>
<script>
$('a[rel=popover]').popover({
	html: true,
	trigger: 'hover',
	content: function () {
		return '<img src="'+$(this).data('img') + '" />';
	}
});
$('.release-user-button').popover({
	html: true,
	trigger: 'hover',
	title: '',
	content: function () {
		return $(this).data('title') + '<br/>' + $(this).data('operator') + '<br/>' + $(this).data('reason');
	}
});
$('.ban-user-button').click(function(){
	var url = $(this).data('url');
	bootbox.prompt("<?php echo __('Please select the ban days.') ?>", function(result) {
		if (result === null) {
			//alert('no days');
		} else {
			$.post(url, {day: result}, function(){
				window.location.href = '?r='+Math.random();
			});
		}
	});
	return false;
});
$('.release-user-button').click(function(){
	var url = $(this).data('url');
	bootbox.confirm("<?php echo __('Are you sure to release the user ?') ?>", function(result) {
		if (result) {
			$.get(url, function(){
				window.location.href = '?r='+Math.random();
			});
		}
	});
	return false;
});
</script>
