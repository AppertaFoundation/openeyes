		</div><!-- #content -->
		<div id="help" class="clearfix">
			<?php /*
			<div class="hint">
				<p><strong>Online Help</strong></p>
				<p><a href="#">Quick Reference Guide</a></p>
				<p>&nbsp;</p>
				<p><strong>Helpdesk</strong></p>
				<p>Telephone: <?php echo Yii::app()->params['helpdesk_phone']?></p>
				<p>Email: <a href="mailto:<?php echo Yii::app()->params['helpdesk_email']?>"><?php echo Yii::app()->params['helpdesk_email']?></a></p>
			</div>
			*/?>
		</div>
	</div><!--#container -->

	<?php echo $this->renderPartial('//base/_footer',array())?>

	<script defer type="text/javascript" src="/js/plugins.js"></script>
	<script defer type="text/javascript" src="/js/script.js"></script>

	<script type="text/javascript">
		$('select[id=selected_firm_id]').die('change').live('change', function() {
			var firmId = $('select[id=selected_firm_id]').val();
			$.ajax({
				type: 'post',
				url: '<?php echo Yii::app()->createUrl('site'); ?>',
				data: {'selected_firm_id': firmId },
				success: function(data) {
					console.log(data);
					window.location.href = '<?php echo Yii::app()->createUrl('site'); ?>';
				}
			});
		});
	</script>

	<?php if (Yii::app()->user->checkAccess('admin')) {?>
		<div class="h1-watermark-admin"><?php echo Yii::app()->params['watermark_admin']?></div>
	<?php } else if (Yii::app()->params['watermark']) {?>
		<div class="h1-watermark"><?php echo Yii::app()->params['watermark']?></div>
	<?php }?>
</body>
</html>
