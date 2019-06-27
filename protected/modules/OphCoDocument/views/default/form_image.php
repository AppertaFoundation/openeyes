<div id="ophco-image-container-<?php echo $element->{$index}->id;?>" class="ophco-image-container">
    <img style="width:100%; <?=!empty($element->{$index}->rotate) ? 'transform: rotate('.$element->{$index}->rotate.'deg)' : ''?>" src="/file/view/<?php echo $element->{$index}->id;?>/image<?php echo strrchr($element->{$index}->name, '.');?>" border="0">
</div>