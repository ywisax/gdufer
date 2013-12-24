<?php
XunSec::style('gduf/css/schedule.css');
XunSec::style('gduf/css/schedule-responsive.css');
XunSec::script('gduf/js/schedule.js');
?>
<div class="row-fluid">
	<div class="span12">
		<strong><?php echo $user->realname ?></strong>
		<a id="gduf-schedule-refresh" href="#" data-callback="<?php echo Route::url('gduf-jwc-action', array('action' => 'fetch')) ?>" class="pull-right">更新课程表</a>
	</div>
</div>
<div id="gduf-schedule">
	<?php //echo $user->schedule_array() ?>
	<?php echo $user->schedule ?>
</div>
