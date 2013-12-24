<?php defined('SYS_PATH') OR die('No direct script access.') ?>
<?php
$group_stats      = Profiler::group_stats();
$group_cols       = array('min', 'max', 'average', 'total');
$application_cols = array('min', 'max', 'average', 'current');
?>
<script type="text/javascript">
	<?php foreach (Profiler::groups() AS $group => $benchmarks): ?>
	console.groupCollapsed("<?php echo __(ucfirst($group)) ?>");
		console.log("<?php echo __('Time cost :second', array(
			':second' => number_format($group_stats[$group]['total']['time'], 6).'s'
		))?>");
		console.log("<?php echo __('Memory cost :second', array(
			':second' => number_format($group_stats[$group]['total']['memory'] / 1024, 4).'KB'
		))?>");
		
		console.groupCollapsed("<?php echo __('Benchmark') ?>");
		<?php foreach ($benchmarks AS $name => $tokens): ?>
			<?php $stats = Profiler::stats($tokens) ?>
			console.groupCollapsed("<?php echo addslashes($name), ' (', count($tokens), ')' ?>");
			<?php foreach ($group_cols AS $key): ?>
				console.log("<?php echo __(':key : :time , :memory', array(
					':key' => $key,
					':time' => number_format($stats[$key]['time'], 6).'s',
					':memory' => number_format($stats[$key]['memory'] / 1024, 4).'KB'
				)) ?>");
			<?php endforeach; ?>
			console.groupEnd();
		<?php endforeach ?>
		console.groupEnd();
	console.groupEnd();
	<?php endforeach ?>

	<?php $stats = Profiler::application() ?>
	console.groupCollapsed("<?php echo __('Application Execution').' ('.$stats['count'].')' ?>");
	<?php foreach ($application_cols AS $key): ?>
		console.log("<?php echo __(':key : :time , :memory', array(
			':key' => $key,
			':time' => number_format($stats[$key]['time'], 6).'s',
			':memory' => number_format($stats[$key]['memory'] / 1024, 4).'KB'
		)) ?>");
	<?php endforeach; ?>
	console.groupEnd();

	<?php foreach (array('_SERVER', '_GET', '_POST', '_COOKIE', '_SESSION', '_FILES') AS $var): ?>
		<?php if (empty($GLOBALS[$var]) OR ! is_array($GLOBALS[$var])) continue ?>
		console.groupCollapsed("<?php echo __('$:var Variables', array(':var' => $var)) ?>");
		<?php foreach ($GLOBALS[$var] AS $key => $value): ?>
			console.log("<?php echo __(':key : :val', array(
				':key' => UTF8::clean($key),
				':val' => is_array($value) ? print_r($value, TRUE) : addslashes(trim($value)),
			)) ?>");
		<?php endforeach ?>
		console.groupEnd();
	<?php endforeach; ?>

	<?php $included = get_included_files() ?>
	console.groupCollapsed("<?php echo __('Included files') ?> (<?php echo count($included) ?>)");
	<?php foreach ($included AS $file): ?>
		console.log("<?php echo addslashes(Debug::path($file)) ?>");
	<?php endforeach; ?>
	console.groupEnd();

	<?php $extensions = get_loaded_extensions() ?>
	console.groupCollapsed("<?php echo __('Loaded extensions') ?> (<?php echo count($included) ?>)");
	<?php foreach ($extensions AS $extension): ?>
		console.log("<?php echo $extension ?>");
	<?php endforeach; ?>
	console.groupEnd();
</script>
