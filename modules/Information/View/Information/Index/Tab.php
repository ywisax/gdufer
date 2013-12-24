<ul id="information-index-main-nav" class="nav nav-tabs">
	<li class="pull-right"><a href="#information-ershou" data-toggle="tab">二手教材</a></li>
	<li class="pull-right"><a href="#information-kaoyan" data-toggle="tab">考研资料</a></li>
	<li class="pull-right"><a href="#information-free" data-toggle="tab">免费赠书</a></li>
	<li class="pull-right"><a href="#information-hot" data-toggle="tab">热门出售</a></li>
	<li class="pull-right active"><a href="#information-newest" data-toggle="tab">最新出售</a></li>
</ul>
<div id="information-index-main-nav-content" class="tab-content">
	<?php include Kohana::find_file('View', 'Information.Index.Tab.Newest') ?>
	<?php include Kohana::find_file('View', 'Information.Index.Tab.Hot') ?>
	<?php include Kohana::find_file('View', 'Information.Index.Tab.Ershou') ?>
	<?php include Kohana::find_file('View', 'Information.Index.Tab.Kaoyan') ?>
	<?php include Kohana::find_file('View', 'Information.Index.Tab.Free') ?>
</div>
