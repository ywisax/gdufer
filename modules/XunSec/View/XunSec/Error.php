<?php if ( ! empty($errors)): ?>
<div class="alert alert-warning">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<ul class="error">
	<?php foreach ($errors AS $field => $error): ?>
		<li rel="<?php echo $field ?>"><?php echo ucfirst($error) ?></li>
	<?php endforeach ?>
	</ul>
</div>
<?php endif; ?>

<?php if ( ! empty($success) AND $success != FALSE): ?>
<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<div class="success">
		<?php echo $success ?>	
	</div>
</div>
<?php endif; ?>
