<div class="grid_16">
	<div class="box">
		<h1><?php echo __('Change Language') ?></h1>

		<?php echo Helper_Form::open(NULL, array('method' => 'get')) ?>
		<p>
			<?php echo Helper_Form::select('lang', $translations, I18N::$lang) ?>
		</p>
		<p>
			<?php echo Helper_Form::submit('submit',__('Change Language'), array('class' => 'btn btn-primary')) ?>
		</p>
		</form>
	</div>
</div>
