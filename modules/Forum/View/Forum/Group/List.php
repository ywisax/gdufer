<?php foreach ($topics AS $topic): ?>
<h3><a href="<?php echo Route::url('forum-topic', array('id' => $topic->id)) ?>"><?php echo $topic->title ?></a></h3>
<?php endforeach; ?>
