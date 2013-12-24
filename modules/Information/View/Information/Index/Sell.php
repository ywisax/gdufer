<div id="information-index-sell">
	<h3 class="header">二手图书</h3>
	<ul class="unstyled">
	<?php
	$books = Model::factory('Information.Book')
		->order_by('id', 'DESC')
		->limit(12)
		->find_all();
	foreach ($books AS $book)
	{
	?>
		<li>
			<a href="<?php echo $book->link() ?>" title="<?php echo $book->book_name ?>">
				<?php echo $book->book_name ?>
			</a>
			<span class="pull-right">
				原价￥<?php echo $book->raw_price ?>
				/
				<?php echo $book->book_author ?>
				/
				<?php echo $book->publisher ?>
			</span>
		</li>
	<?php
	}
	?>
	</ul>
</div>
