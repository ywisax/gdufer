<?php defined('SYS_PATH') OR die('No direct access allowed.'); ?>
<ul>
<?php
$level = $nodes->current()->{$level_column};
$rootlevel = $level;
$first = TRUE;

foreach ($nodes AS $node)
{
	// if showmap is false, skip this item
	if ( ! $node->showmap)
	{
		continue;
	}

	if ($node->{$level_column} > $level)
	{
		echo "<ul>\n";
	}
	else if ($node->{$level_column} < $level)
	{
		
		for( $i=0 ; $i < ($level - $node->{$level_column}) ; $i++ )
		{
			echo "</li></ul></li>\n";
		}
	}
	else if ( ! $first)
	{
		echo "</li>\n";
	}
	
	for ( $j=0 ; $j < ($node->{$level_column}) ; $j++ )
	{
		echo "\t";
	}
	
	echo "<li>" . HTML::anchor($node->url, $node->name);
	
	$level = $node->{$level_column};
	$first = FALSE;
}
for( $i=0 ; $i < ($node->{$level_column}) - $rootlevel ; $i++ )
{
	echo "</li></ul>";
}

echo "</li>\n</ul>";
?>
