<?php XunSec::style('information/css/common.css') ?>
<?php XunSec::script('information/js/index.js') ?>
<div class="row-fluid">
	<div class="span10 offset1">
		<ul id="book-list">
		<?php $is_admin = Auth::instance()->logged_in()
			? (Auth::instance()->get_user()->has_role('admin'))
			: FALSE;
		?>
		<?php foreach ($records AS $record): ?>
			<li>
				<div class="media">
					<a class="pull-left" target="_blank" href="<?php echo $record->link(); ?>">
						<img class="media-object" src="<?php echo $record->image_url() ?>">
					</a>
					<div class="media-body">
						<h4 class="media-heading">
							<a href="<?php echo $record->link() ?>" target="_blank"><?php echo $record->book_name ?></a>
							<?php if ($is_admin): ?>
							<small><a href="<?php echo Route::url('information-action', array('type' => $record->type(), 'action' => 'delete', 'id' => $record->id)) ?>"><?php echo __('Delete') ?></a></small>
							<?php endif; ?>
						</h4>
						<hr />
						<div class="row-fluid">
							<div class="span3">作者：<span class="label label-success"><?php echo $record->book_author ?></span></div>
							<div class="span3">原价：<span class="label label-success">￥<?php echo $record->raw_price ?></span></div>
							<div class="span6">出版社：<span class="label label-success"><?php echo $record->publisher ?></span></div>
						</div>
						<hr />
						<div class="row-fluid">
							<div class="span2">
								分类：<span class="label label-success"><?php echo $record->category->name ?></span>
							</div>
							<div class="span4">
								<div id="quality-selector" class="btn-group">
								<?php foreach ($record->quality_selector AS $val => $name): ?>
									<button type="button" class="btn<?php echo $val == $record->quality ? ' active' : '' ?>" data-quality="<?php echo $val ?>"><?php echo $name ?></button>
								<?php endforeach; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</li>
		<?php endforeach; ?>
		</ul>
		<?php echo isset($pagination) ? $pagination : '' ?>
	</div>
</div>
