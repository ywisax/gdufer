<form id="forum-search-widget" class="form-search" method="post" action="<?php echo Route::url('forum-search') ?>">
	<input type="text" name="keyword" class="input-medium search-query" placeholder="搜索话题">
	<button type="submit" class="btn search-submit"><?php echo __('Froum Search') ?></button>
</form>
