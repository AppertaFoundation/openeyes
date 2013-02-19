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

</body>
</html>
