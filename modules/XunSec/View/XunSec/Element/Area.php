<?php
echo "\n<!-- Element Area  $id ($name) -->\n";
if (XunSec::$adminmode)
{
?>
<p class="xunsec-area-title"><?php echo __('Element Area #:num - :name', array(':num' => $id, ':name' => $name)) ?></p>
<div class="xunsec-area">
<?php
}
echo $content;
if (XunSec::$adminmode)
{
?>
<div class="xunsec-element-control">
	<p class="title"><span class="fam-add inline-sprite"></span><?php echo __('Add New Element') ?></p>
	<?php echo Helper_Form::open() ?>
	<?php echo Helper_Form::hidden('area', $id); ?>
	<select name="type" style="float:left;margin-right:5px;">
		<?php
		$elements = Model::factory('Element.Type')->find_all();
		foreach ($elements AS $element)
		{
			echo "<option value='{$element->id}'>". __(ucfirst($element->name)) ."</option>";
		}
		?>
	</select>
	<?php echo Helper_Form::submit('add', __('Add Element'), array('class' => 'submit')); ?>
	</form>
	<div style="clear:left;"></div>
</div>

</div>
<?php
}
echo "\n<!-- End Content Area $id ($name) -->\n";
