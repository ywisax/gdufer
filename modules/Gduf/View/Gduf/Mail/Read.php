<!-- 阅读邮件 -->
<div id="mail-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="mail-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<div id="mail-modal-label">阅读邮件：<span>标题</span></div>
	</div>
	<div class="modal-body">
		<p>邮件正文</p>
	</div>
	<div class="modal-footer">
		<input type="hidden" id="mail-modal-id" value="0" />
		<input type="hidden" id="mail-modal-personId" value="0" />
		<button data-id="0" class="reply btn btn-primary">答复</button>
		<button data-id="0" class="forward btn btn-info">转发</button>
		<button data-id="0" class="delete btn btn-warning">删除</button>
		<button data-id="0" class="delete-forever btn btn-danger">永久删除</button>
		<button class="back btn" data-dismiss="modal" aria-hidden="true">返回</button>
	</div>
</div>
