<div class="row-fluid">
	<div class="span10 offset1">
		<h4>
			关键词：<?php echo $keyword ?>
			<small class="pull-right">只返回最新的20条记录</small>
		</h4>
		<hr />
	</div>
</div>
<?php include Kohana::find_file('View', 'Information.List.Book') ?>
