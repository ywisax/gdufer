<h1>
	<?php echo $doc->modifiers, $doc->class->name ?>
	<?php foreach ($doc->parents AS $parent): ?>
	<br/><small><?php echo __('extends :class_name', array(
		':class_name' => HTML::anchor($route->uri(array('class' => $parent->name)), $parent->name, NULL, NULL, TRUE)
	)) ?></small>
	<?php endforeach; ?>
</h1>

<?php if ($interfaces = $doc->class->getInterfaceNames()): ?>
<p class="interfaces"><small>
<?php echo __('Implements:') ?>
<?php
for ($i = 0, $split = FALSE, $count = count($interfaces); $i < $count; $i++, $split = ' | ')
{
    echo $split . HTML::anchor($route->uri(array('class' => $interfaces[$i])), $interfaces[$i], NULL, NULL, TRUE);
}
?></small>
</p>
<?php endif; ?>

<?php if ($child = $doc->is_transparent($doc->class->name)):?>
<p class="note">
<?php echo __('This class is a transparent base class for :child and should not be accessed directly.', array(
	':child' => HTML::anchor($route->uri(array('class' => $child)), $child),
)) ?>
</p>
<?php endif;?>

<?php echo $doc->description() ?>

<?php if ($doc->tags): ?>
<dl class="tags">
<?php foreach ($doc->tags() AS $name => $set): ?>
<dt><?php echo $name ?></dt>
<?php foreach ($set AS $tag): ?>
<dd><?php echo $tag ?></dd>
<?php endforeach ?>
<?php endforeach ?>
</dl>
<?php endif; ?>

<p class="note">
<?php if ($path = $doc->class->getFilename()): ?>
<?php
echo __('Class declared in :path on line :line', array(
	':path' => '<code>'.Debug::path($path).'</code>',
	':line' => '<strong>'.$doc->class->getStartLine().'</strong>',
));
?>
<?php else: ?>
<?php echo __('Class is not declared in a file, it is probably an internal <a target="_blank" href=":link">PHP class</a>.', array(
	':link' => 'http://php.net/manual/class.'.strtolower($doc->class->name).'.php',
)) ?>
<?php endif ?>
</p>

<div class="toc">
	<div class="constants">
		<h3><?php echo __('Constants'); ?></h3>
		<ul>
		<?php if ($doc->constants): ?>
		<?php foreach ($doc->constants AS $name => $value): ?>
			<li><a href="#constant:<?php echo $name ?>"><?php echo $name ?></a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
	<div class="properties">
		<h3><?php echo __('Properties'); ?></h3>
		<ul>
		<?php if ($properties = $doc->properties()): ?>
		<?php foreach ($properties AS $prop): ?>
			<li><a href="#property:<?php echo $prop->property->name ?>">$<?php echo $prop->property->name ?></a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
	<div class="methods">
		<h3><?php echo __('Methods'); ?></h3>
		<ul>
		<?php if ($methods = $doc->methods()): ?>
		<?php foreach ($methods AS $method): ?>
			<li><a href="#<?php echo $method->method->name ?>"><?php echo $method->method->name ?>()</a></li>
		<?php endforeach ?>
		<?php else: ?>
			<li><em><?php echo 'None'; ?></em></li>
		<?php endif ?>
		</ul>
	</div>
</div>

<div class="clearfix"></div>

<?php if ($doc->constants): ?>
<div class="constants">
<h1 id="constants"><?php echo __('Constants'); ?></h1>
<dl>
<?php foreach ($doc->constants() AS $name => $value): ?>
<dt><h4 id="constant:<?php echo $name ?>"><?php echo $name ?></h4></dt>
<dd><?php echo $value ?></dd>
<?php endforeach; ?>
</dl>
</div>
<?php endif ?>

<?php if ($properties = $doc->properties()): ?>
<h1 id="properties"><?php echo __('Properties'); ?></h1>
<div class="properties">
<dl>
<?php foreach ($properties AS $prop): ?>
<dt><h4 id="property:<?php echo $prop->property->name ?>"><?php echo $prop->modifiers ?> <code><?php echo $prop->type ?></code> $<?php echo $prop->property->name ?></h4></dt>
<dd><?php echo $prop->description ?></dd>
<dd><?php echo $prop->value ?></dd>
<?php if ($prop->default !== $prop->value): ?>
<dd><small><?php echo __('Default value:') ?></small><br/><?php echo $prop->default ?></dd>
<?php endif ?>
<?php endforeach ?>
</dl>
</div>
<?php endif ?>

<?php if ($methods = $doc->methods()): ?>
<h1 id="methods"><?php echo __('Methods'); ?></h1>
<div class="methods">
<?php foreach ($methods AS $method): ?>
<?php echo View::factory('Guide.API.Method')->set('doc', $method)->set('route', $route) ?>
<?php endforeach ?>
</div>
<?php endif ?>
