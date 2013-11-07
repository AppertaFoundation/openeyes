<meta charset="utf-8" />

<title>NEW THEME - <?php echo CHtml::encode($this->pageTitle); ?></title>
<meta name="viewport" content="width=device-width" />

<?php if (Yii::app()->params['disable_browser_caching']) {?>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
<?php }?>

<link rel="icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>" type="image/x-icon" />
<link rel="shortcut icon" href="<?php echo Yii::app()->createUrl('favicon.ico')?>"/>

<script type="text/javascript">
	var baseUrl = '<?php echo Yii::app()->baseUrl?>';
</script>