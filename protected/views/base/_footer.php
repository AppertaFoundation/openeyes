	<div id="footer">
		<h6>&copy; Copyright OpenEyes Foundation 2011 &nbsp;&nbsp;|<!--&nbsp;&nbsp; Terms of Use &nbsp;&nbsp;|&nbsp;&nbsp; Legals &nbsp;&nbsp;|-->&nbsp;&nbsp; <a href="#" id="support-info-link">served, with love, by <?php echo trim(`hostname`)?></a></h6>
		<div id="footer-contacts">
			<?php if (Yii::app()->params['helpdesk_email']) {?><span class="footer-helpdesk">email: <a href="mailto:<?php echo Yii::app()->params['helpdesk_email']?>"><?php echo Yii::app()->params['helpdesk_email']?></a> &nbsp; <?php }; if (Yii::app()->params['helpdesk_phone']) {?>phone: </span><span class="footer-helpdesk-phone"><?php echo Yii::app()->params['helpdesk_phone']?><?php }?></span>
		</div>
	</div> <!-- #footer -->

<script type="text/javascript">
$('#support-info-link').unbind('click').click(function() {
	$.fancybox({
		'padding'				: 0,
		'href'					: '/site/debuginfo',
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic'
	});
});
</script>
