<div class="row-fluid">
	<div class="span12">
		<div class="tabbable tabs-below">
			<div class="tab-content">
				<div class="tab-pane active" id="A">
					<?php include Kohana::find_file('View', 'Information.List.Book') ?>
				</div>
				<div class="tab-pane" id="B">
					  <p>Howdy, I'm in Section B.</p>
					</div>
					<div class="tab-pane" id="C">
					  <p>What up girl, this is Section C.</p>
					</div>
			</div>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#A" data-toggle="tab">我出售的书</a></li>
				<li><a href="#B" data-toggle="tab">我求购的书</a></li>
				<li><a href="#C" data-toggle="tab">评论列表</a></li>
			</ul>
		</div>
	</div>
</div>
