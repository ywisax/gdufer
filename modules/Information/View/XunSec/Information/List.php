<div class="row-fluid">
	<div class="span9">
		<h2><?php echo __('Book List') ?></h2>
		<hr />
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __('ID') ?></th>
					<th><?php echo __('Book Name') ?></th>
					<th width="80"><?php echo __('Actions') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($records AS $record): ?>
				<tr>
					<td>
						<?php echo $record->id ?>
					</td>
					<td>
						<?php echo $record->book_name ?>
					</td>
					<td>
						<a onclick="return confirm('<?php echo __('Are you sure to delete it ?') ?>')" href="<?php echo Route::url('xunsec-admin', array('controller' => 'Information', 'action' => 'delete')) ?>" class="btn btn-mini btn-danger delete-weibo-btn"><?php echo __('Delete') ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination ?>
	</div>
	<?php include Kohana::find_file('View', 'XunSec.Information.Sidebar') ?>
</div>
<script type="text/javascript" src="<?php echo Media::url('bootbox/bootbox.min.js') ?>"></script>
