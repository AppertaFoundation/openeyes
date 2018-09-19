
<div class="upload-box">
    <label for="Document_<?php echo $index; ?>_id" id="upload_box" class="upload-label" ondrop="drop(event)" ondragover="allowDrop(event)">
        <i class="oe-i download no-click large"></i>
        <br> Click to select file or DROP here</label>
    <input autocomplete="off" type="file" name="Document[<?php echo $index; ?>_id]"
           id="Document_<?php echo $index; ?>_id" style="display:none;"></div>