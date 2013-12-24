<?php
// 生成一个授权页面
$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );
$params = array();
$params['client_id'] = WB_AKEY;
$params['redirect_uri'] = WB_CALLBACK_URL;
$params['response_type'] = 'code';
$params['state'] = NULL;
$params['display'] = NULL;
$params['forcelogin'] = 'true';
$code_url = $o->authorizeURL();
$code_url = $code_url . "?" . http_build_query($params);
?>
<div class="span3">
	<h3><?php echo __('Weibo Client Info') ?></h3>
	<hr />
	<p><?php echo __('Client UID: :val', array(':val' => Weibo::client('uid')->val)) ?></p>
	<?php if (Weibo::client('token')->val): ?>
		<p><?php echo __('Client Register: :val', array(':val' => date('Y-m-d H:i:s', Weibo::client('created')->val))) ?></p>
		<p><?php echo __('Client Expired: :val', array(':val' => date('Y-m-d H:i:s', Weibo::client('created')->val + Weibo::client('expired')->val))) ?></p>
	<?php else: ?>
		<p><strong style="color:red;"><?php echo __('Client has not token now.') ?></strong></p>
	<?php endif; ?>
	<p><a href="<?php echo $code_url ?>" target="_blank" class="btn btn-block btn-large btn-info"><?php echo __('Re-Authentication') ?></a></p>
</div>
