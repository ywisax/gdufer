<!-- 发送邮件 -->
<div id="send-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="send-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<div id="send-modal-label">编辑邮件：<span></span></div>
	</div>
	<div class="modal-body">
		<form class="form-horizontal" method="post" data-callback="<?php echo Route::url('gduf-mail-action', array('action' => 'send')) ?>">
			<div class="control-group">
				<label class="control-label" for="subject">主 题：</label>
				<div id="subject-control" class="controls">
					<input type="text" id="subject" class="input-xxlarge" placeholder="填写主题" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="receiver">收件人：</label>
				<div id="receiver-control" class="controls">
					<input type="text" id="receiver" class="input-xxlarge" placeholder="收件人" />
					<button id="search-user" class="btn">查找用户</button>
				</div>
			</div>
			<hr />
			<div class="control-group">
				<label class="control-label" for="receiver">正文：</label>
				<div class="controls">
					<textarea id="gduf-mail-send-content" name="content"></textarea>
				</div>
			</div>
		</form>
	</div>
	<div class="modal-footer">
		<button id="gduf-mail-send-submit" class="btn btn-primary">发送</button>
		<button class="btn" data-dismiss="modal" aria-hidden="true">返回</button>
	</div>
</div>
