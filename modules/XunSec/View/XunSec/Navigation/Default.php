<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * 默认使用的bootstrap样式的导航
 */
if (Kohana::$profiling === TRUE)
{
	// Start a new benchmark
	$benchmark = Profiler::start('XunSec', 'MPTT crawl');
}
// Change nodes into an array
$nodes = $nodes->as_array();

// Set the defaults
$defaults = array(
	// Options for the header before the nav
	'header'       => FALSE,
	'header_elem'  => 'h3',
	'header_class' => '',
	'header_id'    => '',

	// Options for the list itself
	'class'   => '',
	'id'      => '',
	'depth'   => 2,

	// Options for items
	'current_class' => 'current',
	'first_class' => 'first',
	'last_class'  => 'last',
);
// Merge to create the options
$options = array_merge($defaults, $options);
?>
<?php
// Open the ul
/* echo "\n<ul" . ($options['class'] != '' ? " class='{$options['class']}'":'') .
			 ($options['id'] != '' ? " id='{$options['id']}'":'') . ">\n"; */
echo "\n"
	.'<ul'
	.(' class="'.($options['class'] ? $options['class'] : 'nav').'"')
	.($options['id'] ? ' id="'.$options['id'].'"' : '')
	.'>';
$rootlevel = $nodes[1]->{$level_column};
$level = $nodes[1]->{$level_column};
$first = TRUE;
$classes = array('first');

$count = count($nodes);
for ($i=1 ; $i<$count ; $i++)
{
	$attributes = array();
	$next = Helper_Array::get($nodes, $i+1, FALSE);
	$curr = Helper_Array::get($nodes, $i);
	
	// 如果有下级菜单
	if ($curr->has_children())
	{
		$classes[] = 'dropdown';
		$attributes['data-toggle'] = 'dropdown';
		$attributes['class'] = 'dropdown-toggle';
	}
	if ($curr->{$level_column} > $level)
	{
		echo '<ul class="dropdown-menu">'."\n";
		$classes[] = $options['first_class'];
	}
	else if ($curr->{$level_column} < $level)
	{
		for( $j=0 ; $j < ($level - $curr->{$level_column}) ; $j++ )
		{
			echo "</li></ul></li>\n";
		}
	}
	else if ( ! $first)
	{
		echo "</li>\n";
	}
	
	for ( $j=0 ; $j < ($curr->{$level_column}) ; $j++ )
	{
		echo "\t";
	}
	
	if ( ! empty($classes))
	{
		$classes = array('class'=>implode(' ', $classes));
	}

	echo "<li" . HTML::attributes($classes). ">" . HTML::anchor($curr->url, $curr->name, $attributes);
	
	$level = $curr->{$level_column};
	$classes = array();
	$first = FALSE;
}

for( $j=0 ; $j < ($curr->{$level_column}) - $rootlevel ; $j++ )
{
	echo "</li></ul>";
}

echo "</li>\n</ul>";

if (isset($benchmark))
{
	// Stop the benchmark
	Profiler::stop($benchmark);
}
?>
