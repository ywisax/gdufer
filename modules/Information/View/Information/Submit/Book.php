<?php XunSec::style('information/css/common.css') ?>
<?php XunSec::script('information/js/index.js') ?>
<div class="row-fluid">
	<div class="span8 offset2">
		<?php if (isset($errors) AND ! empty($errors)): ?>
		<div class="alert">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<ul>
			<?php foreach ($errors AS $error): ?>
				<li><?php echo $error ?></li>
			<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>
		<div id="information-submit-breadcrumb">
			<a class="step1 active" href="#">选择物品类型</a>
			<a class="step2" href="#">填写物品信息</a>
			<a class="step3" href="#">你想要什么</a>
			<a class="step4" href="#">你的联系方式</a>
			<a class="step5" href="#">发布成功</a>
		</div>
		<hr />
		<form class="form-horizontal" id="information-submit-form" method="post" enctype="multipart/form-data">
			<div id="information-submit-content" class="clearfix">
				<input type="hidden" name="model_type" value="book" />
				<?php include Kohana::find_file('View', 'Information.Submit.Book.1') ?>
				<?php include Kohana::find_file('View', 'Information.Submit.Book.2') ?>
				<?php include Kohana::find_file('View', 'Information.Submit.Book.3') ?>
				<?php include Kohana::find_file('View', 'Information.Submit.Book.4') ?>
			</div>
		</form>
	</div>
</div>
