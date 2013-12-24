<div id="information-banner" class="row-fluid">
	<div class="span4">
		<form id="information-banner-search" class="form-search" method="post" action="<?php echo Route::url('information-action', array('action' => 'search')) ?>">
			<div class="input-append">
				<input type="text" name="keyword" class="span2 search-query" placeholder="输入你需要的关键词" />
				<button type="submit" class="btn btn-large btn-success">搜索</button>
			</div>
		</form>
	</div>
	<div class="span8">
		<div class="row-fluid">
			<div class="span3">
				<a class="btn btn-large btn-block btn-info" href="<?php echo Route::url('forum-group', array('group' => 1000017)) ?>">我在找书</a>
			</div>
			<div class="span3">
				<a class="btn btn-large btn-block btn-success" href="<?php echo Route::url('information-action', array('action' => 'submit')) ?>">我要卖书</a>
			</div>
			<div class="span3">
				<a class="btn btn-large btn-block btn-primary" href="<?php echo Route::url('information-action', array('type' => 'book', 'action' => 'list', 'id' => 1)) ?>">在售书本</a>
			</div>
			<div class="span3">
				<a class="btn btn-large btn-block btn-warning" href="<?php echo URL::site('forum/group-1000017/1.html') ?>">论坛帮助</a>
			</div>
		</div>
	</div>
</div>
