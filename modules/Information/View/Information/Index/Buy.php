<div id="information-index-buy">
	<h3 class="header">图书求购</h3>
	<ul class="unstyled">
	<?php
	$topics = Model::factory('Forum.Topic')
		->where('group_id', '=', 1000017)
		->order_by('id', 'DESC')
		->limit(6)
		->find_all();
	foreach ($topics AS $topic)
	{
	?>
		<li>
			<a href="<?php echo Route::url('forum-topic', array('id' => $topic->id)) ?>" title="<?php echo $topic->title ?>"><?php echo $topic->title ?></a>
			<span class="pull-right">
				<a href="<?php echo $topic->poster->link() ?>"><?php echo $topic->poster->username ?></a>
			</span>
		</li>
	<?php
	}
	?>
	</ul>
</div>
