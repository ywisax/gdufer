<?php
// 加载编辑器
XunSec::style('bootstrap-wysiwyg/index.css');
XunSec::style('editor/common.css');

XunSec::script('bootstrap-wysiwyg/external/jquery.hotkeys.js');
XunSec::script('bootstrap-wysiwyg/bootstrap-wysiwyg.js');
XunSec::script('editor/common.js');
?>
<div id="editor-toolbar" class="btn-toolbar" data-role="editor-toolbar" data-target="#editor">
	<!--<div class="btn-group">
		<a class="btn dropdown-toggle font-group" data-toggle="dropdown" title="<?php echo __('Font') ?>"><i class="icon-font"></i><b class="caret"></b></a>
		<ul class="dropdown-menu">
		</ul>
	</div>-->
	<div class="btn-group">
		<a class="btn dropdown-toggle" data-toggle="dropdown" title="<?php echo __('Font Size') ?>"><i class="icon-text-height"></i>&nbsp;<b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a data-edit="fontSize 5"><font size="5"><?php echo __('Huge Font') ?></font></a></li>
			<li><a data-edit="fontSize 3"><font size="3"><?php echo __('Normal Font') ?></font></a></li>
			<li><a data-edit="fontSize 1"><font size="1"><?php echo __('Small Font') ?></font></a></li>
		</ul>
	</div>
	<div class="btn-group">
		<a class="btn" data-edit="bold" title="<?php echo __('Bold (Ctrl/Cmd+B)') ?>"><i class="icon-bold"></i></a>
		<a class="btn" data-edit="italic" title="<?php echo __('Italic (Ctrl/Cmd+I)') ?>"><i class="icon-italic"></i></a>
		<a class="btn" data-edit="strikethrough" title="<?php echo __('Strikethrough') ?>"><i class="icon-strikethrough"></i></a>
		<a class="btn" data-edit="underline" title="<?php echo __('Underline (Ctrl/Cmd+U)') ?>"><i class="icon-underline"></i></a>
	</div>
	<div class="btn-group">
		<a class="btn" data-edit="insertunorderedlist" title="<?php echo __('Bullet list') ?>"><i class="icon-list-ul"></i></a>
		<a class="btn" data-edit="insertorderedlist" title="<?php echo __('Number list') ?>"><i class="icon-list-ol"></i></a>
		<a class="btn" data-edit="outdent" title="<?php echo __('Reduce indent (Shift+Tab)') ?>"><i class="icon-indent-left"></i></a>
		<a class="btn" data-edit="indent" title="<?php echo __('Indent (Tab)') ?>"><i class="icon-indent-right"></i></a>
	</div>
	<div class="btn-group">
		<a class="btn" data-edit="justifyleft" title="<?php echo __('Align Left (Ctrl/Cmd+L)') ?>"><i class="icon-align-left"></i></a>
		<a class="btn" data-edit="justifycenter" title="<?php echo __('Center (Ctrl/Cmd+E)') ?>"><i class="icon-align-center"></i></a>
		<a class="btn" data-edit="justifyright" title="<?php echo __('Align Right (Ctrl/Cmd+R)') ?>"><i class="icon-align-right"></i></a>
		<a class="btn" data-edit="justifyfull" title="<?php echo __('Justify (Ctrl/Cmd+J)') ?>"><i class="icon-align-justify"></i></a>
	</div>
	<div id="editor-group-hyperlink" class="btn-group">
		<a class="btn dropdown-toggle" data-toggle="dropdown" title="<?php echo __('Hyperlink') ?>"><i class="icon-link"></i></a>
		<div class="dropdown-menu input-append">
			<input class="input-xlarge input-url" placeholder="URL" type="text" data-edit="createLink"/>
			<button class="btn" type="button"><?php echo __('Add') ?></button>
		</div>
		<a class="btn" data-edit="unlink" title="<?php echo __('Remove Hyperlink') ?>"><i class="icon-cut"></i></a>
	</div>
	<div id="editor-group-image" class="btn-group">
		<a class="btn dropdown-toggle" data-toggle="dropdown" title="<?php echo __('Insert picture (or just drag & drop)') ?>"><i class="icon-picture"></i></a>
		<div class="dropdown-menu input-append">
			<input class="input-xlarge input-url" placeholder="URL" type="text" data-edit="InsertImage"/>
			<button class="btn" type="button"><?php echo __('Add') ?></button>
			<a class="btn" id="pictureBtn" title="<?php echo __('Select and insert an image') ?>"><?php echo __('Upload') ?></a>
			<input id="pictureBtnCallback" type="file" data-role="magic-overlay" data-target="#pictureBtn" data-edit="uploadImage" data-callback="<?php echo Attachment::callback_url() ?>" />
		</div>
	</div>
	<div class="btn-group">
		<a class="btn" data-edit="undo" title="<?php echo __('Undo (Ctrl/Cmd+Z)') ?>"><i class="icon-undo"></i></a>
		<a class="btn" data-edit="redo" title="<?php echo __('Redo (Ctrl/Cmd+Y)') ?>"><i class="icon-repeat"></i></a>
	</div>
	<input type="text" data-edit="inserttext" id="voiceBtn" x-webkit-speech="">
</div>
<div id="editor">
	<?php echo isset($content) ? $content : '' ?>
</div>
<input class="content" name="content" type="text" style="display:none;" />
