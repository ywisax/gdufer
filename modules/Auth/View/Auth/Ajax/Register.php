<div id="common-register-box" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="common-register-box-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="common-register-box-label"><?php echo __('User Register') ?></h3>
	</div>
	<div id="common-register-box-content" class="modal-body">
		<?php
		$ajax = TRUE;
		include Kohana::find_file('View', 'Auth.Register.Form');
		?>
	</div>
</div>
