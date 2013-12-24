<div class="row-fluid">
	<div class="span9">
		<h2><?php echo __('CMS Admin Log') ?></h2>
		<hr />
		<table class="table">
			<thead>
				<tr>
					<th width="80"><?php echo __('ID') ?></th>
					<th><?php echo __('Operator') ?></th>
					<th><?php echo __('Content') ?></th>
					<th><?php echo __('Date') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($logs AS $log): ?>
				<tr>
					<td><?php echo $log->id ?></td>
					<td><?php echo $log->operator_name ?></td>
					<td><?php echo $log->content ?></td>
					<td><?php echo date('Y-m-d H:i:s', $log->date_created) ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination ?>
	</div>
	<?php include Kohana::find_file('View', 'XunSec.Weibo.Sidebar') ?>
</div>
