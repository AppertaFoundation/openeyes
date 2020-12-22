<meta charset="utf-8" />
<?php
$assetManager = Yii::app()->getAssetManager();
$newblue_path = $assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true);
$favicon_path = $newblue_path . '/img/favicon_package_OE';

//Because the wonderful way the namespace is created means if you don't include your file in the assets template
//the namespace doesn't exist and gets overwritten.
?>
<script type="text/javascript">var OpenEyes = OpenEyes || {};</script>
<title><?=\CHtml::encode($this->pageTitle) ?></title>
<?php if (isset($whiteboard) && $whiteboard) : ?>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
<?php else : ?>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=0.5">
<?php endif; ?>
<meta name="format-detection" content="telephone=no">

<?php if (Yii::app()->params['disable_browser_caching']) {?>
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="pragma" content="no-cache" />
<?php }?>

<?php header("Content-Security-Policy: default-src 'self' localhost:*; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src data: https://*/Analytics http://*/Analytics 'self'", true); ?>

<link rel="apple-touch-icon" sizes="180x180" href="<?= $favicon_path ?>/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?= $favicon_path ?>/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= $favicon_path ?>/favicon-16x16.png">
<link rel="manifest" href="<?= $favicon_path ?>/site.webmanifest">
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="theme-color" content="#ffffff">

<script type="text/javascript">
    var baseUrl = '<?php echo rtrim(Yii::app()->createURL('site/index'), '\\/')?>';
</script>
