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
                        
                        if(strrchr($element->single_document->name, '.') == '.pdf'){
                            ?>
                            <object width="100%" height="500px" data="/file/view/<?php echo $element->single_document_id ?>/image<?php echo strrchr ($element->single_document->name, '.') ?>" type="application/pdf">
                                <embed src="/file/view/<?php echo $element->single_document_id ?>/image<?php echo strrchr ($element->single_document->name, '.') ?>" type="application/pdf" />
                            </object>
                            <?php
                        } else {
                            ?>
                               <img src="/file/view/<?php echo $element->single_document_id?>/image<?php echo strrchr ($element->single_document->name, '.') ?>" border="0">
                            <?php
                        }
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
            if(strrchr($element->right_document->name, '.') == '.pdf'){
                ?>
                <object width="100%" height="500px" data="/file/view/<?php echo $element->right_document_id ?>/image<?php echo strrchr ($element->right_document->name, '.') ?>" type="application/pdf">
                    <embed src="/file/view/<?php echo $element->right_document_id ?>/image<?php echo strrchr ($element->right_document->name, '.') ?>" type="application/pdf" />
                </object>
                <?php
            } else {
                ?>
                   <img src="/file/view/<?php echo $element->right_document_id?>/image<?php echo strrchr($element->right_document->name, '.')?>" border="0">
                <?php
            }
            ?>
            
        </div>
        <?php } ?>
        
        <?php if($element->left_document_id) {?>
        <div id="left-eye">
            <?php
            if(strrchr($element->left_document->name, '.') == '.pdf'){
                ?>
                <object width="100%" height="500px" data="/file/view/<?php echo $element->left_document_id ?>/image<?php echo strrchr ($element->left_document->name, '.') ?>" type="application/pdf">
                    <embed src="/file/view/<?php echo $element->left_document_id ?>/image<?php echo strrchr ($element->left_document->name, '.') ?>" type="application/pdf" />
                </object>
                <?php
            } else {
                ?>
                    <img src="/file/view/<?php echo $element->left_document_id?>/image<?php echo strrchr($element->left_document->name, '.')?>" border="0">
                <?php
            }
            ?>
                             
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>