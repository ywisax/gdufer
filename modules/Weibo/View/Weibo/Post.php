<?php
XunSec::script('bootbox/bootbox.min.js');

// uploadify
XunSec::style('uploadify/uploadify.css');
XunSec::script('uploadify/jquery.uploadify.min.js');

XunSec::style('weibo/css/style.css');
XunSec::script('weibo/js/script.js');
?>
<div id="weibo-post" class="row-fluid">
	<div class="offset3 span6">
		<?php if (Weibo::setting('announcement')): ?>
		<div class="well">
			<?php echo Weibo::setting('announcement') ?>
		</div>
		<?php endif; ?>
		<form method="post">
			<legend class="text-center"><?php echo __('Weibo Post Form') ?></legend>
			<p>当前用户：<strong><?php echo Session::instance()->get('screen_name') ?></strong><span id="weibo-post-words">字数：<small>0</small></span></p>
			<textarea id="weibo-post-area" name="content" placeholder="想说点什么？"></textarea>
			<hr />
			<div id="weibo-functions" class="row-fluid">
				<div class="span3 text-center"><a class="function smile" href="#" data-url="<?php echo Route::url('weibo-emotions') ?>"><img src="<?php echo Media::url('weibo/img/smile-48.png') ?>" /> 表情</a></div>
				<div class="span3 text-center"><a class="function pic" href="#"
					data-pic=""
					data-uploadurl="<?php echo Route::url('weibo-upload') ?>"
					data-swf="<?php echo Media::url('uploadify/uploadify.swf') ?>"
					data-cancelImg="<?php echo Media::url('uploadify/uploadify-cancel.png') ?>"
				><img src="<?php echo Media::url('weibo/img/camera-48.png') ?>" /> 图片</a></div>
				<div class="span3 text-center"><a class="function tag" href="#modal-tag" role="button" data-toggle="modal"><img src="<?php echo Media::url('weibo/img/clip-48.png') ?>" /> 话题</a></div>
				<div class="span3 text-center"><a class="function long" href="#"><img data-img1="<?php echo Media::url('weibo/img/notepad-48.png') ?>" data-img2="<?php echo Media::url('weibo/img/notepad-48-1.png') ?>" src="<?php echo Media::url('weibo/img/notepad-48.png') ?>" /> 长微博</a></div>
			</div>
			<hr />
			<p class="text-center">
				<input id="weibo-is-long" type="hidden" name="is_long" value="0" />
				<input id="weibo-image-id" type="hidden" name="img_id" value="0" />
				<button id="weibo-send-button" data-send="<?php echo Route::url('weibo-send') ?>" type="submit" class="btn btn-primary btn-large"><?php echo __('Post to Weibo') ?></button>
			</p>
			<div id="upload-block" style="display:none;">
				<hr />
				<input type="file" name="fileInput" id="fileInput" />
			</div>
		</form>
	</div>
</div>
<div id="modal-tag" class="modal hide fade" role="dialog" aria-labelledby="modal-tag-modal-title" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="modal-tag-modal-title"><?php echo __('Enter the weibo topic') ?></h3>
	</div>
	<div class="modal-body">
		<input id="modal-tag-input" type="text" value="" placeholder="<?php echo __('北师珠小师妹') ?>" />
		<br />
		<?php
		$topics = Weibo::setting('recommend_topics');
		$topics = explode("\n", $topics);
		$topic_classes = array('success', 'warning', 'important', 'info');
		foreach ($topics AS $topic)
		{
			$topic = trim($topic);
			?>&nbsp;<span data-topic="<?php echo $topic ?>" class="badge badge-<?php echo $topic_classes[array_rand($topic_classes)] ?>"><?php echo $topic ?></span>&nbsp;<?php
		}
		?>
	</div>
	<div class="modal-footer">
		<a href="#" id="modal-tag-submit" class="btn btn-primary"><?php echo __('Add') ?></a>
		<a href="#" class="btn btn-warning" data-dismiss="modal" aria-hidden="true"><?php echo __('Close') ?></a>
	</div>
</div>
