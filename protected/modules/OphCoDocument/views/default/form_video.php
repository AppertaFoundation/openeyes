<div id="ophco-image-container-<?php echo $element->{$index}->id;?>" class="ophco-image-container">
    <video controls>
        <source src="/file/view/<?php echo $element->{$index}->id;?>/image<?php echo strrchr($element->{$index}->name, '.');?>">
    </video>
    <span title="Delete" onclick="deleteOPHCOImage(<?php echo $element->{$index}->id; ?>, '<?php echo $index."_id";?>' );" class="image-del-icon">X</span>
</div>
