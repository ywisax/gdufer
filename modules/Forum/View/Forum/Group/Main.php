<?php
XunSec::style('forum/css/forum.css');
XunSec::style('forum/css/forum-responsive.css');
XunSec::script('forum/js/forum.js');
?>
<div class="row-fluid">
	<div class="span9">
		<!-- 头部 -->
		<?php if (isset($header)): ?>
		<?php echo $header ?>
		<?php else: ?>
		<?php
		if ( ! isset($group))
		{
			$group = Model::factory('Forum.Group');
		}
		?>
		<div id="group-header" class="clearfix">
			<ul id="forum-groups" class="inline">
				<li class="title"><?php echo __('Hot Groups:') ?></li>
				<?php foreach ($groups AS $current): ?>
					<li><a class="btn <?php echo ($current->id == $group->id) ? 'btn-warning' : 'btn-info' ?>" href="<?php echo Route::url('forum-group', array('group' => $current->id, 'page' => 1)) ?>"><?php echo $current->name ?></a></li>
				<?php endforeach; ?>
				<?php if ($group->loaded()): ?>
				<li class="all"><a class="btn" href="<?php echo Route::url('forum-list') ?>"><?php echo __('All') ?></a></li>
				<?php endif; ?>
			</ul>
		<?php if ($group->loaded() AND $page == 1): ?>
			<div id="group-desc">
				<hr />
				<?php echo $group->description ?>
			</div>
		<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php include Kohana::find_file('View', 'Forum.Topic.List') ?>
	</div>
	<?php
	if (isset($sidebar))
	{
		echo $sidebar;
	}
	else
	{
		include Kohana::find_file('View', 'Forum.Group.Sidebar');
	}
	?>
</div>
