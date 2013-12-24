<?php
XunSec::style('forum/css/forum.css');
XunSec::style('forum/css/forum-responsive.css');
XunSec::script('forum/js/forum.js');
?>
<div class="row-fluid">
	<div class="span10 offset1">
		<!-- 下面的form-horizontal这个类影响了编辑器，抓狂！！ -->
		<form id="topic-form" method="post" class="form-horizontal" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo $title; ?></legend>
				<div class="control-group<?php if (isset($errors['title'])) { ?> error<?php } ?>">
					<label class="control-label" for="topic_title"><?php echo __('Title'); ?>:</label>
					<div class="controls">
						<input type="text" class="input-xxlarge" id="topic_title" name="title" value="<?php echo $topic->title ?>" placeholder="<?php echo __('Title'); ?>">
						<?php if (isset($errors['title'])) { ?><span class="help-block"><?php echo $errors['title'] ?></span><?php } ?>
					</div>
				</div>
				<div class="control-group<?php if (isset($errors['content'])) { ?> error<?php } ?>">
					<label class="control-label"><?php echo Auth::instance()->get_user()->avatar_link() ?></label>
					<div class="controls">
						<?php echo View::factory('Media.Editor', array(
							'content' => $topic->content
						)) ?>
						<?php if (isset($errors['content'])) { ?><span class="help-block"><?php echo $errors['content'] ?></span><?php } ?>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary"><?php echo __('Submit') ?></button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
