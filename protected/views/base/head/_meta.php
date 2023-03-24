<meta charset="utf-8" />
<?php
$assetManager = Yii::app()->getAssetManager();
$newblue_path = $assetManager->getPublishedUrl(Yii::getPathOfAlias('application.assets.newblue'), true);

//Because the wonderful way the namespace is created means if you don't include your file in the assets template
//the namespace doesn't exist and gets overwritten.
?>
<script type="text/javascript">var OpenEyes = OpenEyes || {};</script>
<title><?=\CHtml::encode($this->pageTitle) ?></title>

<?php

// Override scaling based ondevice type. currently there is no way to identify the exact device,
// so we can only approximate using the User Agent, which can only tell us if it is an iPhone, iPad, Android, Windows, etc.
// The values below target the standard (i.e, cheapest) current iPad and the iPhone 12 (standard version, not max)
// 0.5 is our current default value, which supports older devices of 600px width (e.g., cheap samsung galaxy tablets)
// For other devices and widths, see: https://www.mydevice.io/#compare-devices.
// To calculate scaling, divide the CSS width by 1200 (which is our minimum supported width for OpenEyes)
$ua = $_SERVER['HTTP_USER_AGENT'];
$initial_scale = '0.5';

if (str_contains($ua, 'iPad')) {
    $initial_scale = '0.675';
} elseif (str_contains($ua, 'iPhone')) {
    $initial_scale = '0.325';
} else {
    $initial_scale = "0.5";
}

?>
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=<?= $initial_scale ?>">

<meta name="format-detection" content="telephone=no">

<?php if (Yii::app()->params['disable_browser_caching']) {?>
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="pragma" content="no-cache" />
<?php }?>

<?php
    $iframe_policy = $this->iframe_policy ?? "frame-src 'self' localhost:* blob: complog:;";
    @header("Content-Security-Policy:default-src 'self' localhost:*;script-src 'self' 'unsafe-inline' 'unsafe-eval';style-src 'self' 'unsafe-inline';{$iframe_policy}img-src data: https://*/Analytics http://*/Analytics 'self';worker-src blob:;font-src 'self' data:", true);
?>


<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest" />
<meta name="msapplication-TileColor" content="#2b5797">
<meta name="theme-color" content="#ffffff">

<script type="text/javascript">
    var baseUrl = '<?php echo rtrim(Yii::app()->createURL('site/index'), '\\/')?>';
</script>
