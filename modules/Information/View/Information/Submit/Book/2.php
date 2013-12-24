<div id="step2" class="clearfix step" style="display:none;">
	<div class="info-data">
		<div class="control-group">
			<label class="control-label" for="category_id">分类</label>
			<div class="controls">
				<select id="category_id" name="category_id">
				<?php foreach (Model::factory('Information.Category')->find_all() AS $category): ?>
					<option value="<?php echo $category->id ?>"<?php echo (isset($post['category_id']) AND $post['category_id'] == $category->id) ? ' selected' : '' ?>><?php echo $category->name ?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="book_name">书名</label>
			<div class="controls">
				<input type="text" id="book_name" name="book_name" class="input-xlarge" required="required" placeholder="尽量填写完整书名" value="<?php echo isset($post['book_name']) ? $post['book_name'] : '' ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="quality">新旧程度</label>
			<div class="controls">
				<div id="quality-selector" class="btn-group" data-toggle="buttons-radio">
					<button type="button" class="btn" data-quality="1">基本全新</button>
					<button type="button" class="btn active" data-quality="0">正常痕迹</button>
					<button type="button" class="btn" data-quality="-1">略有破损</button>
				</div>
				<input type="hidden" name="quality" id="quality" value="0" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="book_author">作者</label>
			<div class="controls">
				<input type="text" id="book_author" name="book_author" required="required" placeholder="原作者/译者" value="<?php echo isset($post['book_author']) ? $post['book_author'] : '' ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="publisher">出版社</label>
			<div class="controls">
				<input type="text" id="publisher" name="publisher" required="required" placeholder="出版社名称" value="<?php echo isset($post['publisher']) ? $post['publisher'] : '' ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="raw_price">原价</label>
			<div class="controls">
				<input type="text" id="raw_price" name="raw_price" required="required" placeholder="0.00" value="<?php echo isset($post['raw_price']) ? $post['raw_price'] : '' ?>" />
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="description">描述</label>
			<div class="controls">
				<textarea id="description" name="description" class="input-xlarge" placeholder="填写书籍的描述和你认为有必要添加的说明，200字左右，超出部分会被截断。" required="required"><?php echo isset($post['description']) ? $post['description'] : '' ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="image">图片</label>
			<div class="controls">
				<input type="file" name="image" id="image-upload" style="display:none;" />
				<a id="image" href="#" class="btn btn-info" rel="popover" data-src="">选择预览图片</a>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<a href="#" class="btn back-to-step1">上一步</a>
				<a href="#" class="btn btn-success go-to-step3">下一步</a>
			</div>
		</div>
	</div>
</div>
