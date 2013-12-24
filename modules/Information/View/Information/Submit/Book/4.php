<div id="step4" class="clearfix step" style="display:none;">
	<div class="info-data">
		<div class="control-group">
			<label class="control-label" for="realname">姓名</label>
			<div class="controls">
				<input type="text" id="realname" name="realname" class="input-xlarge" placeholder="XX同学即可" value="<?php echo isset($post['realname']) ? $post['realname'] : Auth::instance()->get_user()->realname ?>" required="required" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="telephone">短号</label>
			<div class="controls">
				<input type="text" id="telephone" name="telephone" class="input-xlarge" placeholder="强烈建议填写短号" value="<?php echo isset($post['telephone']) ? $post['telephone'] : Auth::instance()->get_user()->telephone ?>" required="required" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="remark">备注</label>
			<div class="controls">
				<input type="text" id="remark" name="remark" class="input-xlarge" value="<?php echo isset($post['remark']) ? $post['remark'] : '' ?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<a href="#" class="btn back-to-step3">上一步</a>
				<a href="#" class="btn btn-success go-to-step5">完成</a>
			</div>
		</div>
	</div>
</div>
