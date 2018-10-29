<meta charset="utf-8" />
<?php
$assetManager = Yii::app()->getAssetManager();
$newblue_path = $assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'));
$favicon_path = $newblue_path . '/img/favicon_package_OE';

//Because the wonderful way the namespace is created means if you don't include your file in the assets template
//the namespace doesn't exist and gets overwritten.
?>
<script type="text/javascript">var OpenEyes = OpenEyes || {};</script>
<title><?=\CHtml::encode($this->pageTitle); ?></title>
<meta name="viewport" content="width=1230, initial-scale=1" />
<meta name="format-detection" content="telephone=no">

<?php if (Yii::app()->params['disable_browser_caching']) {?>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
<?php }?>

<link rel="apple-touch-icon" sizes="180x180" href="<?= $favicon_path ?>/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?= $favicon_path ?>/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= $favicon_path ?>/favicon-16x16.png">
<link rel="manifest" href="<?= $favicon_path ?>/site.webmanifest">
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="theme-color" content="#ffffff">

<script type="text/javascript">
	var baseUrl = '<?php echo Yii::app()->baseUrl?>';
</script>
