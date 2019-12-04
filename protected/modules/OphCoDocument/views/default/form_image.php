<div id="ophco-image-container-<?php echo $element->{$index}->id;?>" class="ophco-image-container">
    <img style="width:100%;"
src="/file/view/<?= $element->{$index}->id;?>
/image<?= strrchr($element->{$index}->name, '.');?>
?rotate=<?= $element->{$index}->rotate?>" border="0">
</div>