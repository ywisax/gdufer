<div class="tab-pane" id="information-ershou">
	<?php
	$books = Model::factory('Information.Book')
		//->where() 都算是二手
		->order_by('id', 'DESC')
		->limit(12)
		->find_all();
	?>
	<div class="row-fluid">
		<ul class="thumbnails">
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
		</ul>
	</div>
	<div class="row-fluid">
		<ul class="thumbnails">
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
			<li class="span2">
				<div class="thumbnail">
					<a title="书名" href="<?php echo Route::url('information-action', array('action' => 'view', 'id' => 1)) ?>">
						<img src="<?php echo Media::url('information/img/5.jpg') ?>" />
					</a>
					<p class="price"><s>￥55</s>&nbsp;<strong><span class="label label-important">￥10</span></strong></p>
				</div>
			</li>
		</ul>
	</div>
</div>
