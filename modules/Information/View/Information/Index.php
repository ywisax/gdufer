<?php XunSec::style('information/css/common.css') ?>
<?php XunSec::script('information/js/index.js') ?>
<?php include Kohana::find_file('View', 'Information.Banner') ?>
<?php include Kohana::find_file('View', 'Information.Ad') ?>
<?php
/*
<hr />
<div id="information-index-main" class="row-fluid">
	<div class="span9">
		<?php include Kohana::find_file('View', 'Information.Index.Tab') ?>
	</div>
	<div class="span3">
		<?php include Kohana::find_file('View', 'Information.Index.SuccessLog') ?>
	</div>
</div>
<hr class="no-margin-top" />
*/
?>
<hr />
<div class="row-fluid">
	<div class="span3">
		<img src="<?php echo Media::url('information/img/gdufer-flow-chart.png') ?>" />
	</div>
	<div class="span5">
		<?php include Kohana::find_file('View', 'Information.Index.Sell') ?>
	</div>
	<div class="span4">
		<?php include Kohana::find_file('View', 'Information.Index.Buy') ?>
	</div>
</div>
