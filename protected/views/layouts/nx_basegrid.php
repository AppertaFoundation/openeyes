<!DOCTYPE html>
<html lang="en" class="theme-<?= \SettingMetadata::model()->getSetting('display_theme'); ?>">

<head>
    <?php $this->renderPartial('//base/head/_meta'); ?>
    <?php $this->renderPartial('//base/head/_assets'); ?>
    <?php $this->renderPartial('//base/head/_tracking'); ?>
</head>

<?php
    $training_mode = SettingMetadata::checkSetting('training_mode_enabled', 'on') ? 'training-mode' : '';
?>
<body class="open-eyes oe-grid <?=$training_mode?>">

    <?php (YII_DEBUG) ? $this->renderPartial('//base/_debug') : null; ?>

    <?php $this->renderPartial('//base/gui/_minimum_width_warning'); ?>

    <?php $this->renderPartial('//base/_brand'); ?>

    <?php $this->renderPartial('//base/gui/_restrict_print'); ?>

    <?php $this->renderPartial('//base/_header'); ?>

    <?php echo $content; ?>

    <?php $this->renderPartial('//base/_footer'); ?>
</body>

</html>
