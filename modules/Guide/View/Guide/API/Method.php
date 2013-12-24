<div class="method">

<?php $declares = $doc->method->getDeclaringClass(); ?>
<h3 id="<?php echo $doc->method->name ?>">
	<?php echo $doc->modifiers, $doc->method->name ?>( <?php echo $doc->params ? $doc->params_short() : '' ?>)
	<small><?php echo __('(defined in :declare)', array(':declare' => HTML::anchor($route->uri(array('class' => $declares->name)), $declares->name, NULL, NULL, TRUE))) ?></small>
</h3>

<div class="description">
<?php echo $doc->description ?>
</div>

<?php if ($doc->params): ?>
<h4><?php echo __('Parameters') ?></h4>
<ul>
<?php foreach ($doc->params AS $param): ?>
<li>
<code><?php echo ($param->reference?'byref ':'').($param->type?$param->type:'unknown') ?></code>
<strong><?php echo '$'.$param->name ?></strong>
<?php echo $param->default?'<small> = '.$param->default.'</small>':'<small>required</small>'  ?>
<?php echo $param->description?' - '.$param->description:'' ?>
</li>
<?php endforeach; ?>
</ul>
<?php endif ?>

<?php if ($doc->tags) echo View::factory('Guide.API.Tag')->set('tags', $doc->tags) ?>

<?php if ($doc->return): ?>
<h4><?php echo __('Return Values'); ?></h4>
<ul class="return">
<?php foreach ($doc->return AS $set): list($type, $text) = $set; ?>
<li><code><?php echo HTML::chars($type) ?></code><?php if ($text) echo ' - '.HTML::chars(ucfirst($text)) ?></li>
<?php endforeach ?>
</ul>
<?php endif ?>

<?php if ($doc->source): ?>
<div class="method-source">
<h4><?php echo __('Source Code'); ?></h4>
<pre><code><?php echo HTML::chars($doc->source) ?></code></pre>
</div>
<?php endif ?>

</div>
