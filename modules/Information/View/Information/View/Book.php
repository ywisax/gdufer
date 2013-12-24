<?php
XunSec::style('bootstrap-lightbox/bootstrap-lightbox.min.css');
XunSec::style('information/css/common.css');
XunSec::script('bootstrap-lightbox/bootstrap-lightbox.min.js');
?>
<div id="information-book-view-info" class="media">
	<a data-toggle="lightbox" class="media-image pull-left" href="#book-lightbox">
		<img class="media-object" src="<?php echo $model->image_url() ?>" />
	</a>
	<div id="book-lightbox" class="lightbox hide fade"  tabindex="-1" role="dialog" aria-hidden="true">
		<div class="lightbox-content">
			<img src="<?php echo $model->image_url() ?>" />
			<div class="lightbox-caption"><?php echo $model->book_name ?></div>
		</div>
	</div>
	<div class="media-body info-block">
		<div class="row-fluid">
			<div class="span8">
				<h4 class="media-heading">
					<?php echo $model->book_name ?>
					<?php if ($model->poster->id == Auth::user('id')): ?>
					<a href="<?php echo $model->link('delete') ?>" onclick="return confirm('确定要删除该信息了吗？')">[已经出手，关闭该信息]</a>
					<?php endif; ?>
				</h4>
				<hr />
				<div class="row-fluid">
					<div class="span3">作者：<span class="label label-success"><?php echo $model->book_author ?></span></div>
					<div class="span3">原价：<span class="label label-success">￥<?php echo $model->raw_price ?></span></div>
					<div class="span6">出版社：<span class="label label-success"><?php echo $model->publisher ?></span></div>
				</div>
				<hr />
				<div class="row-fluid">
					<div class="span2">
						分类：<span class="label label-success"><?php echo $model->category->name ?></span>
					</div>
					<div class="span4">
						<div id="quality-selector" class="btn-group">
						<?php foreach ($model->quality_selector AS $val => $name): ?>
							<button type="button" class="btn<?php echo $val == $model->quality ? ' active' : '' ?>" data-quality="<?php echo $val ?>"><?php echo $name ?></button>
						<?php endforeach; ?>
						</div>
					</div>
				</div>
				<hr />
				<p class="description">描述：<?php echo $model->description ?></p>
			</div>
			<div id="information-book-view-return" class="span4 text-center">
				<?php
				switch ($model->return_type)
				{
					case 0: // 自定义
					?>
					<div class="well">
						<h4 class="text-left heading">发布者说：</h4>
						<p class="text-left"><?php echo $model->return_text ?></p>
					</div>
					<?php
						break;
					case 1: // 感谢
					?>
					<h4 class="heading"><small>我想要</small> 你的一句感谢</h4>
					<div class="data">
						<img src="<?php echo Media::url('information/img/thanks.png') ?>" />
					</div>
					<?php
						break;
					case 2: // 吃饭
					?>
					<h4 class="heading"><small>我想要</small> 别人陪我聊天</h4>
					<div class="data">
						<img src="<?php echo Media::url('information/img/talk.png') ?>" />
					</div>
					<?php
						break;
					case 3: // 唱歌
					?>
					<h4 class="heading"><small>我想要</small> 听你唱首歌</h4>
					<div class="data">
						<img src="<?php echo Media::url('information/img/music.png') ?>" />
					</div>
					<?php
						break;
					default:
						?>未知错误<?php
				?>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<div id="information-book-view-action" class="row-fluid">
	<div class="span3">
		<a href="#information-book-view-contact-modal" role="button" class="btn btn-primary btn-large btn-block wantit" data-toggle="modal">获取联系方式</a>
		<div id="information-book-view-contact-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="information-book-view-contact-modal-label" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="information-book-view-contact-modal-label">联系方式</h3>
			</div>
			<div class="modal-body">
				<div class="row-fluid">
					<div class="span4">联系人</div>
					<div class="span8"><?php echo $model->realname ?></div>
				</div>
				<div class="row-fluid">
					<div class="span4">短号</div>
					<div class="span8"><?php echo $model->telephone ?></div>
				</div>
				<?php if ($model->remark): ?>
				<div class="row-fluid">
					<div class="span4">备注</div>
					<div class="span8"><?php echo $model->remark ?></div>
				</div>
				<?php endif; ?>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">确定</button>
			</div>
		</div>
		<div class="share-block clearfix">
			<?php include Kohana::find_file('View', 'Information.View.Book.Share') ?>
		</div>
		<hr />
		<!-- 加载更多其他 -->
		<?php include Kohana::find_file('View', 'Information.View.Book.Recommend') ?>
	</div>
	<div class="span9">
		<?php include Kohana::find_file('View', 'Information.View.Book.Comment') ?>
	</div>
</div>
