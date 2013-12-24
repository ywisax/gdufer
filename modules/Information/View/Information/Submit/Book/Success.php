<hr />
<div class="row-fluid">
	<div class="offset2 span2">
		<img src="<?php echo Media::url('img/ok-apply.png') ?>" />
	</div>
	<div class="span6">
		<h2>提交成功</h2>
		<hr />
		<p>
			你可以进入 <a href="<?php echo $model->link() ?>">这里</a> 查看你发布的信息
		</p>
		<p>
			如果你的物品已经顺利易手，请及时结贴。
		</p>
		<p><a href="<?php echo isset($redirect) ? $redirect : URL::base() ?>">继续访问广金在线</a></p>
	</div>
</div>
<hr />
<div class="text-center">
	<h3>Share what you like, search what you want.</h3>
</div>

