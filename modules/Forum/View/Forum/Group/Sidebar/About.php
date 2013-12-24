<p>广金在线，是个针对广金及周边院校的虚拟大学社区，为学子们提供资源共享平台和校园网络解决方案。</p>
<p>如果你对本站由任何建议，均可在站内发帖或联系管理员admin@gdufer.com来解决。我们欢迎您提出任何方面的建议。</p>
<p>在使用过程中，如果你遇到问题或发现BUG，请给我们<a href="/contact.html" target="_blank">留言反馈</a>。</p>

<table class="table table-bordered">
	<tbody>
		<tr>
			<th>统计</th>
			<th>共计</th>
		</tr>
		<tr>
			<td><?php echo __('Group count') ?></td>
			<td><?php echo Model::factory('Forum.Group')->find_all()->count() ?></td>
		</tr>
		<tr>
			<td><?php echo __('Topic count') ?></td>
			<td><?php echo Model::factory('Forum.Topic')->find_all()->count() + Model::factory('Forum.Reply')->find_all()->count() ?></td>
		</tr>
		<tr>
			<td><?php echo __('User count') ?></td>
			<td><?php echo Model::factory('User')->find_all()->count() ?></td>
		</tr>
	</tbody>
</table>
