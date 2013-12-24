<?php defined('SYS_PATH') OR die('No direct access allowed.');
/**
 * Digg pagination style
 * 
 * @preview  « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next »
 */
?>
<div class="pagination pagination-centered">
<ul>
<?php
	if ($previous_page !== FALSE)
	{
		echo '<li>'.HTML::anchor($page->url($previous_page), __('&laquo;&nbsp;上一页'), array('class' => 'previous', 'data-page' => $previous_page)).'</li>';
	}
	
	if ($total_pages < 13)
	{
		/* « Previous  1 2 3 4 5 6 7 8 9 10 11 12  Next » */
		for ($i = 1; $i <= $total_pages; $i++)
		{ 
			if ($i == $current_page)
			{
				echo '<li class="active"><a data-page="'.$i.'">'.$i.'</a></li>';
			}
			else
			{
				echo '<li>'.HTML::anchor($page->url($i), $i, array('data-page' => $i)).'</li>';
			}
		}
	}
	elseif ($current_page < 9)
	{
		/* « Previous  1 2 3 4 5 6 7 8 9 10 … 25 26  Next » */
		for ($i = 1; $i <= 10; $i++)
		{ 
			if ($i == $current_page)
			{
				echo '<li class="active"><a data-page="'.$i.'">'.$i.'</a></li>';
			}
			else
			{
				echo '<li>'.HTML::anchor($page->url($i), $i, array('data-page' => $i)).'</li>';
			}
		}
		echo '<li class="disabled"><span>&hellip;</span></li>';
		echo '<li>'.HTML::anchor($page->url($last_page - 1), ($last_page - 1), array('data-page' => ($last_page - 1))).'</li>';
		echo '<li>'.HTML::anchor($page->url($last_page), $last_page, array('data-page' => $last_page)).'</li>';
	}
	elseif ($current_page > $total_pages - 8)
	{
		/* « Previous  1 2 … 17 18 19 20 21 22 23 24 25 26  Next » */
		echo '<li>'.HTML::anchor($page->url(1), 1, array('data-page' => 1)).'</li>';
		echo '<li>'.HTML::anchor($page->url(2), 2, array('data-page' => 2)).'</li>';
		echo '<li class="disabled"><span>&hellip;</span></li>';
		for ($i = $total_pages - 9; $i <= $total_pages; $i++)
		{ 
			if ($i == $current_page)
			{
				echo '<li class="active"><a data-page="'.$i.'">'.$i.'</a></li>';
			}
			else
			{
				echo '<li>'.HTML::anchor($page->url($i), $i, array('data-page' => $i)).'</li>';
			}
		}
	}
	else
	{
		/* « Previous  1 2 … 5 6 7 8 9 10 11 12 13 14 … 25 26  Next » */
		echo '<li>'.HTML::anchor($page->url(1), 1, array('data-page' => 1)).'</li>';
		echo '<li>'.HTML::anchor($page->url(2), 2, array('data-page' => 2)).'</li>';
		echo '<li class="disabled"><span>&hellip;</span></li>';
		for ($i = $current_page - 5; $i <= $current_page + 5; $i++)
		{ 
			if ($i == $current_page)
			{
				echo '<li class="active"><a data-page="'.$i.'">'.$i.'</a></li>';
			}
			else
			{
				echo '<li>'.HTML::anchor($page->url($i), $i, array('data-page' => $i)).'</li>';
			}
		}
		echo '<li class="disabled"><span>&hellip;</span></li>';
		echo '<li>'.HTML::anchor($page->url($last_page -1 ), ($last_page - 1), array('data-page' => $last_page - 1)).'</li>';
		echo '<li>'.HTML::anchor($page->url($last_page), $last_page, array('data-page' => $last_page)).'</li>';
	}
	
	if ($next_page !== FALSE)
	{
		echo '<li>'.HTML::anchor($page->url($next_page), __('下一页&nbsp;&raquo;'), array('class' => 'next', 'data-page' => $next_page)).'</li>';
	}
?>
</ul>
</div>
