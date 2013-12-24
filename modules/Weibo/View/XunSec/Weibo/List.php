<?php
//mid转换为url地址后缀  
function midToStr($mid)  
{  
    settype($mid,'string');  
    $mid_length=strlen($mid);  
    $url='';  
    $str=strrev($mid);  
    $str=str_split($str,7);  
      
    foreach ($str AS $v)  
    {  
        $url.=intTo62(strrev($v));  
    }  

    $url_str=strrev($url);

    return $url_str;
}

function str62keys($key)//62进制字典
{
	$key = abs($key);
    $str62keys = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z","A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
    return $str62keys[$key];
}

/* url 10 进制 转62进制*/
function intTo62($int10)
{
     $s62 = '';
     $r = 0;
    while ($int10 != 0)
    {
        $r = $int10 % 62;
        $s62.= str62keys($r);
        $int10 = floor($int10 / 62);
    }
    return $s62;
}
?>
<div class="row-fluid">
	<div class="span9">
		<h2><?php echo __('Weibo List') ?></h2>
		<hr />
		<table class="table">
			<thead>
				<tr>
					<th width="80"><?php echo __('Poster') ?></th>
					<th><?php echo __('Content') ?></th>
					<th width="80"><?php echo __('Actions') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($feeds AS $feed): ?>
				<tr<?php if ( ! $feed->mid) { echo ' class="warning"'; } ?>>
					<td><a href="http://weibo.com/<?php echo $feed->user['profile_url'] ?>" rel="popover" href="#" data-img="<?php echo $feed->user['avatar_large'] ?>" target="_blank"><?php echo $feed->user['screen_name'] ?></a></td>
					<td>
						<?php echo $feed->text ?>
						<?php if ($feed->img_id): ?>
						<img src="<?php echo Media::url($feed->img->filepath) ?>" />
						<?php endif; ?>
					</td>
					<td>
					<?php if ($feed->mid): ?>
						<a class="btn btn-mini btn-info" href="http://weibo.com/<?php echo $feed->user['profile_url'] ?>/<?php echo intTo62($feed->mid) ?>" target="_blank"><?php echo __('View') ?></a>
					<?php else: ?>
						<button class="btn btn-mini btn-warning" disabled><?php echo __('View') ?></button>
					<?php endif; ?>
						<a href="#" data-url="<?php echo Route::url('xunsec-admin', array('controller' => 'Weibo', 'action' => 'delweibo')) ?>" class="btn btn-mini btn-danger delete-weibo-btn"><?php echo __('Delete') ?></a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $pagination ?>
	</div>
	<?php include Kohana::find_file('View', 'XunSec.Weibo.Sidebar') ?>
</div>
<script type="text/javascript" src="<?php echo Media::url('bootbox/bootbox.min.js') ?>"></script>
<script>
$('a[rel=popover]').popover({
	html: true,
	trigger: 'hover',
	content: function () {
		return '<img src="'+$(this).data('img') + '" />';
	}
});
$('.delete-weibo-btn').click(function(){
	var url = $(this).data('url');
	bootbox.confirm("<?php echo __('Are you sure to delete this weibo (will delete the same weibo from weibo.com if exists) ?') ?>", function(result) {                
		if (result === null) {
			//alert('no days');
		} else {
			$.post(url, {day: result}, function(){
				window.location.href = '?r='+Math.random();
			});
		}
	});
	return false;
});
</script>
