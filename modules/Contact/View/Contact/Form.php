<?php
XunSec::style('contact/css/contact.css');
?>
<div id="contact-us-form" class="row-fluid">
	<div class="span6 offset3">
		<form class="well" method="post">
			<fieldset>
				<legend>联系我们</legend>
				
				<label>联系人</label>
				<input type="text" name="realname" placeholder="联系人/真实姓名" />
				<?php if (isset($errors['realname'])): ?>
				<span class="help-block"><?php echo $errors['realname'] ?></span>
				<?php endif; ?>
				
				<label>联系号码</label>
				<input type="text" name="mobile" placeholder="座机/短号/手机号码" />
				<?php if (isset($errors['mobile'])): ?>
				<span class="help-block"><?php echo $errors['mobile'] ?></span>
				<?php endif; ?>
				
				<label>留言内容</label>
				<textarea name="content"></textarea>
				<?php if (isset($errors['content'])): ?>
				<span class="help-block"><?php echo $errors['content'] ?></span>
				<?php endif; ?>

				<?php echo Captcha::instance() ?>
				<input id="captcha-input" type="text" maxlength="4" name="captcha" class="captcha-input" placeholder="验证码"  />
				<br />
				<?php if (isset($errors['captcha'])): ?>
				<span class="help-block"><?php echo $errors['captcha'] ?></span>
				<?php endif; ?>
				
				<button type="submit" class="btn btn-large btn-info">提交</button>
			</fieldset>
		</form>
	</div>
</div>
