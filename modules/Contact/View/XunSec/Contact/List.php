<div class="row-fluid">
	<div class="span12">
		<h2><?php echo __('Contact List') ?></h2>
		<hr />
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __('ID') ?></th>
					<th><?php echo __('Realname') ?></th>
					<th><?php echo __('Mobile') ?></th>
					<th><?php echo __('Content') ?></th>
					<th><?php echo __('IP') ?></th>
					<th><?php echo __('Submit Date') ?></th>
					<th><?php echo __('Action') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($infos AS $info): ?>
				<tr>
					<td><?php echo $info->id ?></td>
					<td><?php echo $info->realname ?></td>
					<td><?php echo $info->mobile ?></td>
					<td><?php echo $info->content ?></td>
					<td><?php echo $info->ip ?></td>
					<td><?php echo date('Y-m-d H:i:s', $info->date_created) ?></td>
					<td><a onclick="return confirm('<?php echo __('Are you confirm to delete it ?') ?>')" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Contact', 'action' => 'delete', 'params' => $info->id)) ?>" class="btn btn-mini btn-danger"><?php echo __('Delete') ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination ?>
	</div>
</div>
