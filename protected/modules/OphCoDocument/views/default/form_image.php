<div id="ophco-image-container-<?php echo $element->{$index}->id;?>" class="ophco-image-container">
    <img src="/file/view/<?php echo $element->{$index}->id;?>/image<?php echo strrchr($element->{$index}->name, '.');?>" border="0">
    <span title="Delete" onclick="deleteOPHCOImage(<?php echo $element->{$index}->id; ?>, '<?php echo $index."_id";?>' );" class="image-del-icon">X</span>
</div>