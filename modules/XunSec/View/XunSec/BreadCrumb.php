<?php defined('SYS_PATH') OR die('No direct access allowed.'); 

echo "\n<!-- Bread Crumbs -->\n<ul>";
$first = TRUE;
foreach ($nodes AS $node)
{
	echo '<li' . ($first?' class="first"':'') .'>';
	echo HTML::anchor($node->url, $node->name);
	echo '</li>';
	$first = FALSE;
}

echo '<li class="last ' . ($first?' first':'') .'">' . $page . "</li></ul>\n<!-- End Bread Crumbs -->";
