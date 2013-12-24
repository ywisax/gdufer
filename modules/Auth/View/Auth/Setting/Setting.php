<div class="tab-pane" id="setting-setting">
	<!-- 订阅 -->
	<div class="control-group">
		<label class="control-label"><?php echo __('Newsletter') ?></label>
		<div class="controls">
			<label class="checkbox">
				<input type="checkbox"> <?php echo __('Allow us to send the informations about campus to you.') ?>
			</label>
		</div>
	</div>
	<hr />
	<!-- 广金邮箱绑定 -->
	<div class="control-group">
		<label class="control-label"><?php echo __('Bind GDUF Mail') ?></label>
		<div class="controls">
			<label><?php echo __('Username') ?></label>
			<input name="gduf_mail_username" type="text" value="" />
			<label><?php echo __('Password') ?></label>
			<input name="gduf_mail_password" type="password" value="" />
		</div>
	</div>
	<hr />
	<!-- 教务处绑定 -->
	<div class="control-group">
		<label class="control-label"><?php echo __('Bind GDUF JWC') ?></label>
		<div class="controls">
			<label><?php echo __('Username') ?></label>
			<input name="gduf_jwc_username" type="text" value="" />
			<label><?php echo __('Password') ?></label>
			<input name="gduf_jwc_password" type="password" value="" />
		</div>
	</div>
</div>
