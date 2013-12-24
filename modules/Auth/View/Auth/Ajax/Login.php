<div id="common-login-box" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="common-login-box-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="common-login-box-label"><?php echo __('User Login') ?></h3>
	</div>
	<div id="common-login-box-content" class="modal-body">
		<?php
		$ajax = TRUE;
		include Kohana::find_file('View', 'Auth.Login.Form');
		?>
	</div>
</div>
