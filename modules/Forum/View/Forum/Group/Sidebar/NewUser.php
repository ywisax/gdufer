<div class="panel panel-success">
	<div class="panel-heading"><?php echo __('Newest User') ?></div>
	<div class="panel-body">
		<div id="new_members">
			<ul class="inline clearfix">
			<?php
			$users = Model_User::newest_user(12);
			foreach ($users AS $user)
			{
			?>
				<li><a href="<?php echo Route::url('auth-action', array('action' => 'view', 'id' => $user->id)) ?>" class="avatar tooltip" data-original-title="<?php echo $user->username ?>" data-toggle="tooltip" data-placement="top"><img src="<?php echo $user->avatar_img() ?>" width="54" height="54"></a></li>
			<?php
			}
			?>
			</ul>
		</div>
	</div>
</div>
