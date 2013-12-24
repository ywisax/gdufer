<div class="grid_16">
	<div class="box">
		<h1><?php echo __('Editing :element', array(':element' => __(ucfirst($element->type())))) ?></h1>
		
		<?php include Kohana::find_file('View', 'XunSec.Error') ?>
		
		<form method="post">
			
			<p>
				<label for="which"><?php echo __('Select a :element', array(':element' => __(ucfirst($element->type())))) ?></label>
				<?php
				
				$choices = $element->select_list($element->pk());

				echo Helper_Form::select('element', $choices, $element->id) ?>
				
			</p>
			
			<p>
				<?php echo Helper_Form::submit('submit', __('Save Changes'), array('class' => 'btn btn-primary')) ?>
				<?php echo HTML::anchor(Route::url('xunsec-admin', array(
					'controller' => 'Page',
					'action' => 'edit',
					'params' => $page
				)), __('cancel')); ?>
			</p>
			
		</form>
		
		</div>
	</div>

</div>