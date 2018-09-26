<div id="ophco-image-container-<?php echo $element->{$index}->id;?>" class="ophco-image-container">
    <video controls>
        <source src="/file/view/<?php echo $element->{$index}->id;?>/image<?php echo strrchr($element->{$index}->name, '.');?>">
    </video>
</div>
