<div id="ophco-image-container-'+sideID+'" class="ophco-image-container">
    <object width="100%" height="500px" data="/file/view/<?php echo $element->{$index}->id ?>/image<?php echo strrchr ($element->{$index}->name, '.') ?>" type="application/pdf">
        <embed src="/file/view/<?php echo $element->{$index}->id ?>/image<?php echo strrchr ($element->{$index}->name, '.') ?>" type="application/pdf" />
    </object>
    <span title="Delete" onclick="deleteOPHCOImage(<?php echo $element->{$index}->id; ?>, '<?php echo $index."_id"; ?>' );" class="image-del-icon">X</span>
</div>