<?php $filePath = "/file/view/" .  $element->{$index}->id . "/image" . strrchr($element->{$index}->name, '.'); ?>

<iframe class="pdf-js-viewer" src="<?= Yii::app()->assetManager->createUrl('components/pdfjs/web/viewer.html?file=' . $filePath)?>" title="webviewer" width="100%"
        style="height:calc(100vh - 200px);"></iframe>
