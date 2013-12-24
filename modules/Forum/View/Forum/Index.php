<?php
XunSec::style('forum/css/forum.css');
XunSec::style('forum/css/forum-responsive.css');
XunSec::script('forum/js/forum.js');
?>
<div class="row-fluid">
	<div id="forum" class="span9" data-target="<?php echo Route::url('forum-group') ?>">
	</div>
	<div id="sidebar" class="span3" data-target="<?php echo Route::url('forum-sidebar') ?>">
	</div>
</div>
