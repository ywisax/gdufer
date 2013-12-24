<div class="row-fluid">
	<div class="span6">
		<a href="<?php echo $topic->group->link() ?>" class="btn btn-block btn-large btn-info"><i class="icon-list"></i> <?php echo $topic->group->name ?></a>
	</div>
	<div class="span6">
		<a href="<?php echo $topic->group->new_post_link() ?>" class="btn btn-block btn-large btn-info"><i class="icon-pencil"></i> 发布新话题</a>
	</div>
</div>
<hr />
<!--
<div class="panel panel-warning">
	<div class="panel-heading">信息推荐</div>
	<div class="panel-body">
		XXXX
	</div>
</div>
-->
<div class="panel panel-success">
	<div class="panel-heading">最新讨论话题</div>
	<div class="panel-body">
		<ul class="unstyled">
		<?php foreach (Model::factory('Forum.Topic')->fetch_newest() AS $topic): ?>
			<li class="topic-item topic-item-<?php echo $topic->id ?>" title="<?php echo $topic->title ?>">
				<a href="<?php echo Route::url('forum-topic', array('id' => $topic->id)) ?>"><?php echo $topic->title ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>

<div class="panel panel-success">
	<div class="panel-heading">随机推荐话题</div>
	<div class="panel-body">
		<ul class="unstyled">
		<?php foreach (Model::factory('Forum.Topic')->fetch_random() AS $topic): ?>
			<li class="topic-item topic-item-<?php echo $topic->id ?>" title="<?php echo $topic->title ?>">
				<a href="<?php echo Route::url('forum-topic', array('id' => $topic->id)) ?>"><?php echo $topic->title ?></a>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>
