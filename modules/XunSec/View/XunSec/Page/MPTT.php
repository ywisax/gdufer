<?php defined('SYS_PATH') OR die('No direct access allowed.');


$level = $nodes->current()->lvl;
$first = TRUE;
echo "<div id='pagetreeloading'>" . HTML::image(URL::site('media/img/loading.gif'), array('alt' => __('loading'))) . __('Loading...') . '</div>';
echo "<ul id='pagetree'><div class='clear'></div>";
foreach ($nodes AS $node)
{
	
	// current item is deeper than the item before it, it is a child of the previous item
	if ($node->{$level_column} > $level)
	{
		echo "<ul>";
	}
	// current item is less deep than the item before it, how many generations up we did we go?
	else if ($node->{$level_column} < $level )
	{
		echo "</li>";
		for( $i=0 ; $i < ($level - $node->{$level_column}) ; $i++ )
		{
			// close a list and item for each generation that just ended
			echo "</ul></li>";
		}
	}
	// not starting on ending generations, just close the previous node.
	else if ( ! $first)
	{
		echo "</li>";
	}
	?>
		
	<li <?php if ($node->lvl == 0) echo "class='open'" ?>>
		<div class="pageinfo">
			<?php if ($node->islink) echo '<div class="fam-arrow"></div>'; ?>
			<div style="float:left">
				<p class='pagename'><?php echo $node->name ?></p>
				<?php
				// echo <p class="pageurl[ islink]">
				echo "<p class='pageurl" . ($node->islink ?' islink':'') . "'>";
				// if the link does not have :// in it, echo the url base (like http://example.com/ ) in a span, so its gray
				echo ( strpos($node->url, '://') === FALSE ? "<span>" . URL::base(FALSE,TRUE) . "</span>" : '' );
				// echo the url, and if its a link, put (Link) after it
				echo $node->url . ($node->islink? ' ' . __('(Link)'):'');
				// close pageurl
				echo "</p>";
				?>
			</div>
			<div class="actions">
				<?php
				echo HTML::anchor($node->url,
					 '<i class="icon-ok"></i> '.__('View'), array(
						'title' => __('Click to view page'),
						'target' => '_blank',
					));
				echo HTML::anchor(Route::url('xunsec-admin', array(
						'controller' => 'Page',
						'action' => 'edit',
						'params'=>$node->id
					)),
					'<i class="icon-edit"></i> '.__('Edit'), array('title'=>__('Click to edit page')));
				echo HTML::anchor(Route::url('xunsec-admin', array(
						'controller' => 'Page',
						'action' => 'move',
						'params' => $node->id,
					)),
					'<i class="icon-move"></i> '.__('Move'), array('title'=>__('Click to move page')));
				echo HTML::anchor(Route::url('xunsec-admin', array(
						'controller' => 'Page',
						'action' => 'add',
						'params' => $node->id,
					)),
					'<i class="icon-plus"></i> '.__('Add'), array('title'=>__('Click to add sub-page')));
				echo HTML::anchor(Route::url('xunsec-admin', array(
						'controller' => 'Page',
						'action' => 'delete',
						'params'=>$node->id,
					)),
					'<i class="icon-trash"></i> '.__('Delete'), array('title'=>__('Click to delete page')));
				?>
				
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
		
		<?php
	// set level to this nodes level
	$level = $node->{$level_column};
	$first = FALSE;
}
// close a li and ul for each level deep that the very last node was
for($i = 0; $i < $level; $i++)
{
	echo "</li></ul>";
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$("#pagetree").treeview({
		animated: "fast",
		collapsed: true,
		persist: "cookie"
	});
	$("#pagetreeloading").hide();
});
</script>
