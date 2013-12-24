<div class="row-fluid">
	<div class="span9">
		<h1><?php echo __('Users') ?></h1>
		<hr />
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __('UserName') ?></th>
					<th><?php echo __('Email') ?></th>
					<th><?php echo __('Regdate') ?></th>
					<th><?php echo __('LastLogin') ?></th>
					<th><?php echo __('Actions') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($users AS $user): ?>
				<tr>
					<td><a href="<?php echo Route::url('xunsec-admin', array(
									'controller' => 'User',
									'action' => 'edit',
									'params' => $user->id
								)); ?>" rel="popover" data-img="<?php echo Media::url( Model_User::avatar_path($user->id) ) ?>"><?php echo $user->username ?></a></td>
					<td><?php echo $user->email ?></td>
					<td><?php echo date('Y-m-d H:i:s', $user->created) ?></td>
					<td><?php echo date('Y-m-d H:i:s', $user->last_login) ?></td>
					<td>
						<a href="<?php echo Route::url('xunsec-admin', array(
									'controller' => 'Auth',
									'action' => 'edit',
									'params' => $user->id
								)); ?>" class="btn btn-mini btn-info"><?php echo __('Edit') ?></a>
						<a href="<?php echo Route::url('xunsec-admin', array(
									'controller' => 'Auth',
									'action' => 'ban',
									'params' => $user->id
								)); ?>" class="btn btn-mini btn-warning"><?php echo __('Ban') ?></a>
						<a href="<?php echo Route::url('xunsec-admin', array(
									'controller' => 'Auth',
									'action' => 'delete',
									'params' => $user->id
								)); ?>" class="btn btn-mini btn-danger"><?php echo __('Delete') ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination ?>
	</div>

	<div class="span3">
		<div class="box">
			<h1><?php echo __('Help') ?></h1>
			<p><?php echo HTML::anchor(Route::url('xunsec-admin', array(
				'controller' => 'User',
				'action' => 'new'
			)), __('Create a New User'), array('class' => 'btn')); ?></p>
		</div>
	</div>
</div>
<script>
$('a[rel=popover]').popover({
	html: true,
	trigger: 'hover',
	content: function () {
		return '<img src="'+$(this).data('img') + '" />';
	}
});
</script>
