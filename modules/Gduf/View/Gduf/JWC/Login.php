<div class="row-fluid">
	<div class="offset3 span6" id="gduf-jwc-login">
		<?php if (isset($errors['unknown'])): ?>
		<div class="well">
			<?php echo $errors['unknown'] ?>
		</div>
		<?php endif; ?>
		<?php include Kohana::find_file('View', 'Gduf.JWC.Login.Form') ?>
	</div>
</div>
