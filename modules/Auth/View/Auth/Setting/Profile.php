<div class="tab-pane" id="setting-profile">
	<!-- 学号 -->
	<div class="control-group">
		<label class="control-label" for="stuno"><?php echo __('Student No') ?></label>
		<div class="controls">
			<input type="text" id="stuno" name="stuno" value="<?php echo $user->stuno ?>" />
		</div>
	</div>
	<!-- 姓名 -->
	<div class="control-group">
		<label class="control-label" for="realname"><?php echo __('Realname') ?></label>
		<div class="controls">
			<input type="text" id="realname" name="realname" value="<?php echo $user->realname ?>" />
		</div>
	</div>
	<!-- QQ -->
	<div class="control-group">
		<label class="control-label" for="qq"><?php echo __('QQ') ?></label>
		<div class="controls">
			<input type="text" id="qq" name="qq" value="<?php echo $user->qq ?>" />
		</div>
	</div>
	<!-- 短号 -->
	<div class="control-group">
		<label class="control-label" for="telephone"><?php echo __('Mobile Shortcut') ?></label>
		<div class="controls">
			<input type="text" id="telephone" class="input-xlarge" name="telephone" value="<?php echo $user->telephone ?>" />
		</div>
	</div>
	<!-- 地址 -->
	<div class="control-group">
		<label class="control-label" for="address"><?php echo __('Address') ?></label>
		<div class="controls">
			<input type="text" id="address" class="input-xxlarge" name="address" value="<?php echo $user->address ?>" />
		</div>
	</div>
</div>
