<?php
XunSec::style('gduf/css/mail.css');
XunSec::style('gduf/css/mail-responsive.css');
XunSec::script('bootbox/bootbox.min.js');
XunSec::script('gduf/js/mail.js');
?>
<script>
	var CURRENT_JSESSIONID = '<?php echo $JSESSIONID ?>';
</script>
<?php include Kohana::find_file('View', 'Gduf.Mail.Login') ?>
<?php include Kohana::find_file('View', 'Gduf.Mail.Body') ?>
<?php include Kohana::find_file('View', 'Gduf.Mail.Read') ?>
<?php include Kohana::find_file('View', 'Gduf.Mail.Send') ?>

<div id="dump">
</div>
