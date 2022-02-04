<?php $format = substr($element->{$index}->mimetype, strrpos($element->{$index}->mimetype, '/', -1)); ?>

<div id="ophco-image-container-<?= $element->{$index}->id;?>" class="ophco-image-container"
     data-image-el=".image-upload-del" data-file-type="image" data-file-format="<?= $format === 'png' ? 'png' : 'jpeg'?>">
    <img id="<?=$side;?>-image-<?=$element->{$index}->id;?>" class="image-upload-del" style="width:100%;"
         src="/file/view/<?= $element->{$index}->id;?>/image<?= strrchr($element->{$index}->name, '.');?>?rotate=<?= $element->{$index}->rotate?>" border="0">
</div>