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

	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/plugins.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/script.js'))?>

	<script type="text/javascript">
		$(this).undelegate('select[id=selected_firm_id]','change').delegate('select[id=selected_firm_id]','change',function() {
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
