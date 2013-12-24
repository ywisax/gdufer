<div class="row-fluid">
	<div class="span9">
		<h2><?php echo __('Weibo Setting') ?></h2>
		<hr />
		<form id="weibo-admin-setting-form" method="post" class="form-horizontal">
			
			<!-- 过滤词语 -->
			<div class="control-group">
				<label class="control-label" for="censor_words"><?php echo __('Censor Words') ?></label>
				<div class="controls">
					<textarea class="input-xxlarge" id="censor_words" name="censor_words" style="height:100px;width:90%;max-width:90%;"><?php echo $settings['censor_words']->val ?></textarea>
					<span class="help-block"><?php echo __('Input the word you need to censor, and using the comma to separate the words.') ?></span>
				</div>
			</div>
			
			<!-- 公告代码 -->
			<div class="control-group">
				<label class="control-label" for="announcement"><?php echo __('Announcement') ?></label>
				<div class="controls">
					<textarea class="input-xxlarge" id="announcement" name="announcement" style="height:100px;width:90%;max-width:90%;"><?php echo $settings['announcement']->val ?></textarea>
					<span class="help-block"><?php echo __('Input the announcement code, and it will be appeared below the weibo post form.') ?></span>
				</div>
			</div>
			
			<!-- 发布间隔 -->
			<div class="control-group">
				<label class="control-label" for="post_interval"><?php echo __('Post Interval') ?></label>
				<div class="controls">
					<input type="text" id="post_interval" name="post_interval" class="input-xxlarge" value="<?php echo $settings['post_interval']->val ?>" />
					<span class="help-block"><?php echo __('Seconds to limit posters to post.') ?></span>
				</div>
			</div>
			
			<!-- 推荐话题 -->
			<div class="control-group">
				<label class="control-label" for="recommend_topics"><?php echo __('Recommend Topics') ?></label>
				<div class="controls">
					<textarea class="input-xxlarge" id="recommend_topics" name="recommend_topics" style="height:100px;width:90%;max-width:90%;"><?php echo $settings['recommend_topics']->val ?></textarea>
					<span class="help-block"><?php echo __('The hot topics adn focused topics.') ?></span>
				</div>
			</div>
			
			<!-- 发布端UID -->
			<div class="control-group">
				<label class="control-label" for="weibo_client_uid"><?php echo __('Client UID') ?></label>
				<div class="controls">
					<input type="text" id="weibo_client_uid" name="weibo_client_uid" class="input-xxlarge" value="<?php echo $settings['weibo_client_uid']->val ?>" />
					<span class="help-block"><?php echo __('The UID of proxy client.') ?></span>
				</div>
			</div>
			
			<!-- 发布端UID -->
			<div class="control-group">
				<label class="control-label" for="allow_time_range"><?php echo __('Time Range') ?></label>
				<div class="controls">
					<input type="text" id="allow_time_range" name="allow_time_range" class="input-xxlarge" value="<?php echo $settings['allow_time_range']->val ?>" />
					<span class="help-block"><?php echo __('Please enter the correct time range.') ?></span>
				</div>
			</div>
			
			<!-- 发布端状态 -->
			<div class="control-group">
				<label class="control-label" for="client_status"><?php echo __('Client Status') ?></label>
				<div class="controls">
					<label class="checkbox" id="client_status">
						<input name="client_status" type="checkbox"<?php if ($settings['client_status']->val) { echo ' checked'; } ?> /> <?php echo __('Open or close the Weibo Client.') ?>
					</label>
				</div>
			</div>
			
			<!-- 动作 -->
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary"><?php echo __('Submit') ?></button>
					<a class="btn" href=""><?php echo __('Refresh') ?></a>
				</div>
			</div>
		</form>
	</div>
	<?php include Kohana::find_file('View', 'XunSec.Weibo.Sidebar') ?>
</div>
