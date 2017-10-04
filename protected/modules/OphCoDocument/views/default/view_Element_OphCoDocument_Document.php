<div class="element">

    <div class="element-data">
        <div class="row data-row">
            <div class="large-12 column end"><div class="data-value "><b><?php if($element->sub_type) echo $element->sub_type->name; ?></b></div></div>
        </div>
        <div class="row data-row">
            
            <?php 
            if($element->single_document) {?>
                <div class="large-12 column">
                    <?php
                        $this->renderPartial('view_'.$this->getTemplateForMimeType($element->single_document->mimetype), array('element'=>$element, 'index'=>'single_document'));
                    ?>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <?php if(($element->right_document_id) || ($element->left_document_id)) {?>
    <div id="ophco-document-viewer">
        <ul class="tabs event-actions">
            <?php if($element->right_document_id) {?>
            <li><a href="#right-eye">Right eye</a></li>
            <?php } ?>
            
            <?php if($element->left_document_id) {?>
            <li><a href="#left-eye">Left eye</a></li>
            <?php } ?>
        </ul>

        <?php if($element->right_document_id) {
            ?>
        <div id="right-eye">
            <?php
                $this->renderPartial('view_'.$this->getTemplateForMimeType($element->right_document->mimetype), array('element'=>$element, 'index'=>'right_document'));
            ?>
        </div>
        <?php } ?>
        
        <?php if($element->left_document_id) {?>
        <div id="left-eye">
            <?php
                $this->renderPartial('view_'.$this->getTemplateForMimeType($element->left_document->mimetype), array('element'=>$element, 'index'=>'left_document'));
            ?>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>